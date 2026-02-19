<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\User;


$router = new \App\Router();


$router->get('/', function() {
    echo "Salut vous etes sur la page d'accueil !";
});

$router->get('/userall', [User::class, 'all']);

$router->get('/contact', function() {
    echo "Salut vous etes sur la page de contact !";
});

$router->get('/contact/{id}/', function($id) {
    echo "Salut vous etes sur la page de contact avec l'ID : $id";
});

$router->resolve();