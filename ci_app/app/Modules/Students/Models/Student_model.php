<?php namespace App\Modules\Students\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Student_model extends Model
{
    protected $table = 'students';

    protected $primaryKey = 'id';
    protected $allowedFields = ['schoolId', 'enrollmentNo', 'firstName','lastName','dob','addressLine1','addressLine2','state','city','zipCode','class','phoneNo','profilePic','isActive','createdBy'];
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
    protected $deletedField = 'deletedAt';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
    public function proccessData($data){
        $response = $this->save($data);

        if(isset($data['id'])){
            return $response;
        }else{
            $insert_id = $this->insertID();
            return $insert_id;
        }
    }
    public function getStudentByEnrNo($enrNo = ''){
        $data = $this->select("*")
            ->where("enrollmentNo", $enrNo)
            ->first();
        return $data;
    }
}