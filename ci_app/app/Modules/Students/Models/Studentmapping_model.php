<?php namespace App\Modules\Students\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Studentmapping_model extends Model
{
    protected $table = 'student_mapping';

    protected $primaryKey = 'id';
    protected $allowedFields = ['studentId', 'userId'];
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
}