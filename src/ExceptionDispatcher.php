<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{
    Attributes\Service,
    Exceptions\StorageExceptionType,
    Interfaces\ExceptionHandler,
    Interfaces\StorageException
};
use Medas\ErrorReporting\Printing\CliExceptionPrinter;

#[Service]
readonly class ExceptionDispatcher implements ExceptionHandler
{
    public function __construct(
        private CliExceptionPrinter                  $cliExceptionPrinter,
        private RequestFactory                       $requestFactory,
        private ResponseDispatcher\OutputDataPrinter $outputDataPrinter,
        private ResponseHandlerRegistry              $responseHandlerRegistry,
        private ResponseModifiers\ModifierRepository $modifierRepository,
    )
    {
    }

    public function handleException(\Throwable $exception): bool
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }

        $job = new ResponseDispatcher\ExceptionJob(
            $this->requestFactory->getWithoutExceptions(),
            $exception
        );

        $this->determineResponseCode($job);

        foreach ($this->modifierRepository->get() as $modifier) {
            $modifier->handle($job);
        }

        try {
            foreach ($this->responseHandlerRegistry->get() as $responseHandler) {
                if ($responseHandler->handleException($job)) {
                    $this->outputDataPrinter->print($job);

                    return true;
                }
            }

            $this->cliExceptionPrinter->handleException($job->exception);
        }
        catch (\Throwable) {
            $this->cliExceptionPrinter->handleException($job->exception);
        }

        return false;
    }

    private function determineResponseCode(ResponseDispatcher\ExceptionJob $job): void
    {
        if ($job->exception instanceof Exceptions\DeclaresResponseCode) {
            $job->responseCode = $job->exception->responseCode();
        }
        elseif ($job->exception instanceof Exceptions\BadRequest) {
            $job->responseCode = 400;
        }
        elseif ($job->exception instanceof StorageException) {
            /** @noinspection PhpDuplicateMatchArmBodyInspection */
            $job->responseCode = match ($job->exception->exceptionType) {
                StorageExceptionType::DuplicateKey => 409,
                StorageExceptionType::ForeignKeyViolation => 409,
                StorageExceptionType::DeadlockDetected => 503,
                StorageExceptionType::LockWaitTimeout => 503,
                StorageExceptionType::ConnectionLost => 503,
                StorageExceptionType::Unknown => 500,
            };
        }
        else {
            $job->responseCode = 500;
        }
    }
}
