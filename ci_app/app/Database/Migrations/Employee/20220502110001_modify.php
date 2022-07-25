<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class EmployeeDetailsAddSchoolId extends Migration
{
    public function up()
    {
        $fields = [
            'studentProfilePic' => [
                'type'       => 'VARCHAR',
                'constraint' => '11',
                'after' => 'userId'
            ],
        ];
        $this->forge->addColumn('employee_details',$fields);
    }
    public function down()
    {

    }
}