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
        // create a context using the current request
        $context = new RequestContext();
        $context->fromRequest($request);
        
        $matcher = new UrlMatcher($this->routes, $context);

        /*  */
        $event = new RequestEvent();
        $event->setRequest($request);

        $this->dispatcher->dispatch( $event, 'request');
        /*  */

        try {
            // $attributes = $matcher->match($request->getPathInfo());
            // $controller = $attributes['controller'];
            // $response = $controller();
            $attributes = $matcher->match($request->getPathInfo());
			$controller = $attributes['controller'];
			unset($attributes['controller']);
			unset($attributes['_route']);
            
			$response = call_user_func_array($controller, $attributes);

        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not found!', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    public function map($path, $controller) {
        $this->routes->add($path, new Route(
            $path,
            array('controller' => $controller)
        ));
    }

    public function on($event, $callback)
    {
        $this->dispatcher->addListener($event, $callback);
    }

    public function fire($event)
    {
	    return $this->dispatcher->dispatch($event);
	}
}