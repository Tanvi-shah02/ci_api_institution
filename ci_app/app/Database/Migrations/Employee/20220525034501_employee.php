<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddEmployee extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => true,
            ],
            'userId'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'empNo'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'designation'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'createdAt'       => [
                'type'       => 'DATETIME',
            ],
            'updatedAt'       => [
                'type'       => 'DATETIME',
            ],
            'deletedAt'       => [
                'type'       => 'DATETIME',
            ],

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('employee_details');
    }
    public function down()
    {
        $this->forge->dropTable('employee_details');
    }
}