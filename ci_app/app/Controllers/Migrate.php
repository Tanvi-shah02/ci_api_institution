<?php

namespace App\Controllers;

class Migrate extends \CodeIgniter\Controller
{
    public function index()
    {
        $migrate = \Config\Services::migrations();

        try {
            //$migrate->latest();
            $res = $migrate->setNamespace(null)->latest();
            echo $res;
        } catch (\Throwable $e) {
            echo $e;
            // Do something with the error here...
        }
    }
}