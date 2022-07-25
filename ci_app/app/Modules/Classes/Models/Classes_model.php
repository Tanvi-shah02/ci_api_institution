<?php namespace App\Modules\Classes\Models;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Classes_model extends Model
{
    protected $table = 'classes';

    protected $primaryKey = 'id';
    protected $allowedFields = ['schoolId', 'className', 'isActive','createdBy'];
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
    protected $deletedField = 'deletedAt';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function processData($data){
        $response = $this->save($data);

        if(isset($data['id'])){
            return $response;
        }else{
            $insert_id = $this->insertID();
            return $insert_id;
        }
    }

    public function getClassesBySchoolId($schoolId, $perPage = 0, $page = 0, $search = '') {
        $this->where('schoolId', $schoolId);
        $this->orderBy('updatedAt', 'DESC');

        if(!empty($search)) {
            $this->like('className', $search);
        }

        $total = $pages = $current_page = 0;
        if($perPage == 0) {
            $results = $this->findAll();
        }else{
            $results = $this->paginate($perPage, 'default', $page);
            $pager = $this->pager;
            $total = $pager->getTotal();
            $pages =  $pager->getPageCount();
            $current_page = $pager->getCurrentPage();
        }



        $list_data = [];
        if(!empty($results)) {
            $list_data = array(
                'total' => $total,
                'pages' => $pages,
                'current_page' => $current_page,
                'limit' => $perPage,
                'result' => $results,
            );
        }

        return $list_data;
    }
}