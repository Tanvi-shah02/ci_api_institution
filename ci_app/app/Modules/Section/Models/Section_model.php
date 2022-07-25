<?php namespace App\Modules\Section\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;
use App\Modules\Section\Models\Assigned_teacher_model;

class Section_model extends Model
{
    protected $table = 'section_details';

    protected $primaryKey = 'id';
    protected $allowedFields = ['schoolId','classId','section'];
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
    protected $deletedField = 'deletedAt';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
        $this->assignedTeacherModel = new Assigned_teacher_model();
    }


    public function proccessData($data){
        //echo "new"; die;
        $response = $this->save($data);

        if(isset($data['id'])  && ($data['id'] > 0)){
            return $response;
        }else{
            $insert_id = $this->insertID();
            return $insert_id;
        }
    }

    public function getList($schoolId, $page = 0, $per_page, $search_text = ''){
        $totalCount =   $this->select("{$this->table}.id, {$this->table}.section, cl.className")
                            ->join("classes AS cl", "{$this->table}.classId = cl.id", 'inner')
                            ->where("{$this->table}.schoolId",$schoolId)
                            ->groupStart()
                            ->like("{$this->table}.section", $search_text)
                            ->orLike('cl.className', $search_text)
                            ->groupEnd()
                            ->countAllResults();

        //echo  $this->getLastQuery(); die;
        //echo $totalCount; die;

        $pages = 0;
        $offset = 0;
        if($per_page > 0) {
            $pages = ceil($totalCount / $per_page);
            $offset = ($page - 1) * $per_page;
        }

        $sectionList = $this
                        ->select("{$this->table}.id, {$this->table}.section, {$this->table}.classId, cl.className")
                        ->join("classes AS cl", "{$this->table}.classId = cl.id", 'inner')
                        ->where("{$this->table}.schoolId",$schoolId)
                        ->groupStart()
                        ->like("{$this->table}.section", $search_text)
                        ->orLike('cl.className', $search_text)
                        ->groupEnd()
                        ->findAll($per_page, $offset);
        //echo "<pre>"; print_r($sectionList);
        $i = 0;
        foreach ($sectionList as $sc){
            $sectionId = $sc['id'];
            $teachers = $this->assignedTeacherModel->select('*')
                        ->where('sectionId', $sectionId)
                        ->whereIn('typeId', [1,2])
                        ->findAll();

            foreach($teachers as $teacher){
                if($teacher['typeId'] == 1){
                    $sectionList[$i]['classTeacher'] = $teacher['teacherId'];
                }elseif($teacher['typeId'] == 2){
                    $sectionList[$i]['classAdmin'] = $teacher['teacherId'];
                }
            }
            $i++;
        }

        //die;
       // echo  $this->getLastQuery(); die;
        $list_data = array(
            'total' => $totalCount,
            'pages' => $pages,
            'current_page' =>$page,
            'limit' => $per_page,
            'result' => $sectionList,
        );

        return $list_data;
    }

   /* public function getList($schoolId){
        $this->select("{$this->table}.id, {$this->table}.section, cl.className");
        $this->join("classes AS cl", "{$this->table}.classId = cl.id", 'inner');
        $this->where("{$this->table}.schoolId",$schoolId);
        //$this->get();
        //echo  $this->getLastQuery(); die;
        $response = $this->findAll();
        return $response;
    }*/

    public function teachersListForTeacher($schoolId, $teacherId, $page = 0, $per_page, $search_text = ''){
        $totalCount =    $this
                         ->select("{$this->table}.id, {$this->table}.section, cl.className")
                         ->join("classes AS cl", "{$this->table}.classId = cl.id", 'inner')
                         ->join("assigned_teacher AS at", "{$this->table}.id = at.sectionId", 'inner')
                         ->where("{$this->table}.schoolId",$schoolId)
                         ->where("at.teacherId",$teacherId)
                         ->groupStart()
                         ->like("{$this->table}.section", $search_text)
                         ->orLike('cl.className', $search_text)
                         ->groupEnd()
                         ->countAllResults();

        //$this->get();
        //echo  $this->getLastQuery(); die;

        $pages = 0;
        $offset = 0;
        if($per_page > 0) {
            $pages = ceil($totalCount / $per_page);
            $offset = ($page - 1) * $per_page;
        }

        $teacherList = $this
                        ->select("{$this->table}.id, {$this->table}.section, cl.className")
                        ->join("classes AS cl", "{$this->table}.classId = cl.id", 'inner')
                        ->join("assigned_teacher AS at", "{$this->table}.id = at.sectionId", 'inner')
                        ->where("{$this->table}.schoolId",$schoolId)
                        ->where("at.teacherId",$teacherId)
                        ->groupStart()
                        ->like("{$this->table}.section", $search_text)
                        ->orLike('cl.className', $search_text)
                        ->groupEnd()
                        ->findAll($per_page, $offset);

        $list_data = array(
            'total' => $totalCount,
            'pages' => $pages,
            'current_page' =>$page,
            'limit' => $per_page,
            'result' => $teacherList,
        );

        return $list_data;
    }

}