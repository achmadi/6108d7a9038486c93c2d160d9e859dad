<?php 
namespace Erahma\FutureFramework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Erahma\FutureFramework\Event\RequestEvent;
use Erahma\FutureFramework\Storages\EloquentManager;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class Kernel implements HttpKernelInterface
{
    public const MAIN_REQUEST = 1;
    public const SUB_REQUEST = 2;

    /**
     * @deprecated since symfony/http-kernel 5.3, use MAIN_REQUEST instead.
     *             To ease the migration, this constant won't be removed until Symfony 7.0.
     */
    public const MASTER_REQUEST = self::MAIN_REQUEST;

    /** @var RouteCollection */
    protected $routes;
    protected $dispatcher;

    public function __construct($config = [])
    {
        $this->routes = new RouteCollection();
        $this->dispatcher = new EventDispatcher();

        EloquentManager::init(
            $config['driver']??null,
            $config['host']??null,
            $config['database']??null,
            $config['username']??null,
            $config['password']??null
        );
        
    }
    
    public function handle(Request $request, int $type = Kernel::MAIN_REQUEST, bool $catch = true) : Response
    {
        try {
            // create a context using the current request
            $context = new RequestContext();
            $context->fromRequest($request);
            
            $matcher = new UrlMatcher($this->routes, $context);

            /* Apply Event Request */
            $event = new RequestEvent($this);
            $event->setRequest($request);
            $this->dispatcher->dispatch( $event, 'request');
            /*  */
           
            $attributes = $matcher->match($request->getPathInfo());
            
			$controller = $attributes['controller'];
			unset($attributes['controller']);
			unset($attributes['_route']);
			unset($attributes['middlewares']);
            
            $data = [];
            if ($request->getContent() !== '' ) {
                $data = $request->toArray();
            }
            
			$response = call_user_func_array($controller, array_merge($attributes, ['data' => $data]));

        } catch (ResourceNotFoundException $e) {

            $response = new Response('Not found! (ResourceNotFoundException)', Response::HTTP_NOT_FOUND);

        }catch (MethodNotAllowedException $e) {

            $response = new Response('Not found! (MethodNotAllowedException)', Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {
            
            $response = new Response("Internal server error! (".$th->getMessage().")", Response::HTTP_NOT_FOUND);

        }

        return $response;

    }

    public function map($path, $controller, $middlewares = [], string|array $methods= 'GET') {
        $route = new Route(
            $path,
            array_merge(array('controller' => $controller), [ 'middlewares' => $middlewares])
        );
        $route->setMethods($methods);
        $this->routes->add($path, $route);
    }

    public function on($event, $callback)
    {
        $this->dispatcher->addListener($event, $callback);
    }

    public function fire($event)
    {
	    return $this->dispatcher->dispatch($event);
	}

    public $middlewares = [];
    
    public function registerMiddleware($name, callable $callback)
    {
	    $this->middlewares[$name] = $callback;
	}

    public function applyMiddleware($request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);
        
        $matcher = new UrlMatcher($this->routes, $context);
        $attributes = $matcher->match($request->getPathInfo());
        $shipMiddlewares = $attributes['middlewares'];
        foreach ($shipMiddlewares as $midName) {
            if ($callback = $this->middlewares[$midName]??false) {
                $callback($request, $this->routes);
            }
        }
	}
}