<?php 
/* phpinfo(); */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$loader = require '../vendor/autoload.php';
    $loader->register();

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    require '../src/Kernel.php';
  
    $request = Request::createFromGlobals();
    
    // Our framework is now handling itself the request
    $app = new Erahma\FutureFramework\Kernel();
    
    $app->map('/hello/{name}', function ($name) {
		return new Response('Hello '.$name);
	});
    
    $response = $app->handle($request);
    $response->send();