<?php

$routes->group("api",['filter' => 'jwtauth',"namespace" => "\Modules\Section\Controllers\Api"], function ($routes) {
    $routes->group("section", function ($routes) {
        $routes->post('save', 'Section::save');
        $routes->get('list', 'Section::getList');
        $routes->get('delete', 'Section::deleteSection');
        $routes->get('list-schoolid', 'Section::getListBySchoolId');   //For Mobile
            $routes->group("teachers", function ($routes) {
                $routes->get('', 'Section::getTeacherList');
                $routes->post('add', 'Section::addTeacherToSection');
                $routes->get('remove', 'Section::removeTeacherFromSection');
        });
    });
});