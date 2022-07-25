<?php

namespace Modules\Authentication\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;
use App\Models\Users_model;
use App\Models\Assigned_role_model;
class Authentication extends RestController
{
   // use ResponseTrait;
    public function __construct()
    {
        //helper('common_functions');
        $this->usersModel = new Users_model();
        $this->assignedRoleModel = new Assigned_role_model();
    }
    public function login_mobile_no()
    {
        try {

            $rules = [
                'mobile_no' => [
                    'label'  => 'mobile_no',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Mobile number is required'
                    ]
                ],
            ];

            $input = getRequestInput($this->request);
            //echo "<pre>"; print_r($input); die;

            if (!$this->validateRequest($input, $rules)){
                $response = [
                    'status' => false,
                    'message' => "",
                    'errors' => $this->validator->getErrors(),
                    'data' => new \stdClass()
                ];
                return $this->respond($response);
            } else {
                $from = array('web','mobile');
                $api_from = (isset($input['from']) && !empty($input['from'])) ? $input['from'] : 'mobile';

                if(!in_array($api_from, $from)){
                    $errors = array(
                        "from" => "From field is not valid"
                    );
                    $response = [
                        'status' => false,
                        'message' => "",
                        'errors' => $errors,
                        'data' => new \stdClass()
                    ];
                    return $this->respond($response);
                }
                $userCheck = '';
                if($api_from == $from[0]){
                    $allow_roles = [1];
                    $userCheck = $this->usersModel
                                ->select('users.*')
                                ->join('assigned_role as ar', "ar.userId = users.id")
                                ->where("users.mobileNo", $input['mobile_no'])
                                ->where("users.isActive", 1)
                                ->whereIn('ar.roleId', $allow_roles)
                                ->countAllResults();

                }elseif($api_from == $from[1]){
                    $allow_roles = [2,3];
                    $userCheck = $this->usersModel
                                ->select('users.*')
                                ->join('assigned_role as ar', "ar.userId = users.id")
                                ->where("users.mobileNo", $input['mobile_no'])
                                ->where("users.isActive", 1)
                                ->whereIn('ar.roleId', $allow_roles)
                                ->countAllResults();
                }

                if($userCheck > 0){

                    $result['is_success'] = true;
                    $response = [
                        'status' => true,
                        'message' => "OTP sent successfully",
                        'errors' => new \stdClass(),
                        'data' => $result
                    ];
                }else{
                    $errors = array(
                        "mobile_no" => "Mobile number Incorrect or Not found"
                    );
                    $response = [
                        'status' => false,
                        'message' => "",
                        'errors' => $errors,
                        'data' => new \stdClass(),
                    ];
                }

                return $this->respond($response);
            }
        }catch (\Exception $e){
           // echo "<pre>"; print_r($e); die;
            $errors = array("Something went wrong");
            $response = [
                'status' => false,
                'message' => "",
                'errors' => $errors,
                'data' => new \stdClass(),
            ];
            return $this->respond($response, 400);
        }

    }
    public function login_otp()
    {
        try {
            $rules = [
                'otp' => [
                    'label'  => 'otp',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'OTP is required'
                    ]
                ],
                'mobile_no' => [
                    'label'  => 'mobile_no',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Mobile number is required'
                    ]
                ],
            ];

            $input = getRequestInput($this->request);
            if (!$this->validateRequest($input, $rules)){
                $response = [
                    'status' => false,
                    'message' => "",
                    'errors' => $this->validator->getErrors(),
                    'data' => new \stdClass()
                ];
                return $this->respond($response);
            } else {
                $userCheckOtp = $this->usersModel
                    ->where('mobileNo', $input['mobile_no'])
                    ->where('otp', $input['otp'])
                    ->first();

                if(isset($userCheckOtp) && !empty($userCheckOtp)){

                    $mobileNo = $userCheckOtp['mobileNo'];

                    $access_token = getSignedJWTForUser($mobileNo);

                    //echo "<pre>"; print_r($access_token); die;
                    $result['access_token'] = $access_token;
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => $result
                    ];
                }else{
                    $errors = array(
                        "otp" => "Incorrect OTP. Please check."
                    );
                    $response = [
                        'status' => false,
                        'message' => "",
                        'errors' => $errors,
                        'data' => new \stdClass(),
                    ];
                }

                return $this->respond($response);
            }

        }catch (\Exception $e){
            echo "<pre>ff"; print_r($e); die;
            $errors = array("Something went wrong");
            $response = [
                'status' => false,
                'message' => "",
                'errors' => $errors,
                'data' => new \stdClass(),
            ];
            return $this->respond($response, 400);
        }
    }
}