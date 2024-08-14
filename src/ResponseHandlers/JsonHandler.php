<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\{Attributes\Service, StringMaker};
use Medas\HttpRequestHandler\{
    Exceptions\DoesNotImplementJsonResponse,
    Request\Request,
    ResponseHandlerManager,
    ResponseTypes\JsonResponse,
    ResponseTypes\Response
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

    public function handleResponse(Request $request, Response $response, ResponseHandlerManager $manager): bool
    {
        if ($request->uri->extension === 'json') {
            if (!$response instanceof JsonResponse) {
                throw new DoesNotImplementJsonResponse($response);
            }

            // Else, fall through to the echo command
        }
        elseif (!$request->serverData->acceptsMimeType('application/json') || !$response instanceof JsonResponse) {
            return false;
        }

        $manager->setHeader('Content-Type', 'application/json');
        $manager->setHeader('Access-Control-Allow-Origin', '*');

        echo $this->jsonEncoder->encode($response->getJsonResponse());

        return true;
    }

    public function handleException(
        Request                      $request,
        \Exception|\TypeError|\Error $exception,
        ResponseHandlerManager       $manager
    ): bool
    {
        if (!$request->serverData->acceptsMimeType('application/json')) {
            return false;
        }

        $manager->setHeader('Content-Type', 'application/json');
        $manager->setHeader('Access-Control-Allow-Origin', '*');

        $trace = $this->normalizeTrace($exception);

        echo $this->jsonEncoder->encode([
            'message' => StringMaker::instance()->forceUtf8($exception->getMessage()),
            'code' => $exception->getCode(),
            'fileName' => $exception->getFile(),
            'lineNumber' => $exception->getLine(),
            'trace' => $trace,
        ]);

        return true;
    }

    private function normalizeTrace(\Exception|\TypeError|\Error $exception): array
    {
        $paths = [];

        foreach ($exception->getTrace() as $trace) {
            $arguments = [];

            foreach ($trace['args'] as $arg) {
                $type = get_debug_type($arg);

                if (class_exists($type) || !is_scalar($arg)) {
                    $arguments[] = $type;
                }
                else {
                    $arguments[] = StringMaker::instance()->forceUtf8($arg);
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
