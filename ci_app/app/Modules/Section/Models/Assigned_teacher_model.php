<?php namespace App\Modules\Section\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Assigned_teacher_model extends Model
{
    protected $table = 'assigned_teacher';

    protected $primaryKey = 'id';
    protected $allowedFields = ['sectionId', 'teacherId', 'typeId'];
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function proccessData($data){
        //echo "new"; die;
        $response = $this->save($data);

        if(isset($data['id']) && ($data['id'] > 0)){
            return $response;
        }else{
            $insert_id = $this->insertID();
            return $insert_id;
        }
    }

    public function teachersList($sectionId, $page = 0, $per_page, $search_text = ''){
        $totalCount = $this
            ->select("us.id, us.firstName, us.middleName, us.lastName, {$this->table}.typeId")
            ->join("users AS us", "{$this->table}.teacherId = us.id", 'inner')
            ->where("{$this->table}.sectionId",$sectionId)
            ->groupStart()
            ->like("us.firstName", $search_text)
            ->orLike("us.middleName", $search_text)
            ->orLike("us.lastName", $search_text)
            ->groupEnd()
            ->countAllResults();

        $pages = 0;
        $offset = 0;
        if($per_page > 0) {
            $pages = ceil($totalCount / $per_page);
            $offset = ($page - 1) * $per_page;
        }


        $teacherList =  $this
                      ->select("us.id, us.firstName, us.middleName, us.lastName, {$this->table}.typeId")
                      ->join("users AS us", "{$this->table}.teacherId = us.id", 'inner')
                      ->where("{$this->table}.sectionId",$sectionId)
                      ->groupStart()
                      ->like("us.firstName", $search_text)
                      ->orLike("us.middleName", $search_text)
                      ->orLike("us.lastName", $search_text)
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