<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\Files\MimetypeManager;
use Medas\HttpRequestHandler\{
    Exceptions\MimeTypeIsNotAccepted,
    ResponseHandlerManager\ExceptionJob,
    ResponseHandlerManager\Job,
    ResponseTypes\FileResponse
};

#[Service]
readonly class FileHandler implements ResponseHandler
{
    public function __construct(
        private MimetypeManager $mimetypeManager,
    )
    {
    }

    public function priority(): int
    {
        return -30;
    }

    public function handleResponse(Job $job): bool
    {
        if (!$job->response instanceof FileResponse) {
            return false;
        }

        $mimetype = $this->mimetypeManager->get($job->response->file);

        if (!$job->request->serverData->acceptsMimeType($mimetype, ignoreDoubleWild: false)) {
            throw new MimeTypeIsNotAccepted($mimetype);
        }

        $fileName = $job->response->file->name ?: str_replace('/', '.', $mimetype);
        $job->headers['Access-Control-Allow-Origin'] = '*';
        $job->headers['Content-Type'] = $mimetype;
        $job->headers['Content-Disposition: inline; filename="%s"'] = $fileName;
        $job->output = $job->response->file->content;

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        return false;
    }
}
