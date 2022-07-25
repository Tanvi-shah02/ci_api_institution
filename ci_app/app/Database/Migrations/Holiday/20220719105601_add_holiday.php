<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddHolidays extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'holidayName'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
            ],
            'startDate'       => [
                'type'       => 'DATETIME',
            ],
            'endDate'       => [
                'type'       => 'DATETIME',
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
        $this->forge->createTable('holidays');
    }
    public function down()
    {
        $this->forge->dropTable('holidays');
    }
}

