<?php 
/* phpinfo(); */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$loader = require '../vendor/autoload.php';
    $loader->register();

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\RouteCollection;
    use Symfony\Component\HttpFoundation\Response;

    use Erahma\FutureFramework\Event\RequestEvent;

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    use Firebase\JWT\SignatureInvalidException;
    use Firebase\JWT\BeforeValidException;
    use Firebase\JWT\ExpiredException;
    use DomainException;
use Erahma\FutureFramework\Models\User;
use InvalidArgumentException;
    use UnexpectedValueException;


    use Symfony\Component\Dotenv\Dotenv;

    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/../.env');

    require '../src/Kernel.php';
  
    $request = Request::createFromGlobals();
    
    
    $app = new Erahma\FutureFramework\Kernel(
        [
            'driver' =>  $_ENV['DB_CONNECTION'],
            // 'host' =>  $_ENV['DB_HOST'].':'.$_ENV['DB_PORT'],
            'host' =>  $_ENV['DB_HOST'],
            'database' =>  $_ENV['DB_DATABASE'],
            'username' =>  $_ENV['DB_USERNAME'],
            'password' =>  $_ENV['DB_PASSWORD'],
        ]
    );
    
    $app->map('/', function ($data) {
        
        $response = new Response();
        $response->setContent(json_encode([
            'code' => 200,
            'message' => 'Welcome !!',
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	});

    /* $app->map('/hello/{name}', function ($name) {
		return new Response('Hello '.$name);
	}); */

    $app->map('/refresh-api-key', function ( $data) {
        $result = [ 'code' => 400, 'api_key' => null, 'message' => 'failed', ];
        $user = User::
            where('email', $data['email']??'')
            ->where('password', $data['password']??'')
            ->first();

        if ($user) {
            $user->api_key = uniqid();
            $user->save();
            $result = [ 'code' => 200, 'api_key' => $user->api_key, 'message' => 'success', ];
        }

		$response = new Response();
        $response->setContent(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}, [], 'POST');

    $app->map('/generate-token/{api_key}', function ($api_key, $data) {

        require '../secret.php';
        $user = User::where('api_key', $api_key)->first();

        $result = [ 'code' => 200, 'token' => null, 'message' => null, ];

        if ($user) {

            $issuedAt   = new DateTimeImmutable();
            $expire     = (clone $issuedAt)->modify('+'.((int) ($data['duration']??1)).' minutes')->getTimestamp();      // Add 60 seconds
            
            $username   = $user->email;                                           // Retrieved from filtered POST data
    
            $payload = [
                'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
                'iss'  => $serverName,                       // Issuer
                'nbf'  => $issuedAt->getTimestamp(),         // Not before
                'exp'  => $expire,                           // Expire
                'userName' => $username,                     // User name
            ];
    
            $jwt = JWT::encode($payload, $privateKey, 'RS256');
            $result['token'] = $jwt;
            $result['message'] = 'success';
            
            $response = new Response();
            $response->setContent(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $result['code'] = 400;
        $result['message'] = 'Uknown api key';

        $response = new Response();
        $response->setContent(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

	}, [], ['POST']);

    $app->map('/api/v1', function ($data) {
		return new Response('Hello api ');
	}, ['auth']);


    $app->registerMiddleware('auth', function (Request $request, RouteCollection $route )  {
        require '../secret.php';
        $secretKey = new Key($publicKey, 'RS256');
        
		if (str_starts_with($request->getPathInfo(), '/api/v1')) {
            
            if (! preg_match('/Bearer\s(\S+)/', $request->headers->get('Authorization')??'', $matches)) {
                header('HTTP/1.0 400 Bad Request');
                echo 'Token not found in request';
                exit;
            }
			
            try {
                $jwt = $matches[1]??false;

                $token = JWT::decode($jwt, $secretKey);
                $now = new DateTimeImmutable();
                
                return true;
                /* if (($token?->iss??'') !== $serverName
                    || $token?->nbf??'' > $now->getTimestamp() 
                    || $token?->exp??0 < $now->getTimestamp() 
                    )
                {
                    header('HTTP/1.1 401 Unauthorized');
                    echo '401 Unauthorized';
                    exit;
                } */
            } catch (InvalidArgumentException $e) {
                header('HTTP/1.1 401 Unauthorized');
                echo '401 Unauthorized InvalidArgumentException';
                exit;
                // provided key/key-array is empty or malformed.
            } catch (DomainException $e) {

                header('HTTP/1.1 401 Unauthorized');
                echo '401 Unauthorized DomainException';
                exit;
                // provided algorithm is unsupported OR
                // provided key is invalid OR
                // unknown error thrown in openSSL or libsodium OR
                // libsodium is required but not available.
            } catch (SignatureInvalidException $e) {

                header('HTTP/1.1 401 Unauthorized');
                echo '401 Unauthorized SignatureInvalidException';
                exit;
                // provided JWT signature verification failed.
            } catch (BeforeValidException $e) {
                header('HTTP/1.1 401 Unauthorized');
                echo '401 Unauthorized BeforeValidException';
                exit;

                // provided JWT is trying to be used before "nbf" claim OR
                // provided JWT is trying to be used before "iat" claim.
            } catch (ExpiredException $e) {
                
                header('HTTP/1.1 401 Unauthorized');
                echo '401 Unauthorized ExpiredException';
                exit;
                // provided JWT is trying to be used after "exp" claim.
            } catch (UnexpectedValueException $e) {
                header('HTTP/1.1 401 Unauthorized');
                echo '401 Unauthorized UnexpectedValueException';
                exit;
                // provided JWT is malformed OR
                // provided JWT is missing an algorithm / using an unsupported algorithm OR
                // provided JWT algorithm does not match provided key OR
                // provided key ID in key/key-array is empty or invalid.
            }
            
		}
        
    });

    $app->on('request', function (RequestEvent $event) {
        $kernel = $event->getKernel();
        return $kernel->applyMiddleware($event->getRequest());

	});
    
    $response = $app->handle($request);
    $response->send();