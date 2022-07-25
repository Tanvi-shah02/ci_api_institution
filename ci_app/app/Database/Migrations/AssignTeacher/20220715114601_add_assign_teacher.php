<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AssignedTeachers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'sectionId'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'teacherId'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'typeId'       => [
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
        $this->forge->createTable('assigned_teacher');
    }
    public function down()
    {
        $this->forge->dropTable('assigned_teacher');
    }
}