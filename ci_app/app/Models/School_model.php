<?php namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class School_model extends Model
{
    protected $table = 'schools';

    protected $primaryKey = 'id';
    protected $allowedFields = ['schoolName', 'address', 'city', 'state', 'zipcoce', 'logo', 'isActive'];
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

        $response = $this->save($data);

        if(isset($data['id'])){
            return $response;
        }else{
            $insert_id = $this->insertID();
            return $insert_id;
        }
    }
    public function getSchoolProfile($current_user_id = 0){
        $base_url = base_url('uploads');
        $schooldata = $this->select("{$this->table}.*, CONCAT('".$base_url."/school_logo/',logo) as logo_path")
                        ->join('school_mapping as sm', "sm.schoolId = {$this->table}.id")
                        ->where("{$this->table}.isActive", 1)
                        ->where("sm.userId", $current_user_id)
                        ->first();
        return $schooldata;
    }

}