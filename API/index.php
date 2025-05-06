<?php
require_once __DIR__ . '/../Router/Router.php'; // ou Core/Router.php selon ton projet
require_once __DIR__ . '/../Config/database.php'; // si besoin

$router = new Router();

// Ajout de la route de suggestion de noms
$router->add('GET', '/api/suggest-names', 'UserController@suggestNames');

// Dispatcher selon la requête
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
