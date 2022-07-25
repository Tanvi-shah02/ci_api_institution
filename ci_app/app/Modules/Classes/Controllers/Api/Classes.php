<?php

namespace Modules\Classes\Controllers\Api;

use App\Modules\Classes\Models\Classes_model;
use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;


class Classes extends RestController
{
    public function __construct()
    {
    }

    public function lists()
    {
        $input = getRequestInput($this->request);
        //echo "<pre>"; print_r($input); die;
        $perPage = (isset($input['perPage']) && $input['perPage'] > 0) ? $input['perPage'] : 0;
        $page = (isset($input['page']) && $input['page'] > 0) ? $input['page'] : 0;
        $search = (isset($input['search']) && !empty($input['search'])) ? trim($input['search']) : '';
        $schoolId = (isset($input['schoolId']) && !empty($input['schoolId'])) ? trim($input['schoolId']) : $this->current_user['schoolIds'];
        $classesModel = new Classes_model();

        $data = $classesModel->getClassesBySchoolId($schoolId, $perPage, $page, $search);
        if(empty($data)) {
            $response = [
                'status' => false,
                'message' => "",
                'errors' => [
                    'No data found.'
                ],
                'data' => new \stdClass(),
            ];
            return $this->respond($response);
        }

        $response = [
            'status' => true,
            'message' => "",
            'errors' => new \stdClass(),
            'data' => $data,
        ];
        return $this->respond($response);
    }

    public function save()
    {
        try {
            $input = getRequestInput($this->request);
            $rules = [
                'id' => [
                    'label'  => 'id',
                    'rules'  => 'required|is_natural',
                    'errors' => [
                        'required' => 'Id is required',
                        'is_natural' => 'Only numeric is allowed for Id',
                    ]
                ],
                'className' => [
                    'label'  => 'className',
                    'rules'  => 'required',
                    'errors' => [
                        'required' => 'Class is required',
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
            }

            $classesModel = new Classes_model();

            $data = [
                'schoolId' => $this->current_user['schoolIds'],
                'className' => $input['className'],
                'createdBy' => $this->current_user['id']
            ];

            if($input['id'] > 0) {
                $data['id'] = $input['id'];
            }

            $lastId = $classesModel->save($data);

            if($lastId > 0) {
                $response = [
                    'status' => true,
                    'message' => "Class saved successfully.",
                    'errors' => new \stdClass(),
                    'data' => new \stdClass(),
                ];
                return $this->respond($response, 200);
            } else {
                $response = [
                    'status' => false,
                    'message' => "",
                    'errors' => [
                        'Something went wrong. Class not saved.'
                    ],
                    'data' => new \stdClass(),
                ];
                return $this->respond($response, 200);
            }
            echo "<pre>";
            print_r($data);
            echo "</pre>"; die;

        } catch (\Exception $e){
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

    public function delete($id = NULL)
    {
        $input = getRequestInput($this->request);
        $id = (isset($input['id']) && $input['id'] > 0) ? $input['id'] : 0;
        if($id <= 0) {
            $response = [
                'status' => false,
                'message' => "",
                'errors' => [
                    'Class id not found.'
                ],
                'data' => new \stdClass()
            ];
            return $this->respond($response);
        }

        $classesModel = new Classes_model();

        $class = $classesModel->find($id);

        if(empty($class)) {
            $response = [
                'status' => false,
                'message' => "",
                'errors' => [
                    'Class not found.'
                ],
                'data' => new \stdClass()
            ];
            return $this->respond($response);
        }

        $classesModel->delete($id);

        $response = [
            'status' => true,
            'message' => "Class deleted successfully.",
            'errors' => new \stdClass(),
            'data' => new \stdClass()
        ];
        return $this->respond($response);
    }
}