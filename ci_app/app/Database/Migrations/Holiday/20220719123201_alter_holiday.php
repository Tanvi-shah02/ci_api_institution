<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class AlterColumnInHolidays extends Migration
{
    public function up()
    {
        $fields = [
            'startDate'       => [
                'type'       => 'DATE',
            ],
            'endDate'       => [
                'type'       => 'DATE',
            ],
        ];
        $this->forge->modifyColumn('holidays',$fields);
    }
    public function down()
    {

    }
}