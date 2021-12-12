<?php

// Example implementation

require 'skibidy.php';
$router = new Router();

$auth = function($req, $res) {
  $token = $req->header('AUTHORIZATION');
  if(!$token || $token !== 'secret') {
    http_response_code(401); exit;
  }
};

$router->get('/', fn($req, $res) => $res->json(['msg'=>'Hello!']));
$router->get('/api', $auth, fn($req, $res) => $res->json(['version'=>1.0]));
$router->get('/api/:id', $auth, fn($req, $res) => $res->json($req->params));
$router->get('/api/:id/thing/:tid', $auth, fn($req, $res) => $res->json($req->params));

$router->run();