# skibidy
Simplest PHP, Express-like API router ever

## get started
Easy; just require or include skibidy.php into the root file of your endpoint. Only thing you need to make sure of is that your server redirects all routes to that file, i.e. via .htaccess if on apache.

## how it works
If you've used Express.js before, it's basically that with a few differences.

If not, no worries. Just make a new Router instance:

`$router = new Router();`

and then add a route with a callback:

`$router->get('/', fn($req, $res) => $res->json(['msg'=>'Hello!']));`

finally, run it:
`$router->run();`

and that's pretty much it. If you hit that endpoint, you'll receive a JSON response.

## cool! now what?

Now you can add more routes with different methods and routes and callbacks.

### supported methods

`GET,POST,PUT,PATCH,DELETE`

I'll work on OPTIONS and HEAD if anyone cares

### callbacks

Similar to Express, the callbacks are a spread argument, so you can tack on multiple "middleware" callbacks for things like authentication and prerequisites.

```
$auth = fn($req, $res) => $req->header('AUTHORIZATION') == 'token' ? true : $res->code(401);
$router->post('/api', $authFunc, fn($req, $res) => ...);
```

### nested routes

You can nest routers in each other using the `use()` function:

```
$router = new Router();
$api = new Router();
$router->use('/api', $api);
```

# known issues & limitations

- Kinda slow, but eh, it's interpreted
- No async/await, sorry