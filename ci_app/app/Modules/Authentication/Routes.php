<?php

$routes->group("api", ["namespace" => "\Modules\Authentication\Controllers\Api"], function ($routes) {
    $routes->group("authentication", function ($routes) {
        $routes->post('mobile-no', 'Authentication::login_mobile_no');
        $routes->post('otp', 'Authentication::login_otp');
    });
});

//$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\Authentication\Controllers\Api"], function ($routes) {
//    $routes->group("authentication", function ($routes) {
//        $routes->post('test', 'Authentication::test');
//
//    });
//});

