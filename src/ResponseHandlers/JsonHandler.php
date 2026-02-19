<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\HttpRequestHandler\{
    ConfigOptions\CorsAllowedOrigins,
    Exceptions\DoesNotImplementJsonResponse,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseTypes\JsonResponse
};
use Medas\Json\{JsonEncoder, Settings};
use Medas\ServiceManager\ErrorHandling\ThrowableNormalizer;

#[Service]
readonly class JsonHandler implements ResponseHandler
{
    public function __construct(
        private JsonEncoder         $jsonEncoder,
        private ThrowableNormalizer $throwableNormalizer,

        #[ConfigValue(CorsAllowedOrigins::class)]
        private string              $corsAllowedOrigins,
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

        $this->setCorsHeaders($job);

        $job->headers['Content-Type'] = 'application/json';

        $job->output = $this->jsonEncoder->encode(
            $job->response->getJsonResponse(),
            new Settings(urlSafe: true)
        );

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        if (!$job->request->serverData->acceptsMimeType('application/json')) {
            return false;
        }

        $this->setCorsHeaders($job);

        $job->headers['Content-Type'] = 'application/json';

        $job->output = $this->jsonEncoder->encode(
            $this->throwableNormalizer->normalize($job->exception),
            new Settings(urlSafe: true)
        );

        return true;
    }

    /**
     * Sets CORS headers based on configuration.
     * 
     * If configured as wildcard (*), allows any origin.
     * Otherwise, validates the request origin against the allowed list.
     */
    private function setCorsHeaders(Job|ExceptionJob $job): void
    {
        if ($this->corsAllowedOrigins === '*') {
            $job->headers['Access-Control-Allow-Origin'] = '*';

            return;
        }

        $allowedOrigins = array_map('trim', explode(',', $this->corsAllowedOrigins));
        $requestOrigin = $job->request->serverData['HTTP_ORIGIN'] ?? '';

        if (in_array($requestOrigin, $allowedOrigins, true)) {
            $job->headers['Access-Control-Allow-Origin'] = $requestOrigin;
            $job->headers['Access-Control-Allow-Credentials'] = 'true';
        }
    }
}
