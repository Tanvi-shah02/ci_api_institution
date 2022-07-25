<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class StudentMapping extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'studentId'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'userId'       => [
                'type'       => 'INT',
                'constraint' => '11',
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
        $this->forge->createTable('student_mapping');
    }
    public function down()
    {
        $this->forge->dropTable('student_mapping');
    }
}