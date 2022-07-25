<?php namespace App\Modules\Employee\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class EmployeeDetail_model extends Model
{
    protected $table = 'employee_details';

    protected $primaryKey = 'id';
    protected $allowedFields = ['userId', 'empNo', 'designation','schoolId'];
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
    protected $deletedField = 'deletedAt';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
    }



    /*public function getList($page = 1, $per_page = 10){
        $this->select("{$this->table}.*")
            //->join('assigned_role as ar', "ar.userId = users.id")
            ->where("users.mobileNo", $input['mobile_no'])
            ->where("users.isActive", 1)
            ->whereIn('ar.roleId', $allow_roles)
            ->countAllResults();
    }*/


}