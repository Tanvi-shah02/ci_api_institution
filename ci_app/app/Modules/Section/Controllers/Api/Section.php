<?php

namespace Modules\Section\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;

use App\Modules\Section\Models\Section_model;
use App\Modules\Section\Models\Assigned_teacher_model;

class Section extends RestController
{
    public function __construct()
    {
        $this->sectionModel = new Section_model();
        $this->assignedTeacherModel = new Assigned_teacher_model();
    }
    public function save()
    {
        try{
            $input = getRequestInput($this->request);
            $rules = [
                'classId' => [
                    'label'  => 'classId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Class is required'
                    ]
                ],
                'section' => [
                    'label'  => 'section',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Section is required'
                    ]
                ],
                'classTeacherId' => [
                    'label'  => 'classTeacherId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Class Teacher is required'
                    ]
                ],
                'classAdminId' => [
                    'label'  => 'classAdminId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Class Admin is required'
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
                $data = array(
                    'id' => (isset($input['id']) && $input['id'] > 0) ? $input['id'] : 0,
                    'schoolId' => $this->current_user['schoolIds'],
                    //'schoolId' => 5,
                    'classId'    => $input['classId'],
                    'section'    => $input['section'],
                );

                $sectionInsertId = $this->sectionModel->proccessData($data);

                if($sectionInsertId > 0){
                    $sId = (isset($input['id']) && $input['id'] > 0) ? $input['id'] : 0;
                    if(!empty($sId) && $sId > 0){
                        $this->assignedTeacherModel->where('sectionId', $sId)->delete();
                    }

                    if(!empty($sId) && $sId > 0){
                        $data = [
                            [
                                'sectionId' => $sId,
                                'teacherId'  => $input['classTeacherId'],
                                'typeId'  => 1,
                            ],
                            [
                                'sectionId' => $sId,
                                'teacherId'  => $input['classAdminId'],
                                'typeId'  => 2,
                            ],
                        ];
                    }else{
                        $data = [
                            [
                                'sectionId' => $sectionInsertId,
                                'teacherId'  => $input['classTeacherId'],
                                'typeId'  => 1,
                            ],
                            [
                                'sectionId' => $sectionInsertId,
                                'teacherId'  => $input['classAdminId'],
                                'typeId'  => 2,
                            ],
                        ];
                    }


                    $this->assignedTeacherModel->insertBatch($data);

                    $response = [
                        'status' => true,
                        'message' => "Section details are saved.",
                        'errors' => new \stdClass(),
                        'data' => $sectionInsertId,
                    ];
                    return $this->respond($response);
                }else{
                    $response = [
                        'status' => false,
                        'message' => "Section details are not saved.",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass()
                    ];
                    return $this->respond($response);
                }

            }

        }catch(\Exception $e){
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

            $schoolId =  $this->current_user['schoolIds'];

            //$section_list = $this->sectionModel->getList($schoolId);
            $section_list = $this->sectionModel->getList($schoolId, $page, $per_page, $sear_text);
            //echo "<pre>"; print_r($section_list); die;

            if(!empty($section_list)) {
                $response = [
                    'status' => true,
                    'message' => "",
                    'errors' => new \stdClass(),
                    'data' => $section_list
                ];
            }else{
                $response = [

                    'status' => true,
                    'message' => "Section List is empty.",
                    'errors' => new \stdClass(),
                    'data' => new \stdClass()
                ];
            }
            return $this->respond($response);

        }catch(\Exception $e){
            //echo $e;
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

    public function getTeacherList()
    {
        try{
            $input = getRequestInput($this->request);
            $rules = [
                'sectionId' => [
                    'label'  => 'sectionId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Section is required'
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
                $sectionId = $input['sectionId'];
                $per_page = (isset($input['per_page']) && $input['per_page'] > 0) ? $input['per_page'] : 0;
                $page = (isset($input['page']) && $input['page'] > 0) ? $input['page'] : 0;
                $sear_text = (isset($input['sear_text'])) ? $input['sear_text'] : '';

                $teacher_list = $this->assignedTeacherModel->teachersList($sectionId, $page, $per_page, $sear_text);
                //echo "<pre>"; print_r($section_list);

                if(!empty($teacher_list)) {
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => $teacher_list
                    ];
                }else{
                    $response = [
                        'status' => true,
                        'message' => "Teacher List is empty.",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass()
                    ];
                }
                return $this->respond($response);

            }
        }catch(\Exception $e){
            //echo $e;
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

    public function addTeacherToSection()
    {
        try{
            $input = getRequestInput($this->request);
            $rules = [
                'sectionId' => [
                    'label'  => 'sectionId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Section is required'
                    ]
                ],
                'teacherId' => [
                    'label'  => 'teacherId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Teacher is required'
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
            }else{
                $sectionId = $input['sectionId'];
                $teacherId = $input['teacherId'];

                $data = array(
                    'sectionId'    => $sectionId,
                    'teacherId'    => $teacherId,
                    'typeId'    => 3,
                );

                $added_teacher = $this->assignedTeacherModel->proccessData($data);
                //echo "<pre>"; print_r($section_list); die;

                if($added_teacher > 0) {
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => $added_teacher
                    ];
                }else{
                    $response = [
                        'status' => true,
                        'message' => "Teacher is not added to the section.",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass()
                    ];
                }
                return $this->respond($response);

            }
        }catch(\Exception $e){
            //echo $e;
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

    public function getListBySchoolId()
    {
        try{
            $input = getRequestInput($this->request);
            $rules = [
                'schoolId' => [
                    'label'  => 'schoolId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'School Id is required'
                    ]
                ],
                'teacherId' => [
                    'label'  => 'teacherId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Teacher Id is required'
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
            }else{
                //$input = getRequestInput($this->request);
                $per_page = (isset($input['per_page']) && $input['per_page'] > 0) ? $input['per_page'] : 0;
                $page = (isset($input['page']) && $input['page'] > 0) ? $input['page'] : 0;
                $sear_text = (isset($input['sear_text'])) ? $input['sear_text'] : '';

                $schoolId = $input['schoolId'];
                $teacherId = $input['teacherId'];

                $section_list = $this->sectionModel->teachersListForTeacher($schoolId, $teacherId, $page, $per_page, $sear_text);
               // echo "<pre>"; print_r($section_list); die;

                if(!empty($section_list)) {
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => $section_list
                    ];
                }else{
                    $response = [
                        'status' => true,
                        'message' => "Section List is empty.",
                        'errors' => new \stdClass(),
                        'data' => new \stdClass()
                    ];
                }
                return $this->respond($response);

            }

        }catch(\Exception $e){
            //echo $e;
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
    public function deleteSection(){
        try {
            $input = getRequestInput($this->request);
            $rules = [
                'id' => [
                    'label'  => 'id',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Section ID is required'
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
                $this->assignedTeacherModel
                    ->where('sectionId', $input['id'])
                    ->delete();

                $this->sectionModel
                        ->where('id', $input['id'])
                        ->delete();
                $response = [
                    'status' => true,
                    'message' => "Section deleted successfully.",
                    'errors' => new \stdClass(),
                    'data' => new \stdClass(),
                ];
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

    public function removeTeacherFromSection()
    {
        //echo "remove"; die;
        try {
            $input = getRequestInput($this->request);
            $rules = [
                'sectionId' => [
                    'label'  => 'sectionId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Section ID is required'
                    ]
                ],
                'teacherId' => [
                    'label'  => 'teacherId',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Teacher ID is required'
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
                $this->assignedTeacherModel
                    ->where('teacherId', $input['teacherId'])
                    ->where('sectionId', $input['sectionId'])
                    ->delete();
                //echo  $this->assignedTeacherModel->getLastQuery(); die;
                $response = [
                    'status' => true,
                    'message' => "Teacher is removed successfully.",
                    'errors' => new \stdClass(),
                    'data' => new \stdClass(),
                ];
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