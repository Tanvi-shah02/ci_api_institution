<?php

$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\Students\Controllers\Api"], function ($routes) {
    $routes->group("students", function ($routes) {
        //$routes->get('profile', 'School::profile');
        $routes->post('save', 'Students::save');
    });
});