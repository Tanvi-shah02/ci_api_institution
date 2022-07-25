<?php

$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\Employee\Controllers\Api"], function ($routes) {
    $routes->group("employee", function ($routes) {
        $routes->post('save', 'Employee::save');
        $routes->get('list', 'Employee::getList');
        $routes->get('delete', 'Employee::deleteEmp');
        $routes->get('validate', 'Employee::empValidateByMobile');
    });
});