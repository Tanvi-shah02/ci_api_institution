<?php

namespace Modules\School_profile\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;
use App\Models\Users_model;
use App\Models\Assigned_role_model;
use App\Models\School_model;
class School extends RestController
{
    public function __construct()
    {
        $this->usersModel = new Users_model();
        $this->schoolModel = new School_model();
        //$this->assignedRoleModel = new Assigned_role_model();
    }
    public function profile()
    {
        try {
            $current_user_id = $this->current_user['id'];
            $school_data = $this->schoolModel->getSchoolProfile($current_user_id);
            $response = [
                'status' => true,
                'message' => "",
                'errors' => new \stdClass(),
                'data' => $school_data
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
    public function save()
    {
        try {
            $rules = [
                'schoolName' => [
                    'label'  => 'schoolName',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'school Name is required'
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
                $schoolId = (isset($input['schoolId']) && !empty($input['schoolId'])) ? $input['schoolId'] : 0;
                $data = array(
                    'schoolName' => $input['schoolName'],
                    'address'    => $input['address']
                );
                if($schoolId > 0){
                    $data['id'] = $schoolId;
                }
                $insert_id = $this->schoolModel->save($data);
                if($insert_id > 0){
                    $logo = $this->request->getFile('logo');
                    if(!empty($logo)) {
                        $file_name = $logo->getName();
                        if (!empty($file_name)) {
                            $validated_file = [
                                'logo' => [
                                    'label' => 'Logo File',
                                    'rules' => 'uploaded[logo]'
                                        . '|is_image[logo]'
                                        . '|mime_in[logo,image/jpg,image/jpeg,image/png]'
                                        . '|max_size[logo,4096]'
                                ],
                            ];
                            if (!$this->validate($validated_file)) {
                                $response = [
                                    'status' => true,
                                    'message' => "",
                                    'errors' => $this->validator->getErrors(),
                                    'data' => new \stdClass(),
                                ];
                                return $this->respond($response, 400);
                            }
                            $type = $logo->getClientExtension();

                            $newName = strtotime(date('Y-m-d H:i:s')) . '.' . $type;

                            $logo->move(FCPATH . 'uploads/school_logo', $newName);

                            $data = [
                                'logo' => $newName,
                            ];
                            if ($schoolId > 0) {
                                $data['id'] = $schoolId;
                            } else {
                                $data['id'] = $insert_id;
                            }
                            $this->schoolModel->save($data);

                        }
                    }
                    $response = [
                        'status' => true,
                        'message' => "School profile is saved.",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass(),
                    ];
                    return $this->respond($response);
                }
            }
        }catch (\Exception $e){
            echo "<pre>"; print_r($e); die;
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