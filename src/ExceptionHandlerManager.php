<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;
use Medas\ServiceManager\ErrorHandling\CliExceptionHandler;

#[Service]
readonly class ExceptionHandlerManager
{
    public function __construct(
        private CliExceptionHandler                      $cliExceptionHandler,
        private ResponseHandlerManager\OutputDataPrinter $outputDataPrinter,
        private RequestDataManager                       $requestDataManager,
        private ResponseHandlerFinder                    $handlerFinder,
    )
    {
    }

    public function handle(\Throwable $exception): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        $job = new ResponseHandlerManager\ExceptionJob(
            $this->requestDataManager->getWithoutExceptions(),
            $exception
        );

        $this->determineResponseCode($job);

        try {
            foreach ($this->handlerFinder->get() as $responseHandler) {
                if ($responseHandler->handleException($job)) {
                    $this->outputDataPrinter->print($job);

                    return;
                }
            }

            $this->cliExceptionHandler->printThrowable($job->exception);
        }
        catch (\Throwable) {
            $this->cliExceptionHandler->printThrowable($job->exception);
        }
    }

    private function determineResponseCode(ResponseHandlerManager\ExceptionJob $job): void
    {
        if ($job->exception instanceof Exceptions\DeclaresResponseCode) {
            $job->responseCode = $job->exception->responseCode();
        }
        elseif ($job->exception instanceof Exceptions\BadRequest) {
            $job->responseCode = 400;
        }
        elseif ($job->exception instanceof Exceptions\UnauthorizedRequest) {
            $job->responseCode = 403;
        }
        else {
            $job->responseCode = 500;
        }
    }
}
