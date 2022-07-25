<?php

namespace Modules\Students\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;
use App\Models\Users_model;
use App\Models\Assigned_role_model;
use App\Modules\Students\Models\Student_model;
use App\Models\School_mapping_model;
use App\Modules\Students\Models\Studentmapping_model;


class Students extends RestController
{
    public function __construct()
    {
        $this->usersModel = new Users_model();
        $this->schoolMapping = new School_mapping_model();
        $this->assignedRoleModel = new Assigned_role_model();
        $this->studentModel = new Student_model();
        $this->studentMapping = new Studentmapping_model();
    }
    public function save()
    {
        try {
            $input = getRequestInput($this->request);
            $rules = [
                'enrolmentNo' => [
                    'label'  => 'enrolmentNo',
                    'rules'  => 'required|is_unique[students.enrollmentNo]',
                    'errors' => [
                        'required' => 'Enrolment No is required',
                        'is_unique' => 'Enrolment No is already exist'
                    ]
                ],
                'firstName' => [
                    'label'  => 'firstName',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'first Name is required'
                    ]
                ],
                'lastName' => [
                    'label'  => 'lastName',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Last Name is required'
                    ]
                ],
                'class' => [
                    'label'  => 'class',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Class is required'
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
//                $student_data = $this->studentModel->getStudentByEnrNo($input['enrolmentNo']);
//                if(!empty($student_data)){
//                    $errors = array("Student is already exist");
//                    $response = [
//                        'status' => false,
//                        'message' => "",
//                        'errors' => $errors,
//                        'data' => new \stdClass(),
//                    ];
//                    return $this->respond($response, 400);
//                }
                $isFatherProfile = $isMotherProfile = $isGuardianProfile = false;
                if($input['fatherFirstName'] != '' && $input['fatherLastName'] != ''){
                    /// set father profile
                    $isFatherProfile = true;
                    if($input['fatherMobileNo'] == ''){
                        $response = [
                            'status' => false,
                            'message' => "Father mobile number is required.",
                            'errors' => new \stdClass(),
                            'data' => new \stdClass(),
                        ];
                        return $this->respond($response);
                    }
                }
                if($input['motherFirstName'] != '' && $input['motherLastName'] != ''){
                    /// set mother profile
                    $isMotherProfile = true;
                    if($input['motherMobileNo'] == ''){
                        $response = [
                            'status' => false,
                            'message' => "Mother mobile number is required.",
                            'errors' => new \stdClass(),
                            'data' => new \stdClass(),
                        ];
                        return $this->respond($response);
                    }
                }
                if($input['guardianFirstName'] != '' && $input['guardianLastName'] != ''){
                    /// set mother profile
                    ///
                    $isGuardianProfile = true;
                    if($input['guardianMobileNo'] == ''){
                        $response = [
                            'status' => false,
                            'message' => "Guardian mobile number is required.",
                            'errors' => new \stdClass(),
                            'data' => new \stdClass(),
                        ];
                        return $this->respond($response);
                    }
                }
                if(!$isFatherProfile && !$isMotherProfile && !$isGuardianProfile){
                    $response = [
                        'status' => false,
                        'message' => "Any profile information from the Father, Mother, or Guardian profiles is required.",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass(),
                    ];
                    return $this->respond($response);
                }else{
                    //echo "<pre>"; print_r($input); die;


                    $data = array(
                        'enrollmentNo' => $input['enrolmentNo'],
                        'schoolId' => $this->current_user['schoolIds'],
                        'firstName'    => $input['firstName'],
                        'lastName'    => $input['lastName'],
                        'dob'    => $input['dob'],
                        'addressLine1'    => $input['addressLine'],
                        'addressLine2'    => $input['addressLine2'],
                        'class'    => $input['class'],
                        'phoneNo'    => $input['phoneNo'],
                        'zipCode'    => $input['pinCode'],
                        'state'    => $input['state'],
                        'city'    => $input['city'],
                        'isActive'    => 1,
                        'createdBy'   => $this->current_user['id'],
                    );
                    $insertId = $this->studentModel->proccessData($data);
                    if($insertId > 0){
                        $studentProfilePic = $this->request->getFile('studentProfilePic');
                        if(!empty($studentProfilePic)) {
                            $file_name = $studentProfilePic->getName();
                            if (!empty($file_name)) {
                                $validated_file = [
                                    'studentProfilePic' => [
                                        'label' => 'Student Profile',
                                        'rules' => 'uploaded[studentProfilePic]'
                                            . '|is_image[studentProfilePic]'
                                            . '|mime_in[studentProfilePic,image/jpg,image/jpeg,image/png]'
                                            . '|max_size[studentProfilePic,4096]'
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
                                $type = $studentProfilePic->getClientExtension();

                                $newName = strtotime(date('Y-m-d H:i:s')) . '.' . $type;

                                $studentProfilePic->move(FCPATH . 'uploads/student_profile', $newName);
                                $data = [
                                    'profilePic' => $newName,
                                ];
                                $data['id'] = $insertId;
                                $this->studentModel->proccessData($data);
                            }
                        }
                        // father profile
                        $father_id = 0;
                        $student_mapping = array();
                        $is_user = $this->userValidateByMobile($input['fatherMobileNo']);
                        if(!$is_user){
                            $data = array(
                                'firstName' => $input['fatherFirstName'],
                                'lastName'    => $input['fatherLastName'],
                                'mobileNo'    => $input['fatherMobileNo'],
                                'isActive'    => 1,
                                'createdBy'   => $this->current_user['id'],
                            );
                            $father_id = $this->createUser($data, 3);
                            $fatherProfilePic = $this->request->getFile('fatherProfilePic');
                            if(!empty($fatherProfilePic)) {
                                $f_file_name = $fatherProfilePic->getName();
                                if (!empty($f_file_name)) {
                                    $validated_file = [
                                        'fatherProfilePic' => [
                                            'label' => 'Father Profile',
                                            'rules' => 'uploaded[fatherProfilePic]'
                                                . '|is_image[fatherProfilePic]'
                                                . '|mime_in[fatherProfilePic,image/jpg,image/jpeg,image/png]'
                                                . '|max_size[fatherProfilePic,4096]'
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
                                    $type = $fatherProfilePic->getClientExtension();

                                    $newName = $father_id."-".strtotime(date('Y-m-d H:i:s')) . '.' . $type;

                                    $fatherProfilePic->move(FCPATH . 'uploads/user_profile', $newName);
                                    $data = [
                                        'profilePic' => $newName,
                                    ];
                                    $data['id'] = $father_id;
                                    $this->usersModel->proccessData($data);
                                }
                            }
                        }else{
                            $father_id = $is_user['id'];
                        }
                        $map_data = array(
                            'studentId' => $insertId,
                            'userId' => $father_id,
                        );
                        array_push($student_mapping, $map_data);

                        // mother profile
                        $mother_id = 0;
                        if($input['fatherMobileNo'] != $input['motherMobileNo']) {

                            $is_user = $this->userValidateByMobile($input['motherMobileNo']);
                            if (!$is_user) {
                                $data = array(
                                    'firstName' => $input['motherFirstName'],
                                    'lastName' => $input['motherLastName'],
                                    'mobileNo' => $input['motherMobileNo'],
                                    'isActive' => 1,
                                    'createdBy' => $this->current_user['id'],
                                );
                                $mother_id = $this->createUser($data, 3);
                                $motherProfilePic = $this->request->getFile('motherProfilePic');
                                if(!empty($motherProfilePic)) {
                                    $m_file_name = $motherProfilePic->getName();
                                    if (!empty($m_file_name)) {
                                        $validated_file = [
                                            'motherProfilePic' => [
                                                'label' => 'Mother Profile',
                                                'rules' => 'uploaded[motherProfilePic]'
                                                    . '|is_image[motherProfilePic]'
                                                    . '|mime_in[motherProfilePic,image/jpg,image/jpeg,image/png]'
                                                    . '|max_size[motherProfilePic,4096]'
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
                                        $type = $motherProfilePic->getClientExtension();

                                        $newName = $mother_id."-".strtotime(date('Y-m-d H:i:s')) . '.' . $type;

                                        $motherProfilePic->move(FCPATH . 'uploads/user_profile', $newName);
                                        $data = [
                                            'profilePic' => $newName,
                                        ];
                                        $data['id'] = $mother_id;
                                        $this->usersModel->proccessData($data);
                                    }
                                }
                            } else {
                                $mother_id = $is_user['id'];
                            }

                            $map_data = array(
                                'studentId' => $insertId,
                                'userId' => $mother_id,
                            );
                            array_push($student_mapping, $map_data);
                        }
                        // guardian profile
                        $guardian_id = 0;
                        if(($input['fatherMobileNo'] != $input['guardianMobileNo']) && ($input['motherMobileNo'] != $input['guardianMobileNo'])) {

                            $is_user = $this->userValidateByMobile($input['guardianMobileNo']);
                            if (!$is_user) {
                                $data = array(
                                    'firstName' => $input['guardianFirstName'],
                                    'lastName' => $input['guardianLastName'],
                                    'mobileNo' => $input['guardianMobileNo'],
                                    'isActive' => 1,
                                    'createdBy' => $this->current_user['id'],
                                );
                                $guardian_id = $this->createUser($data, 4);
                                $guardianProfilePic = $this->request->getFile('guardianProfilePic');
                                if(!empty($guardianProfilePic)) {
                                    $g_file_name = $guardianProfilePic->getName();
                                    if (!empty($g_file_name)) {
                                        $validated_file = [
                                            'motherProfilePic' => [
                                                'label' => 'Mother Profile',
                                                'rules' => 'uploaded[guardianProfilePic]'
                                                    . '|is_image[guardianProfilePic]'
                                                    . '|mime_in[guardianProfilePic,image/jpg,image/jpeg,image/png]'
                                                    . '|max_size[guardianProfilePic,4096]'
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
                                        $type = $guardianProfilePic->getClientExtension();

                                        $newName = $guardian_id."-".strtotime(date('Y-m-d H:i:s')) . '.' . $type;

                                        $guardianProfilePic->move(FCPATH . 'uploads/user_profile', $newName);
                                        $data = [
                                            'profilePic' => $newName,
                                        ];
                                        $data['id'] = $guardian_id;
                                        $this->usersModel->proccessData($data);
                                    }
                                }

                            } else {
                                $guardian_id = $is_user['id'];
                            }
                            $map_data = array(
                                'studentId' => $insertId,
                                'userId' => $guardian_id,
                            );
                            array_push($student_mapping, $map_data);
                        }
                        //echo "<pre>"; print_r($student_mapping); die;
                        $this->studentMapping->insertBatch($student_mapping);
                        $response = [
                            'status' => true,
                            'message' => "Student data saved",
                            'errors' => new \stdClass(),
                            'data' => new \stdClass(),
                        ];
                        return $this->respond($response, 200);
                    }
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

    public function userValidateByMobile($mobileNo = '')
    {
        $userData = $this->usersModel
            ->select('users.*, ifnull(sm.schoolIds,"") as schoolIds')
            ->join('(select userId, group_concat(schoolId) schoolIds from school_mapping group by userId ) as sm', "sm.userId = users.id", 'left')
            ->where("users.mobileNo", $mobileNo)
            ->first();
        if(isset($userData) && !empty($userData)){
            return $userData;
        }else{
            return false;
        }
    }
    function createUser($data = '', $roleId = 0){
        $insert_id = $this->usersModel->proccessData($data);
        if($insert_id > 0){
            // Assign role
            $roleData = array(
                'userId' => $insert_id,
                'roleId' => $roleId,
                'schoolId' => $this->current_user['schoolIds'],
            );
            $roleInsertId = $this->assignedRoleModel->save($roleData);

            // Assign School
            $schoolData = array(
                'userId' => $insert_id,
                'schoolId' => $this->current_user['schoolIds'],
            );
            $smInsertId = $this->schoolMapping->save($schoolData);
        }
        return $insert_id;
    }
}