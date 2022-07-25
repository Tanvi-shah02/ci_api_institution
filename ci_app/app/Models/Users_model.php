<?php namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Users_model extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';
    protected $allowedFields = ['firstName', 'middleName', 'lastName', 'mobileNo', 'email', 'OTP', 'profilePic','isActive', 'createdBy'];
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
}