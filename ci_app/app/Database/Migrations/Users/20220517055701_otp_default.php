<?php

namespace App\Database\Migrations\Users;

use CodeIgniter\Database\Migration;

class OtpDefault extends Migration
{
    public function up()
    {
        $fields = [
            'OTP' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'default' => '1111',
            ],
        ];
        $this->forge->modifyColumn('users',$fields);
    }
    public function down()
    {

    }
}