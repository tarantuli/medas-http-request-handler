<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\{Attributes\Service, StringMaker};
use Medas\HttpRequestHandler\{
    Exceptions\DoesNotImplementJsonResponse,
    ResponseHandlerManager\ExceptionJob,
    ResponseHandlerManager\Job,
    ResponseTypes\JsonResponse
};
use Medas\Json\JsonEncoder;

#[Service]
readonly class JsonHandler implements ResponseHandler
{
    public function __construct(
        private JsonEncoder $jsonEncoder,
    )
    {
    }

    public function priority(): int
    {
        return -10;
    }

    public function handleResponse(Job $job): bool
    {
        if ($job->request->uri->extension === 'json') {
            if (!$job->response instanceof JsonResponse) {
                throw new DoesNotImplementJsonResponse($job->response);
            }

            // Else, fall through to the echo command
        }
        elseif (
            !$job->request->serverData->acceptsMimeType('application/json')
            || !$job->response instanceof JsonResponse
        ) {
            return false;
        }

        $job->headers['Access-Control-Allow-Origin'] = '*';
        $job->headers['Content-Type'] = 'application/json';
        $job->output = $this->jsonEncoder->encode($job->response->getJsonResponse());

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        if (!$job->request->serverData->acceptsMimeType('application/json')) {
            return false;
        }

        $job->headers['Content-Type'] = 'application/json';
        $job->headers['Access-Control-Allow-Origin'] = '*';
        $trace = $this->normalizeTrace($job->exception);

        $job->output = $this->jsonEncoder->encode([
            'message' => StringMaker::instance()->forceUtf8($job->exception->getMessage()),
            'code' => $job->exception->getCode(),
            'fileName' => $job->exception->getFile(),
            'lineNumber' => $job->exception->getLine(),
            'trace' => $trace,
        ]);

        return true;
    }

    private function normalizeTrace(\Throwable $exception): array
    {
        $paths = [];

        foreach ($exception->getTrace() as $trace) {
            $arguments = [];

            foreach ($trace['args'] ?? [] as $arg) {
                $type = get_debug_type($arg);

                if (class_exists($type) || !is_scalar($arg)) {
                    $arguments[] = $type;
                }
                else {
                    $arguments[] = StringMaker::instance()->forceUtf8((string) $arg);
                }
            }

            $paths[] = [
                'file' => $trace['file'],
                'line' => $trace['line'],
                'function' => $trace['function'],
                'arguments' => $arguments,
            ];
        }

        return $paths;
    }
}
