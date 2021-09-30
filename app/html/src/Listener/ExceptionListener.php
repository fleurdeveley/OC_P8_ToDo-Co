<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Twig\Environment;

class ExceptionListener
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : $exception->getCode();

        if($status === 0) {
            $status = 500;
        }

        $message = $exception->getMessage();

        $response = new Response($this->twig->render('default/error.html.twig', ['message' => $message]), $status);

        $event->setResponse($response);
    }
}