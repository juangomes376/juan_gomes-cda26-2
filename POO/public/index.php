<?php
require_once __DIR__ . '/../src/Class/Router.php';

$router = new Router();


$router->get('/', function() {
    echo "Salut vous etes sur la page d'accueil !";
});


// $router->get('/contato', ['ContatoController', 'index']);

$router->get('/contato', function() {
    echo "Salut vous etes sur la page de contact !";
});


$router->resolve();