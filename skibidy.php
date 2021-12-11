<?php
class Route {
  public $method;
  public $route;
  public $callbacks;
  function __construct($m, $r, $c) {
    $this->method = $m;
    $this->route = $r;
    $this->callbacks = is_array($c) ? $c : [$c];
  }
}
class Request {
  public $method;
  public $route;
  public $params;
  public $headers;
  function __construct($base = '') {
    $this->route = str_replace($base,'',$_SERVER['REQUEST_URI']);
    $this->method = $_SERVER['REQUEST_METHOD'];
  }
  function header($k) {
    return isset($_SERVER["HTTP_$k"]) ? $_SERVER["HTTP_$k"] : null;
  }
  function body() {
    return match($_SERVER['HTTP_CONTENT_TYPE']) {
      'application/json' => json_decode(file_get_contents('php://input')),
      default => file_get_contents('php://input')
    };
  }
}
class Response {
  function json($d) {
    header('Content-Type: application/json');
    echo json_encode($d);
    exit;
  }
}
class Router {
  public $base = '';
  public $routes;
  function __construct() {
    $this->routes = [];
  }
  function use($u, Router $r, ...$c) {
    foreach($r->routes as &$sr) {
      $sr->route = rtrim($u.$sr->route,'/');
      if($c) $sr->callbacks = array_merge($c, $sr->callbacks);
    }
    $this->routes = array_merge($this->routes, $r->routes);
  }
  function get($u, ...$c) {
    $this->routes[] = new Route('GET', $u, $c);
  }
  function post($u, ...$c) {
    $this->routes[] = new Route('POST', $u, $c);
  }
  function put($u, ...$c) {
    $this->routes[] = new Route('PUT', $u, $c);
  }
  function patch($u, ...$c) {
    $this->routes[] = new Route('PATCH', $u, $c);
  }
  function delete($u, ...$c) {
    $this->routes[] = new Route('DELETE', $u, $c);
  }
  function run() {
    $req = new Request($this->base);
    $res = new Response();
    foreach($this->routes as $r) {
      if($r->method == $req->method) {
        if($r->route == $req->route) {
          foreach($r->callbacks as $c) ($c)($req, $res);
        }
      }
    }
    http_response_code(404); exit;
  }
}