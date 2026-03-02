<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Interfaces\ExceptionHandler};
use Medas\ServiceManager\ErrorHandling\CliExceptionHandler;

#[Service]
readonly class ExceptionDispatcher implements ExceptionHandler
{
    public function __construct(
        private CliExceptionHandler                  $cliExceptionHandler,
        private RequestFactory                       $requestFactory,
        private ResponseDispatcher\OutputDataPrinter $outputDataPrinter,
        private ResponseHandlerRegistry              $responseHandlerRegistry,
        private ResponseHandlers\CorsHeaderWriter    $corsHeaderWriter,
    )
    {
    }

    public function handleException(\Throwable $exception): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        $job = new ResponseDispatcher\ExceptionJob(
            $this->requestFactory->getWithoutExceptions(),
            $exception
        );

        $this->determineResponseCode($job);
        $this->corsHeaderWriter->handle($job);

        try {
            foreach ($this->responseHandlerRegistry->get() as $responseHandler) {
                if ($responseHandler->handleException($job)) {
                    $this->outputDataPrinter->print($job);

                    return;
                }
            }

            $this->cliExceptionHandler->handleException($job->exception);
        }
        catch (\Throwable) {
            $this->cliExceptionHandler->handleException($job->exception);
        }
    }

    private function determineResponseCode(ResponseDispatcher\ExceptionJob $job): void
    {
        if ($job->exception instanceof Exceptions\DeclaresResponseCode) {
            $job->responseCode = $job->exception->responseCode();
        }
        elseif ($job->exception instanceof Exceptions\UnauthorizedRequest) {
            $job->responseCode = 403;
        }
        elseif ($job->exception instanceof Exceptions\BadRequest) {
            $job->responseCode = 400;
        }
        else {
            $job->responseCode = 500;
        }
    }
}
