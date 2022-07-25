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
            'schoolId'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'enrollmentNo'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'firstName'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'lastName'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'dob'       => [
                'type'       => 'DATE',
            ],
            'addressLine1'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'addressLine2'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'state'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'city'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'zipCode'       => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'class'       => [
                'type'       => 'INT',
                'constraint' => '11',
            ],
            'phoneNo'       => [
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
        $this->forge->createTable('students');
    }
    public function down()
    {
        $this->forge->dropTable('students');
    }
}