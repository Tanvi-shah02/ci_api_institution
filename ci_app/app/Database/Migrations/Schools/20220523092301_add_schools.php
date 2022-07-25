<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddSchools extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'schoolName'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'address'       => [
                'type'       => 'TEXT',
            ],
            'city'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'state'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'zipcoce'       => [
                'type'       => 'VARCHAR',
                'constraint' => '11',
            ],
            'logo'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'isActive'       => [
                'type'       => 'INT',
                'constraint' => '1',
                'default'    => 1,
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
        $this->forge->createTable('schools');
    }
    public function down()
    {
        $this->forge->dropTable('schools');
    }
}