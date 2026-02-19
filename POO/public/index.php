<?php

require_once __DIR__ . '/../vendor/autoload.php';


$router = new \App\Router();


$router->get('/', function() {
    echo "Salut vous etes sur la page d'accueil !";
});


// $router->get('/contato', ['ContatoController', 'index']);

$router->get('/contact', function() {
    echo "Salut vous etes sur la page de contact !";
});


$router->resolve();