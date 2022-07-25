<?php

$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\Classes\Controllers\Api"], function ($routes) {
    $routes->group("classes", function ($routes) {
        $routes->get('', 'Classes::lists');
        $routes->get('list', 'Classes::lists');
        $routes->post('save', 'Classes::save');
        $routes->post('delete', 'Classes::delete');
    });
});