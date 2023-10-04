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

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    use Symfony\Component\Dotenv\Dotenv;

    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/../.env');

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

    $app->map('/test', function () {
		return new Response('Hello ');
	});
    $app->map('/api/v1', function () {
		return new Response('elllo api ');
	});

    $app->on('request', function (RequestEvent $event) {

        require '../secret.php';
        $secretKey = new Key($publicKey, 'RS256');
        
		// let's assume a proper check here
		if ('/admin' == $event->getRequest()->getPathInfo()) {
			echo 'Access Denied Admin!';
			exit;
		}
		if (str_starts_with($event->getRequest()->getPathInfo(), '/api/v1')) {
            
            if (! preg_match('/Bearer\s(\S+)/', $event->getRequest()->headers->get('Authorization')??'', $matches)) {
                header('HTTP/1.0 400 Bad Request');
                echo 'Token not found in request';
                exit;
            }
			$jwt = $matches[1]??false;

            $token = JWT::decode($jwt, $secretKey);
            $now = new DateTimeImmutable();
            $serverName = "example.org";
            
            if (($token?->iss??'') !== $serverName
                 /* || $token?->nbf??'' > $now->getTimestamp() 
                 || $token?->exp??0 < $now->getTimestamp() */
                )
            {
                header('HTTP/1.1 401 Unauthorized');
                echo '401 Unauthorized';
                exit;
            }
            
		}

		
        if (str_starts_with($event->getRequest()->getPathInfo(), '/api/test')) {

            $payload = [
                'iss' => 'example.org',
                'aud' => 'example.com',
                'iat' => 1356999524,
                'nbf' => 1357000000,
                'data'=> [
                    'email'=> 'test@gmail.com',
                    'message'=> 'your message'
                ]
            ];

            $jwt = JWT::encode($payload, $privateKey, 'RS256');
            echo "Encode:\n" . print_r($jwt, true) . "\n";

            $decoded = JWT::decode($jwt, $secretKey);

            /*
            NOTE: This will now be an object instead of an associative array. To get
            an associative array, you will need to cast it as such:
            */

            $decoded_array = (array) $decoded;
            echo "Decode:\n" . print_r($decoded_array, true) . "\n";
            /* ================ */
			exit;
		}
	});
    
    $response = $app->handle($request);
    $response->send();