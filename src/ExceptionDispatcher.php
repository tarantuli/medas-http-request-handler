<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;
use Medas\ServiceManager\ErrorHandling\CliExceptionHandler;

#[Service]
readonly class ExceptionDispatcher
{
    public function __construct(
        private CliExceptionHandler                  $cliExceptionHandler,
        private RequestFactory                       $requestFactory,
        private ResponseDispatcher\OutputDataPrinter $outputDataPrinter,
        private ResponseHandlerRegistry              $responseHandlerRegistry,
    )
    {
    }

    public function handle(\Throwable $exception): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        $job = new ResponseDispatcher\ExceptionJob(
            $this->requestFactory->getWithoutExceptions(),
            $exception
        );

        $this->determineResponseCode($job);

        try {
            foreach ($this->responseHandlerRegistry->get() as $responseHandler) {
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
