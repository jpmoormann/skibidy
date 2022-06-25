<?php
class Route
{
  public $method;
  public $route;
  public $callbacks;
  function __construct(string $m, string $r, array $c)
  {
    $this->method = $m;
    $this->route = $r;
    $this->callbacks = $c;
  }
}
class Request
{
  public $method;
  public $route;
  public $params;
  public $query;
  function __construct(string $base = '')
  {
    $route = explode('?', str_replace($base, '', $_SERVER['REQUEST_URI']));
    $this->route = $route[0];
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->query = (object)[];
    $this->params = (object)[];
    if (isset($route[1])) $this->parseQuery($route[1]);
  }
  private function parseQuery(string $q)
  {
    $parts = explode('&', $q);
    foreach ($parts as $p) {
      $kv = explode('=', $p);
      if (isset($kv[1])) $this->query->{$kv[0]} = $kv[1];
    }
  }
  function header(string $k)
  {
    return isset($_SERVER["HTTP_$k"]) ? $_SERVER["HTTP_$k"] : null;
  }
  function body()
  {
    return match ($_SERVER['HTTP_CONTENT_TYPE']) {
      'application/json' => json_decode(file_get_contents('php://input')),
      default => file_get_contents('php://input')
    };
  }
  function files($key)
  {
    return (object)($_FILES[$key]);
  }
}
class Response
{
  function send(mixed $d, string $t, int $c = 200)
  {
    header("Content-Type: $t");
    http_response_code($c);
    echo match ($t) {
      'application/json' => json_encode($d, JSON_UNESCAPED_SLASHES),
      'text/html' => $d,
      default => $d
    };
    exit;
  }
  function json(mixed $d, int $c = 200)
  {
    echo $this->send($d, 'application/json', $c);
  }
  function file(string $f, int $c = 200)
  {
    http_response_code($c);
    echo file_get_contents($f);
    exit;
  }
  function status(int $code = 200)
  {
    http_response_code($code);
    exit;
  }
}
class Router
{
  private $base;
  public $routes;
  function __construct(string $b = '')
  {
    $this->base = $b;
    $this->routes = [];
  }
  function get(string $u, mixed ...$c)
  {
    $this->routes[] = new Route('GET', $u, $c);
  }
  function post(string $u, mixed ...$c)
  {
    $this->routes[] = new Route('POST', $u, $c);
  }
  function put(string $u, mixed ...$c)
  {
    $this->routes[] = new Route('PUT', $u, $c);
  }
  function patch(string $u, mixed ...$c)
  {
    $this->routes[] = new Route('PATCH', $u, $c);
  }
  function delete(string $u, mixed ...$c)
  {
    $this->routes[] = new Route('DELETE', $u, $c);
  }
  function use(string $u, Router $r, mixed ...$c)
  {
    foreach ($r->routes as &$sr) {
      $sr->route = rtrim($u . $sr->route, '/');
      if ($c) $sr->callbacks = array_merge($c, $sr->callbacks);
    }
    $this->routes = array_merge($this->routes, $r->routes);
  }
  private function matchUri(string $s, string $t)
  {
    if (str_contains($t, ':') && substr_count($s, '/') == substr_count($t, '/')) {
      $p = (object)[];
      $sp = explode('/', $s);
      $tp = explode('/', $t);
      for ($i = 0; $i < count($tp); $i++) {
        if ($tp[$i] && $tp[$i][0] == ':') $p->{substr($tp[$i], 1)} = $sp[$i];
        elseif ($tp[$i] !== $sp[$i]) return false;
      }
      return $p;
    }
    return null;
  }
  function run()
  {
    $req = new Request($this->base);
    $res = new Response();
    foreach ($this->routes as $r) {
      if ($r->method == $req->method) {
        if ($r->route == $req->route) {
          foreach ($r->callbacks as $c) ($c)($req, $res);
        } elseif ($p = $this->matchUri($req->route, $r->route)) {
          $req->params = $p;
          foreach ($r->callbacks as $c) ($c)($req, $res);
        }
      }
    }
    http_response_code(404);
    exit;
  }
}
