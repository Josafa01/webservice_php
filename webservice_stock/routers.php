<?php
global $routes;
$routes = array();

// User
$routes['/user/login'] = '/user/login';
$routes['/user/new'] = '/user/new_user';
$routes['/user/{id}/edit_user'] = '/user/edit_user/:id';
$routes['/user/{id}/get_prod'] = '/user/get_prod/:id';

// Products
$routes['/products/new'] = '/products/new_prod';
$routes['/products/{id}/edit_prod'] = '/products/edit_prod/:id';

