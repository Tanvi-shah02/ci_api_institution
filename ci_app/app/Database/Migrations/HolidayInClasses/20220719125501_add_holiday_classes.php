<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AddHolidayClasses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'holidayId'          => [
                'type'           => 'INT',
                'constraint'     => '11',
            ],
            'classId'          => [
                'type'           => 'INT',
                'constraint'     => '11',
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
        $this->forge->createTable('holiday_classes');
    }
    public function down()
    {
        $this->forge->dropTable('holiday_classes');
    }
}