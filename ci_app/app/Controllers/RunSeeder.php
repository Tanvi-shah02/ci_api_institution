<?php

namespace App\Controllers;

class RunSeeder extends \CodeIgniter\Controller
{
    public function index()
    {
        $seeder = \Config\Database::seeder();

        try {
            //$migrate->latest();
            $res = $seeder->call('AllSeeder');
            echo $res;
        } catch (\Throwable $e) {
            echo $e;
            // Do something with the error here...
        }
    }
}