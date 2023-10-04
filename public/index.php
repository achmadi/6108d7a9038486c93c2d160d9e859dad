<?php 
/* phpinfo(); */

$loader = require '../vendor/autoload.php';
    $loader->register();

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    
    $request = Request::createFromGlobals();
    $response = new Response();
    
    switch ($request->getPathInfo()) {
    case '/':
    	$response->setContent('This is the website home');
    	break;
    
    	case '/about':
    		$response->setContent('This is the about page');
    		break;
    
    	default:
    		$response->setContent('Not found !');
    	$response->setStatusCode(Response::HTTP_NOT_FOUND);
    }
    
    $response->send();