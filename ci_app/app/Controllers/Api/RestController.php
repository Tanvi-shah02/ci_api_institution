<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Services;
use App\Models\Users_model;
use App\Models\Assigned_role_model;
class RestController extends ResourceController
{
    use ResponseTrait;

    protected $helpers = ['common_functions','url','cookie', 'text','inflector','jwt'];
    protected $current_user = [];
    protected $userkeysModel;
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->current_user = $this->getLoggedInUserData($request);
    }

    public function validateRequest($input, array $rules, array $messages =[]){
        $this->validator = Services::Validation()->setRules($rules);
        // If you replace the $rules array with the name of the group
        if (is_string($rules)) {
            $validation = config('Validation');

            // If the rule wasn't found in the \Config\Validation, we
            // should throw an exception so the developer can find it.
            if (!isset($validation->$rules)) {
                throw ValidationException::forRuleNotFound($rules);
            }

            // If no error message is defined, use the error message in the Config\Validation file
            if (!$messages) {
                $errorName = $rules . '_errors';
                $messages = $validation->$errorName ?? [];
            }

            $rules = $validation->$rules;
        }
        return $this->validator->setRules($rules, $messages)->run($input);
    }
    public function getLoggedInUserData($request) {
        $this->usersModel = new Users_model();
        $this->assignedRoleModel = new Assigned_role_model();
        $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');

        $userData = [];

        if($authenticationHeader) {
            $authenticationHeaderExpload = explode(' ', $authenticationHeader);

            if ($authenticationHeaderExpload[1] != 'null') {
                $token = $authenticationHeaderExpload[1];
                $token_data = validateJWTFromRequest($token);
                if ($token_data != 'UNAUTHORIZED') {
                    $userMobileNo = $token_data->data->mobileNo;


                    $userData = $this->usersModel
                        ->select('users.*, ifnull(sm.schoolIds,"") as schoolIds')
                        ->join('assigned_role as ar', "ar.userId = users.id")
                        ->join('(select userId, group_concat(schoolId) schoolIds from school_mapping group by userId ) as sm', "sm.userId = users.id")
                        ->where("users.mobileNo", $userMobileNo)
                        ->first();

                    $assigned_roles = $this->assignedRoleModel
                        ->select('assigned_role.roleId, assigned_role.schoolId, rl.title, rl.slug')
                        ->join('roles as rl', "rl.id = assigned_role.roleId")
                        ->where('assigned_role.userId', $userData['id'])
                        ->find();
                    $userData['roles'] = $assigned_roles;
                }
            }
        }
        return $userData;



    }
}
