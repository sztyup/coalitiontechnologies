<?php

namespace App\Http\Controllers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller
{
    /** @var EntityManager */
    protected $em;

    /** @var Factory */
    protected $viewFactory;

    /** @var Redirector */
    protected $redirector;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Factory $view
     * @param Redirector $redirector
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Factory $view,
        Redirector $redirector
    ) {
        $this->em = $entityManager;
        $this->viewFactory = $view;
        $this->redirector = $redirector;
    }

    /**
     * @param int $error_code
     * @param string|null $message
     *
     * @return HttpException
     */
    public function abort(int $error_code, string $message = null)
    {
        return new HttpException($error_code, $message);
    }

    /**
     * @return Redirector
     */
    protected function redirect()
    {
        return $this->redirector;
    }

    /**
     * @param $view
     * @param array $data
     * @param array $mergeData
     * @return View
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return $this->viewFactory->make($view, $data, $mergeData);
    }

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return Response
     */
    public function callAction($method, $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }
}
