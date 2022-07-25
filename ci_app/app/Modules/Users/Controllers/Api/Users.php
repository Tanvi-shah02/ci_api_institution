<?php

namespace Modules\Users\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;
use App\Models\Users_model;
use App\Models\School_model;
use App\Models\Assigned_role_model;
class Users extends RestController
{
    public function __construct()
    {
        $this->usersModel = new Users_model();
        $this->assignedRoleModel = new Assigned_role_model();
        $this->school_model = new School_model();
    }
    public function profile()
    {
        $userProfile = $this->current_user;
        try {
            unset($userProfile['OTP']);
            $response = [
                'status' => true,
                'message' => "",
                'errors' => new \stdClass(),
                'data' => $userProfile
            ];
            return $this->respond($response);
        }catch (\Exception $e){

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
    public function schoolList(){
        try {
            $schoolIds = $this->current_user['schoolIds'];
            $schoolIds = explode(",",$schoolIds);
            $school_list = $this->school_model->whereIn('id', $schoolIds)->findAll();
            $response = [
                'status' => true,
                'message' => "",
                'errors' => new \stdClass(),
                'data' => $school_list
            ];
            return $this->respond($response);
        }catch (\Exception $e){
            //echo "<pre>"; print_R($e); die;
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