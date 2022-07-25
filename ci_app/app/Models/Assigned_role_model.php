<?php namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Assigned_role_model extends Model
{
    protected $table = 'assigned_role';

    protected $primaryKey = 'id';
    protected $allowedFields = ['userId', 'roleId', 'schoolId'];
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

}