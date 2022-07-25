<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddClasses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'schoolId'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'className'      => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'isActive'       => [
                'type'       => 'INT',
                'constraint' => '1',
                'default'    => 1,
            ],
            'createdBy'       => [
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
        $this->forge->createTable('classes');
    }
    public function down()
    {
        $this->forge->dropTable('classes');
    }
}