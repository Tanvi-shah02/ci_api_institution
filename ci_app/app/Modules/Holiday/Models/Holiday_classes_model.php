<?php namespace App\Modules\Holiday\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use App\Models\Hash;
use CodeIgniter\Validation\ValidationInterface;

class Holiday_classes_model extends Model
{
    protected $table = 'holiday_classes';

    protected $primaryKey = 'id';
    protected $allowedFields = ['holidayId','classId'];
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


    public function holidayList($schoolId, $page = 0, $per_page, $search_text = ''){
        //echo "testttt"; die;
        $totalCount = $this
            ->select("hs.id, hs.startDate, DAYNAME(hs.startDate) as day, hs.holidayName, GROUP_CONCAT(cl.className) as classes")
            ->join("holidays AS hs", "{$this->table}.holidayId = hs.id", 'inner')
            ->join("classes AS cl", "{$this->table}.classId = cl.id", 'inner')
            ->where("hs.schoolId",$schoolId)
            ->groupStart()
            ->like("hs.startDate", $search_text)
            ->orLike("hs.holidayName", $search_text)
            ->groupEnd()
            ->groupBy('hs.id')
            ->countAllResults();

        //echo $totalCount; die;

        $pages = 0;
        $offset = 0;
        if($per_page > 0) {
            $pages = ceil($totalCount / $per_page);
            $offset = ($page - 1) * $per_page;
        }

        $holidayList = $this
            ->select("hs.id, hs.startDate, DAYNAME(hs.startDate) as day, hs.holidayName, GROUP_CONCAT(cl.className) as classes")
            ->join("holidays AS hs", "{$this->table}.holidayId = hs.id", 'inner')
            ->join("classes AS cl", "{$this->table}.classId = cl.id", 'inner')
            ->where("hs.schoolId",$schoolId)
            ->groupStart()
            ->like("hs.startDate", $search_text)
            ->orLike("hs.holidayName", $search_text)
             ->groupEnd()
            ->groupBy('hs.id')
            ->findAll($per_page, $offset);

        $list_data = array(
            'total' => $totalCount,
            'pages' => $pages,
            'current_page' =>$page,
            'limit' => $per_page,
            'result' => $holidayList,
        );
        return $list_data;
    }

}