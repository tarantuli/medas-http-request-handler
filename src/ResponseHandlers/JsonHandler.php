<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\ErrorReporting\Normalizing\ThrowableNormalizer;
use Medas\HttpRequestHandler\{
    Exceptions\DoesNotImplementJsonResponse,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseTypes\JsonResponse
};
use Medas\Json\{JsonEncoder, Settings};

#[Service]
readonly class JsonHandler implements ResponseHandler
{
    public function __construct(
        private JsonEncoder         $jsonEncoder,
        private ThrowableNormalizer $throwableNormalizer,
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

        $job->setHeader('Content-Type', 'application/json');

        $job->output = $this->jsonEncoder->encode(
            $job->response->getJsonResponse(),
            new Settings(urlSafe: true)
        );

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        if (!$job->request->serverData->acceptsMimeType('application/json')
                && !$job->request->serverData->acceptsMimeType(
                    'application/json',
                    ignoreDoubleWild: false
                )) {
            return false;
        }

        $job->setHeader('Content-Type', 'application/json');

        $job->output = $this->jsonEncoder->encode(
            $this->throwableNormalizer->normalize($job->exception),
            new Settings(urlSafe: true)
        );

        return true;
    }
}
