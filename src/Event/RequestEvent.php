<?php 

namespace Erahma\FutureFramework\Event;

use Erahma\FutureFramework\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestEvent extends EventDispatcher
{
    protected $request;
    protected Kernel $kernel;

    function __construct(Kernel $kernel) {
        $this->kernel = $kernel;
        parent::__construct();
    }
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getKernel() : Kernel
    {
        return $this->kernel;
    }
}