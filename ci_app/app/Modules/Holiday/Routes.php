<?php

$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\Holiday\Controllers\Api"], function ($routes) {
    $routes->group("holiday", function ($routes) {
        $routes->post('save', 'Holiday::save');
        $routes->get('list', 'Holiday::getList');
    });
});