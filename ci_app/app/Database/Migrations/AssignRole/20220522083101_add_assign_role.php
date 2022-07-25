<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AssignedRoles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'userId'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'roleId'       => [
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
        $this->forge->createTable('assigned_role');
    }
    public function down()
    {
        $this->forge->dropTable('assigned_role');
    }
}