<?php

$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\Users\Controllers\Api"], function ($routes) {
    $routes->group("users", function ($routes) {
        $routes->get('profile', 'Users::profile');
        $routes->get('school-list', 'Users::schoolList');
    });
});