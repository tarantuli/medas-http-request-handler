<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\Files\MimetypeManager;
use Medas\HttpRequestHandler\{
    Exceptions\MimeTypeIsNotAccepted,
    ResponseDispatcher\ExceptionJob,
    ResponseDispatcher\Job,
    ResponseTypes\FileResponse
};

#[Service]
readonly class FileHandler implements ResponseHandler
{
    public function __construct(
        private CorsHeaderWriter $corsHandler,
        private MimetypeManager  $mimetypeManager,
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

        $this->corsHandler->handle($job);

        $job->setHeader('Content-Type', $mimetype);
        $job->setHeader('Content-Disposition', sprintf('inline; filename="%s"', $fileName));

        $job->output = $job->response->file->content;

        return true;
    }

    public function handleException(ExceptionJob $job): bool
    {
        return false;
    }
}
