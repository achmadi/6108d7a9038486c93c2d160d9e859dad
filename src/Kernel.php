<?php 
namespace Erahma\FutureFramework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Kernel implements HttpKernelInterface
{
    public const MAIN_REQUEST = 1;
    public const SUB_REQUEST = 2;

    /**
     * @deprecated since symfony/http-kernel 5.3, use MAIN_REQUEST instead.
     *             To ease the migration, this constant won't be removed until Symfony 7.0.
     */
    public const MASTER_REQUEST = self::MAIN_REQUEST;
    public function handle(Request $request, int $type = Kernel::MAIN_REQUEST, bool $catch = true) : Response
    {
        switch ($request->getPathInfo()) {
            case '/':
                $response = new Response('This is the website home');
                break;

            case '/about':
                $response = new Response('This is the about page');
                break;

            default:
                $response = new Response('Not found !', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }
}