<?php 
/* phpinfo(); */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$loader = require '../vendor/autoload.php';
    $loader->register();

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    use Erahma\FutureFramework\Event\RequestEvent;

    require '../src/Kernel.php';
  
    $request = Request::createFromGlobals();
    
    
    $app = new Erahma\FutureFramework\Kernel();
    
    $app->map('/', function () {
        $response = new Response();
        $response->setContent(json_encode([
            'code' => 200,
            'message' => 'Welcome !!',
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	});

    $app->map('/hello/{name}', function ($name) {
		return new Response('Hello '.$name);
	});

    $app->on('request', function (RequestEvent $event) {
        
		// let's assume a proper check here
		if ('/admin' == $event->getRequest()->getPathInfo()) {
			echo 'Access Denied Admin!';
			exit;
		}
		if (str_starts_with($event->getRequest()->getPathInfo(), '/api/v1')) {
			echo 'Access Denied!';
			exit;
		}
	});
    
    $response = $app->handle($request);
    $response->send();