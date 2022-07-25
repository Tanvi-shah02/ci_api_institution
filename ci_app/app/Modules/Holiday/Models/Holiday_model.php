<?php namespace App\Modules\Holiday\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Holiday_model extends Model
{
    protected $table = 'holidays';

    protected $primaryKey = 'id';
    protected $allowedFields = ['schoolId','holidayName','startDate','endDate'];
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

        if(isset($data['id']) && ($data['id'] > 0)){
            return $response;
        }else{
            $insert_id = $this->insertID();
            return $insert_id;
        }
    }

}
