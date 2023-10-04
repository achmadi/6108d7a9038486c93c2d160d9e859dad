<?php 
/* phpinfo(); */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$loader = require '../vendor/autoload.php';
    $loader->register();

    use Symfony\Component\HttpFoundation\Request;

    require '../src/Kernel.php';
  
    $request = Request::createFromGlobals();
    
    // Our framework is now handling itself the request
    $app = new Erahma\FutureFramework\Kernel();
    
    
    $response = $app->handle($request);
    $response->send();