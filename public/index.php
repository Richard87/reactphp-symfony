<?php

use App\Kernel;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use \Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

$_SERVER['APP_RUNTIME'] = \Runtime\React\Runtime::class;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

(new Dotenv('APP_ENV', 'APP_DEBUG'))->bootEnv('.env');

$loop = new \React\EventLoop\ExtEvLoop();
\React\EventLoop\Loop::set($loop);

class Application implements RequestHandlerInterface {

    public function __construct(array $context)
    {
        $this->kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
        $psr17Factory = new Psr17Factory();
        $this->psrFactory            = new PsrHttpFactory($psr17Factory,$psr17Factory,$psr17Factory,$psr17Factory);
        $this->httpFoundationFactory = new HttpFoundationFactory();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $req = $this->httpFoundationFactory->createRequest($request);
        $res = $this->kernel->handle($req);
        $response = $this->psrFactory->createResponse($res);

        return $response;
    }
}


return function (array $context) {
    return new Application($context);
};
