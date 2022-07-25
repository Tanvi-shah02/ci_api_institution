<?php

namespace Modules\Holiday\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Controllers\Api\RestController;
use App\CodeIgniter\Validation\Exceptions\ValidationException;
use App\Config\Services;

use App\Modules\Holiday\Models\Holiday_model;
use App\Modules\Holiday\Models\Holiday_classes_model;

class Holiday extends RestController
{
    public function __construct()
    {
        $this->holidayModel = new Holiday_model();
        $this->holidayClassesModel = new Holiday_classes_model();
    }
    public function save()
    {
        try {
            $input = getRequestInput($this->request);
            $rules = [
                'holidayName' => [
                    'label' => 'holidayName',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Holiday name is required'
                    ]
                ],
                'startDate' => [
                    'label' => 'startDate',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Start date is required'
                    ]
                ],
                'endDate' => [
                    'label' => 'endDate',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'End date is required'
                    ]
                ],
                'classId' => [
                    'label' => 'classId',
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Class Name is required'
                    ]
                ],
            ];

            if (!$this->validateRequest($input, $rules)) {
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
                    'holidayName' => $input['holidayName'],
                    'startDate' => $input['startDate'],
                    'endDate' => $input['endDate'],
                );

                $added_holiday = $this->holidayModel->proccessData($data);

                if ($added_holiday > 0) {
                    //$id = $input['id'];
                    $classId = $input['classId'];
                    $cId = explode(",", $classId);

                    $hId = (isset($input['id']) && $input['id'] > 0) ? $input['id'] : 0;
                    if(!empty($hId) && $hId > 0){
                        $this->holidayClassesModel->where('holidayId', $hId)->delete();
                    }

                    foreach ($cId as $singleId) {
                        if(!empty($hId) && $hId > 0){
                            $data = array(
                                'holidayId' => $hId,
                                'classId' => $singleId,
                            );
                        }
                        else{
                            $data = array(
                                'holidayId' => $added_holiday,
                                'classId' => $singleId,
                            );
                        }
                        $this->holidayClassesModel->proccessData($data);
                    }
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => $added_holiday
                    ];

                } else {
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

    public function getList()
    {
        try {
                $input = getRequestInput($this->request);

                $schoolId =  $this->current_user['schoolIds'];
                $per_page = (isset($input['per_page']) && $input['per_page'] > 0) ? $input['per_page'] : 0;
                $page = (isset($input['page']) && $input['page'] > 0) ? $input['page'] : 0;
                $sear_text = (isset($input['sear_text'])) ? $input['sear_text'] : '';

                $holiday_list = $this->holidayClassesModel->holidayList($schoolId, $page, $per_page, $sear_text);

                if(!empty($holiday_list)) {
                    $response = [
                        'status' => true,
                        'message' => "",
                        'errors' => new \stdClass(),
                        'data' => $holiday_list,
                    ];
                }else{
                    $response = [
                        'status' => true,
                        'message' => "Holiday List is empty.",
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

}