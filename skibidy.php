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
  function __construct($base = '') {
    $this->route = str_replace($base,'',$_SERVER['REQUEST_URI']);
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->params = (object)[];
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
  function send($d, $t) {
    header("Content-Type: $t");
    echo match($t) {
      'application/json' => json_encode($d),
      'text/html' => $d,
      default => $d
    };
    exit;
  }
  function json($d) {
    echo $this->send($d, 'application/json');
  }
  function file($p) {
    echo file_get_contents($p);
    exit;
  }
}
class Router {
  private $base;
  public $routes;
  function __construct(string $b = '') {
    $this->base = $b;
    $this->routes = [];
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
  function use($u, Router $r, ...$c) {
    foreach($r->routes as &$sr) {
      $sr->route = rtrim($u.$sr->route,'/');
      if($c) $sr->callbacks = array_merge($c, $sr->callbacks);
    }
    $this->routes = array_merge($this->routes, $r->routes);
  }
  private function match($s, $t) {
    if(str_contains($t,':') && substr_count($s,'/') == substr_count($t,'/')) {
      $p = (object)[];
      $sp = explode('/',$s);
      $tp = explode('/',$t);
      for($i=0;$i<count($tp);$i++) {
        if($tp[$i] && $tp[$i][0] == ':') $p->{substr($tp[$i],1)} = $sp[$i];
      }
      return $p;
    }
    return null;
  }
  function run() {
    $req = new Request($this->base);
    $res = new Response();
    foreach($this->routes as $r) {
      if($r->method == $req->method) {
        if($r->route == $req->route) {
          foreach($r->callbacks as $c) ($c)($req, $res);
        } elseif($p = $this->match($req->route, $r->route)) {
          $req->params = $p;
          foreach($r->callbacks as $c) ($c)($req, $res);
        }
      }
    }
    http_response_code(404); exit;
  }
}