<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'firstName'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'middleName'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'lastName'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'mobileNo'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'email'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'OTP'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'profilePic'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'isActive'       => [
                'type'       => 'INT',
                'constraint' => '1',
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
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}