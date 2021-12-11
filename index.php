<?php

require 'a.php';
require 'config.php';
$router = new Router();

$auth = function($req, $res) {
  $token = $req->header('AUTHORIZATION');
  if(!$token || $token !== 'secret') {
    http_response_code(401); exit;
  }
};

$router->get('/', fn($req, $res) => $res->json(['msg'=>'Hello!']));
$router->get('/api', $auth, fn($req, $res) => $res->json(['version'=>1.0]));

$router->run();