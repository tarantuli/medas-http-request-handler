<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\{
    Exceptions\DoesNotImplementJsonLdResponse,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseTypes\JsonLdResponse
};
use Medas\Json\JsonEncoder;

#[Service]
readonly class JsonLdHandler implements ResponseHandler
{
    public function __construct(
        private JsonEncoder $jsonEncoder,
    )
    {
    }

    public function priority(): int
    {
        return -5;
    }

    public function handleResponse(Job $job): bool
    {
        if ($job->request->uri->extension === 'jsonld') {
            if (!$job->response instanceof JsonLdResponse) {
                throw new DoesNotImplementJsonLdResponse($job->response);
            }

            // Else, fall through to the echo command
        }
        elseif (
            !$job->request->serverData->acceptsMimeType('application/ld+json')
            || !$job->response instanceof JsonLdResponse
        ) {
            return false;
        }

        $job->setHeader('Content-Type', 'application/ld+json');

        $job->output = $this->jsonEncoder->encode($job->response->getJsonLdResponse());

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        if (!$job->request->serverData->acceptsMimeType('application/ld+json')) {
            return false;
        }

        $job->setHeader('Content-Type', 'application/ld+json');

        // TODO: craft a real jsonld error response
        $job->output = $this->jsonEncoder->encode([
            'message' => $job->exception->getMessage(),
            'code' => $job->exception->getCode(),
            'fileName' => $job->exception->getFile(),
            'lineNumber' => $job->exception->getLine(),
        ]);

        return true;
    }
}
