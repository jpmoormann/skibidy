# skibidy
Simplest PHP, Express-like API router ever

## Getting Started
Just require/include `skibidy.php` into the root file of your endpoint. Only thing you need to make sure of is that your server redirects all routes to that file, i.e. via .htaccess if on apache.

If you've used Express.js before, it's basically that, just with some variations.

To start, make a new Router instance:
```php
$router = new Router();
```

Then, add a route with a callback:
```php
$router->get('/', fn($req, $res) => $res->json(['msg'=>'Hello!']));
```

Finally, run it:
```php
$router->run();
```

And that's it! If you hit that route, you'll receive a JSON response.

To do more, check the docs below.

## Docs

### `Router`

Handles matching the current request against a collection of routes. Takes an optional string for a base route to prepend to all routes

**Methods**

- `get(string $u, array ...$c)`
  - Adds a GET route. Takes a string for the route to match on, and an array spread of functions for middleware and callbacks
- `post(string $u, array ...$c)`
  - Adds a POST route. Takes a string for the route to match on, and an array spread of functions for middleware and callbacks
- `put(string $u, array ...$c)`
  - Adds a PUT route. Takes a string for the route to match on, and an array spread of functions for middleware and callbacks
- `patch(string $u, array ...$c)`
  - Adds a PATCH route. Takes a string for the route to match on, and an array spread of functions for middleware and callbacks
- `delete(string $u, array ...$c)`
  - Adds a DELETE route. Takes a string for the route to match on, and an array spread of functions for middleware and callbacks
- `use(string $u, Router $r, array ...$c)`
  - Adds an existing Router instance to nest its routes under a route prefix. Takes a string for the route prefix to be prepended on each route, a Router instance to use for the nested routes, and an array spread of functions for any additional middleware/callbacks.
- `run()`
  - Iterates over all routes to match on the current request. Takes no arguments. If no route can be matched, terminates with a 404 response.

### `Request`

Takes an optional string for a base route to prepend to all routes

**Methods**

- `header(string $k)`
  - Retrieve a request header's value by key.
- `body()`
  - Return the request payload body, parsed based on Content-Type header.

### `Response`

Takes no arguments

**Methods**

- `send(any $d, string $t)`
  - Returns a terminating response with a value. Takes any data type to return, and a string for the Content-Type.
- `json(object $d)`
  - Returns a terminating JSON response with a value. Takes an object to return.

## Examples

See the `index.php` file for a simple example to help get started

## Known Issues & Limitations

- Kinda slow, but eh, it's interpreted
- No async/await, sorry