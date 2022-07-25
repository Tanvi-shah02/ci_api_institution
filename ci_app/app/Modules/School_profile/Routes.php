<?php

$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\School_profile\Controllers\Api"], function ($routes) {
    $routes->group("school", function ($routes) {
        $routes->get('profile', 'School::profile');
        $routes->post('profile/save', 'School::save');
    });
});