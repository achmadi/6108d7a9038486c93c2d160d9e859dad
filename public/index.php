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

            /* ================ */
            $privateKey = <<<EOD
            -----BEGIN RSA PRIVATE KEY-----
            MIIEowIBAAKCAQEAuzWHNM5f+amCjQztc5QTfJfzCC5J4nuW+L/aOxZ4f8J3Frew
            M2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJhzkPYLae7bTVro3hok0zDITR8F6S
            JGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548tu4czCuqU8BGVOlnp6IqBHhAswNMM
            78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vSopcT51koWOgiTf3C7nJUoMWZHZI5
            HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTzTTqo1SCSH2pooJl9O8at6kkRYsrZ
            WwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/BwQIDAQABAoIBAFtGaOqNKGwggn9k
            6yzr6GhZ6Wt2rh1Xpq8XUz514UBhPxD7dFRLpbzCrLVpzY80LbmVGJ9+1pJozyWc
            VKeCeUdNwbqkr240Oe7GTFmGjDoxU+5/HX/SJYPpC8JZ9oqgEA87iz+WQX9hVoP2
            oF6EB4ckDvXmk8FMwVZW2l2/kd5mrEVbDaXKxhvUDf52iVD+sGIlTif7mBgR99/b
            c3qiCnxCMmfYUnT2eh7Vv2LhCR/G9S6C3R4lA71rEyiU3KgsGfg0d82/XWXbegJW
            h3QbWNtQLxTuIvLq5aAryV3PfaHlPgdgK0ft6ocU2de2FagFka3nfVEyC7IUsNTK
            bq6nhAECgYEA7d/0DPOIaItl/8BWKyCuAHMss47j0wlGbBSHdJIiS55akMvnAG0M
            39y22Qqfzh1at9kBFeYeFIIU82ZLF3xOcE3z6pJZ4Dyvx4BYdXH77odo9uVK9s1l
            3T3BlMcqd1hvZLMS7dviyH79jZo4CXSHiKzc7pQ2YfK5eKxKqONeXuECgYEAyXlG
            vonaus/YTb1IBei9HwaccnQ/1HRn6MvfDjb7JJDIBhNClGPt6xRlzBbSZ73c2QEC
            6Fu9h36K/HZ2qcLd2bXiNyhIV7b6tVKk+0Psoj0dL9EbhsD1OsmE1nTPyAc9XZbb
            OPYxy+dpBCUA8/1U9+uiFoCa7mIbWcSQ+39gHuECgYAz82pQfct30aH4JiBrkNqP
            nJfRq05UY70uk5k1u0ikLTRoVS/hJu/d4E1Kv4hBMqYCavFSwAwnvHUo51lVCr/y
            xQOVYlsgnwBg2MX4+GjmIkqpSVCC8D7j/73MaWb746OIYZervQ8dbKahi2HbpsiG
            8AHcVSA/agxZr38qvWV54QKBgCD5TlDE8x18AuTGQ9FjxAAd7uD0kbXNz2vUYg9L
            hFL5tyL3aAAtUrUUw4xhd9IuysRhW/53dU+FsG2dXdJu6CxHjlyEpUJl2iZu/j15
            YnMzGWHIEX8+eWRDsw/+Ujtko/B7TinGcWPz3cYl4EAOiCeDUyXnqnO1btCEUU44
            DJ1BAoGBAJuPD27ErTSVtId90+M4zFPNibFP50KprVdc8CR37BE7r8vuGgNYXmnI
            RLnGP9p3pVgFCktORuYS2J/6t84I3+A17nEoB4xvhTLeAinAW/uTQOUmNicOP4Ek
            2MsLL2kHgL8bLTmvXV4FX+PXphrDKg1XxzOYn0otuoqdAQrkK4og
            -----END RSA PRIVATE KEY-----
            EOD;

            $publicKey = <<<EOD
            -----BEGIN PUBLIC KEY-----
            MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuzWHNM5f+amCjQztc5QT
            fJfzCC5J4nuW+L/aOxZ4f8J3FrewM2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJ
            hzkPYLae7bTVro3hok0zDITR8F6SJGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548t
            u4czCuqU8BGVOlnp6IqBHhAswNMM78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vS
            opcT51koWOgiTf3C7nJUoMWZHZI5HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTz
            TTqo1SCSH2pooJl9O8at6kkRYsrZWwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/B
            wQIDAQAB
            -----END PUBLIC KEY-----
            EOD;
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