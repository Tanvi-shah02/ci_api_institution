<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddColInHoliday extends Migration
{
    public function up()
    {
        $fields = [
            'schoolId' => [
                'type'       => 'INT',
                'constraint' => '11',
                'after' => 'id'
            ],
        ];
        $this->forge->addColumn('holidays',$fields);
    }
    public function down()
    {

    }
}