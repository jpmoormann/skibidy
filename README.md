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

This will return `{"msg":"Hello!"}`.

To make the route dynamic, use the `:{parameter name}` syntax in the route string:
```php
$router->get('/:id', fn($req, $res) => $req->json($req->params));
```

Now, this will return `{"id":"123"}` as a response if the request was `/123`.

And that's pretty much it! For more info, check the docs below.

## Docs
### `Route`
Defines a route on the Router instance. Takes a string for the method, a string for the route, and an array spread of functions for middleware or callbacks.

**Properties**
- `method` String
- `route` String
- `callbacks` Array

### `Router`
Handles matching the current request against a collection of routes. Takes an optional string for a base route to prepend to all routes.

**Properties**
- `routes` Array

**Methods**
- `get()`
  - Adds a GET route. Takes a string for the route to match on, and an array spread of functions for middleware or callbacks
- `post()`
  - Adds a POST route. Takes a string for the route to match on, and an array spread of functions for middleware or callbacks
- `put()`
  - Adds a PUT route. Takes a string for the route to match on, and an array spread of functions for middleware or callbacks
- `patch()`
  - Adds a PATCH route. Takes a string for the route to match on, and an array spread of functions for middleware or callbacks
- `delete()`
  - Adds a DELETE route. Takes a string for the route to match on, and an array spread of functions for middleware or callbacks
- `use()`
  - Adds an existing Router instance to nest its routes under a route prefix. Takes a string for the route prefix to be prepended on each route, a Router instance to use for the nested routes, and an array spread of functions for any additional middleware/callbacks.
- `run()`
  - Iterates over all routes to match on the current request. Takes no arguments. If no route can be matched, terminates with a 404 response.

### `Request`
Takes an optional string for a base route to prepend to all routes

**Properties**
- `method` String
- `route` String
- `params` Object
  - Contains any available dynamic route parameters. Is empty by default.

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