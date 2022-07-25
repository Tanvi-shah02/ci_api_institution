<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AssignedRolesAddSchoolId extends Migration
{
    public function up()
    {
        $fields = [
            'schoolId' => [
                'type'       => 'INT',
                'constraint' => '11',
                'after' => 'roleId'
            ],
        ];
        $this->forge->addColumn('assigned_role',$fields);
    }
    public function down()
    {

    }
}