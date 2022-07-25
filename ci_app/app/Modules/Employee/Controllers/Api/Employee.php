<?php

namespace Modules\Employee\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;
use App\Models\Users_model;
use App\Models\Assigned_role_model;
use App\Models\School_mapping_model;
use App\Modules\Employee\Models\EmployeeDetail_model;

class Employee extends RestController
{
    public function __construct()
    {
        //helper('common_functions');
        $this->usersModel = new Users_model();
        $this->EmployeeDetail = new EmployeeDetail_model();
        $this->assignedRoleModel = new Assigned_role_model();
        $this->schoolMapping = new School_mapping_model();
    }
    public function save()
    {
       try {
            $rules = [
                'empNo' => [
                    'label'  => 'empNo',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Employee number is required'
                    ]
                ],
                'firstName' => [
                    'label'  => 'firstName',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'First name is required'
                    ]
                ],
                'lastName' => [
                    'label'  => 'lastName',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Last name is required'
                    ]
                ],
                'designation' => [
                    'label'  => 'designation',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Designation is required'
                    ]
                ],
                'mobileNo' => [
                    'label'  => 'mobileNo',
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
                if($input['id'] == 0 && $input['schoolId'] == 0){
                    $data = array(
                        'firstName' => $input['firstName'],
                        'lastName'    => $input['lastName'],
                        'mobileNo'    => $input['mobileNo'],
                        'isActive'    => 1,
                        'createdBy'   => $this->current_user['id'],
                    );
//                    if($input['id'] > 0){
//                        $data['id'] = $input['id'];
//                    }
                    $insert_id = $this->usersModel->proccessData($data);
                    if($insert_id > 0){
                        // Add detail to employee detail
                        //if($input['id'] == 0) {
                            $empData = array(
                                'userId' => $insert_id,
                                'empNo' => $input['empNo'],
                                'designation' => $input['designation'],
                                'schoolId' => $this->current_user['schoolIds'],
                            );
                            $empInsertId = $this->EmployeeDetail->save($empData);

                            // Assign role
                            $roleData = array(
                                'userId' => $insert_id,
                                'roleId' => 2, //Teacher
                                'schoolId' => $this->current_user['schoolIds'],
                            );
                            $roleInsertId = $this->assignedRoleModel->save($roleData);

                            // Assign School
                            $schoolData = array(
                                'userId' => $insert_id,
                                'schoolId' => $this->current_user['schoolIds'],
                            );
                            $smInsertId = $this->schoolMapping->save($schoolData);
//                        }else{
//                            $empData = array(
//                                'empNo' => $input['empNo'],
//                                'designation' => $input['designation'],
//                            );
//                            $empInsertId = $this->EmployeeDetail
//                                ->set($empData)
//                                ->where('userId',$input['id'])
//                                ->update();
//
//                        }
                        $response = [
                            'status' => true,
                            'message' => "Employee details are saved.",
                            'errors' => new \stdClass(),
                            'data' => new \stdClass()
                        ];
                        return $this->respond($response);
                    }else{

                        $response = [
                            'status' => false,
                            'message' => "Employee details are not saved.",
                            'errors' => new \stdClass(),
                            'data' => new \stdClass()
                        ];
                        return $this->respond($response);
                    }
                }else{
                    if($input['schoolId'] == $this->current_user['schoolIds']){
                        $data = array(
                            'id' => $input['id'],
                            'firstName' => $input['firstName'],
                            'lastName'    => $input['lastName'],
                            'mobileNo'    => $input['mobileNo'],
                        );
                        $insert_id = $this->usersModel->proccessData($data);
                        if($insert_id > 0){
                            $empData = array(
                                'empNo' => $input['empNo'],
                                'designation' => $input['designation'],
                            );
                            $empInsertId = $this->EmployeeDetail
                                ->set($empData)
                                ->where('userId',$input['id'])
                                ->where('schoolId',$input['schoolId'])
                                ->update();
                            $response = [
                                'status' => true,
                                'message' => "Employee details are saved.",
                                'errors' => new \stdClass(),
                                'data' => new \stdClass()
                            ];
                            return $this->respond($response);
                        }else{
                            $response = [
                                'status' => false,
                                'message' => "Employee details are not saved.",
                                'errors' => new \stdClass(),
                                'data' => new \stdClass()
                            ];
                            return $this->respond($response);
                        }
                    }else {

                        $empData = array(
                            'userId' => $input['id'],
                            'empNo' => $input['empNo'],
                            'designation' => $input['designation'],
                            'schoolId' =>$this->current_user['schoolIds'],
                        );
                        $empInsertId = $this->EmployeeDetail->save($empData);
                        $roleData = array(
                            'userId' => $input['id'],
                            'roleId' => 2, //Teacher
                            'schoolId' => $this->current_user['schoolIds'],
                        );
                        $roleInsertId = $this->assignedRoleModel->save($roleData);

                        // Assign School
                        $schoolData = array(
                            'userId' => $input['id'],
                            'schoolId' => $this->current_user['schoolIds'],
                        );
                        $smInsertId = $this->schoolMapping->save($schoolData);
                        if($empInsertId > 0){
                            $response = [
                                'status' => true,
                                'message' => "Employee details are saved.",
                                'errors' => new \stdClass(),
                                'data' => new \stdClass()
                            ];
                            return $this->respond($response);
                        }else{
                            $response = [
                                'status' => true,
                                'message' => "Employee details are saved.",
                                'errors' => new \stdClass(),
                                'data' => new \stdClass()
                            ];
                            return $this->respond($response);
                        }
                    }
                }
            }
        }catch (\Exception $e){
           //echo "<pre>"; print_r($e); die;
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
    public function getList()
    {
        try{
            $input = getRequestInput($this->request);
            $per_page = (isset($input['per_page']) && $input['per_page'] > 0) ? $input['per_page'] : 0;
            $page = (isset($input['page']) && $input['page'] > 0) ? $input['page'] : 0;
            $sear_text = (isset($input['sear_text'])) ? $input['sear_text'] : '';
            $role = (isset($input['role'])) ? $input['role'] : 0;

            $empData = $this->getEmpData($page, $per_page, $sear_text, $role);

            $response = [
                'status' => true,
                'message' => "",
                'errors' => new \stdClass(),
                'data' => $empData
            ];
            return $this->respond($response);
        }catch (\Exception $e){
            //echo "<pre>"; print_r($e); die;
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
    function getEmpData($page = 0, $per_page, $search_text = '', $role = 0){
        //$array = ['users.firstName' => $search_text, 'users.lastName' => $search_text];
        //echo "<pre>"; print_r($this->current_user); die;
        if($role > 0) {
            $totalCount = $this->usersModel
                ->select("users.id, users.firstName, users.lastName, users.mobileNo, emp.empNo, emp.designation")
                ->join('school_mapping as sm', "sm.userId = users.id", 'left')
                ->join('employee_details as emp', "emp.userId = users.id")
                ->join('assigned_role as ar', "ar.userId = users.id")
                ->where("sm.schoolId", $this->current_user['schoolIds'])
                ->where("sm.deletedAt", '0000-00-00 00:00:00')
                ->where("emp.schoolId", $this->current_user['schoolIds'])
                ->where("emp.deletedAt", '0000-00-00 00:00:00')
                ->where("ar.roleId", $role)
                ->groupStart()
                ->like('emp.empNo', $search_text)
                ->orLike('users.mobileNo', $search_text)
                ->orLike('users.firstName', $search_text)
                ->orLike('users.lastName', $search_text)
                ->groupEnd()
                ->groupBy('users.mobileNo')
                ->countAllResults();
        }else{
            $totalCount = $this->usersModel
                ->select("users.id, users.firstName, users.lastName, users.mobileNo, emp.empNo, emp.designation")
                ->join('school_mapping as sm', "sm.userId = users.id", 'left')
                ->join('employee_details as emp', "emp.userId = users.id")
                ->join('assigned_role as ar', "ar.userId = users.id")
                ->where("sm.schoolId", $this->current_user['schoolIds'])
                ->where("sm.deletedAt", '0000-00-00 00:00:00')
                ->where("emp.schoolId", $this->current_user['schoolIds'])
                ->where("emp.deletedAt", '0000-00-00 00:00:00')
                ->groupStart()
                ->like('emp.empNo', $search_text)
                ->orLike('users.mobileNo', $search_text)
                ->orLike('users.firstName', $search_text)
                ->orLike('users.lastName', $search_text)
                ->groupEnd()
                ->groupBy('users.mobileNo')
                ->countAllResults();
        }
        //echo $this->usersModel->getLastQuery(); die;
        //echo "<pre>"; print_r($totalCount); die;
        $pages = 0;
        $offset = 0;
        if($per_page > 0) {
            $pages = ceil($totalCount / $per_page);
            $offset = ($page - 1) * $per_page;
        }
        //echo "<pre>"; print_r($offset); die;
        if($role > 0) {
            $empData = $this->usersModel
                ->select("users.id, users.firstName, users.lastName, users.mobileNo, emp.empNo, emp.designation")
                ->join('school_mapping as sm', "sm.userId = users.id", 'left')
                ->join('employee_details as emp', "emp.userId = users.id")
                ->join('assigned_role as ar', "ar.userId = users.id")
                ->where("sm.schoolId", $this->current_user['schoolIds'])
                ->where("sm.deletedAt", '0000-00-00 00:00:00')
                ->where("emp.schoolId", $this->current_user['schoolIds'])
                ->where("emp.deletedAt", '0000-00-00 00:00:00')
                ->where("ar.roleId", $role)
                ->groupStart()
                ->like('emp.empNo', $search_text)
                ->orLike('users.mobileNo', $search_text)
                ->orLike('users.firstName', $search_text)
                ->orLike('users.lastName', $search_text)
                ->groupEnd()
                ->groupBy('users.mobileNo')
                ->findAll($per_page, $offset);
        }else{
            $empData = $this->usersModel
                ->select("users.id, users.firstName, users.lastName, users.mobileNo, emp.empNo, emp.designation")
                ->join('school_mapping as sm', "sm.userId = users.id", 'left')
                ->join('employee_details as emp', "emp.userId = users.id")
                ->join('assigned_role as ar', "ar.userId = users.id")
                ->where("sm.schoolId", $this->current_user['schoolIds'])
                ->where("sm.deletedAt", '0000-00-00 00:00:00')
                ->where("emp.schoolId", $this->current_user['schoolIds'])
                ->where("emp.deletedAt", '0000-00-00 00:00:00')
                ->groupStart()
                ->like('emp.empNo', $search_text)
                ->orLike('users.mobileNo', $search_text)
                ->orLike('users.firstName', $search_text)
                ->orLike('users.lastName', $search_text)
                ->groupEnd()
                ->groupBy('users.mobileNo')
                ->findAll($per_page, $offset);
        }
       // echo $this->usersModel->getLastQuery(); die;
        $list_data = array(
            'total' => $totalCount,
            'pages' => $pages,
            'current_page' =>$page,
            'limit' => $per_page,
            'result' => $empData,
        );
        return $list_data;
    }
    public function deleteEmp()
    {
        try{
            $input = getRequestInput($this->request);
            //echo "<pre>"; print_r($input); die;
            $rules = [
                'id' => [
                    'label'  => 'id',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Employee ID is required'
                    ]
                ],
            ];
            if (!$this->validateRequest($input, $rules)){
                $response = [
                    'status' => false,
                    'message' => "",
                    'errors' => $this->validator->getErrors(),
                    'data' => new \stdClass()
                ];
                return $this->respond($response);
            } else {
//                $isDelete = $this->usersModel
//                    ->where('id', $input['id'])
//                    ->delete();
//                if($isDelete){
                    // delete form employee detail
                    $this->EmployeeDetail
                        ->where('userId', $input['id'])
                        ->where('schoolId', $input['schoolId'])
                        ->delete();

                    // delete form role
                    $this->assignedRoleModel
                        ->where('userId', $input['id'])
                        ->where('schoolId', $input['schoolId'])
                        ->delete();
                    // delete form role
                    $this->schoolMapping
                        ->where('userId', $input['id'])
                        ->where('schoolId', $input['schoolId'])
                        ->delete();
                    $response = [
                        'status' => true,
                        'message' => "Employee deleted successfully.",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass(),
                    ];
                    return $this->respond($response);
                //}else{
//                    $response = [
//                        'status' => false,
//                        'message' => "Employee not deleted.",
//                        'errors' => new \stdClass(),
//                        'data' => new \stdClass(),
//                    ];
//                    return $this->respond($response);
                //}
            }
           // echo "<pre>"; print_r($input); die;
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
    public function empValidateByMobile()
    {
        try {
            $input = getRequestInput($this->request);
            $rules = [
                'mobileNo' => [
                    'label' => 'mobileNo',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Mobile number is required'
                    ]
                ],
            ];
            if (!$this->validateRequest($input, $rules)){
                $response = [
                    'status' => false,
                    'message' => "",
                    'errors' => $this->validator->getErrors(),
                    'data' => new \stdClass()
                ];
                return $this->respond($response);
            } else {
                $userData = $this->usersModel
                    ->select('users.*, ifnull(sm.schoolIds,"") as schoolIds')
                    ->join('(select userId, group_concat(schoolId) schoolIds from school_mapping group by userId ) as sm', "sm.userId = users.id", 'left')
                    ->where("users.mobileNo", $input['mobileNo'])
                    ->first();
                unset($userData['OTP']);
                if(!empty($userData)) {
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => $userData
                    ];
                }else{
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass()
                    ];
                }
                return $this->respond($response);
            }
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

}