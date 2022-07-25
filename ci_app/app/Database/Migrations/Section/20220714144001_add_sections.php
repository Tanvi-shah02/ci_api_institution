<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddSection extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => true,
            ],
            'schoolId'          => [
                'type'           => 'INT',
                'constraint'     => '11',
            ],
            'classId'          => [
                'type'           => 'INT',
                'constraint'     => '11',
            ],
            'section'          => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
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
        $this->forge->createTable('section_details');
    }
    public function down()
    {
        $this->forge->dropTable('section_details');
    }
}