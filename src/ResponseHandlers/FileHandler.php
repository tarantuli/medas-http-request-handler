<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\{
    Exceptions\MimeTypeIsNotAccepted,
    Request\Request,
    ResponseHandlerManager,
    ResponseTypes\FileResponse,
    ResponseTypes\Response
};

#[Service]
class FileHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -30;
    }

    public function handleResponse(Request $request, Response $response, ResponseHandlerManager $manager): bool
    {
        if (!$response instanceof FileResponse) {
            return false;
        }

        $file = $response->getFileResponse();
        $mimetype = $file->mimetype();

        if (!$request->serverData->acceptsMimeType($mimetype)) {
            throw new MimeTypeIsNotAccepted($mimetype);
        }

        $fileName = $file->name() ?: str_replace('/', '.', $mimetype);

        $manager->setHeader('Access-Control-Allow-Origin', '*');
        $manager->setHeader('Content-Type', $mimetype);
        $manager->setHeader('Content-Disposition: inline; filename="%s"', $fileName);

        echo $file->content();

        return true;
    }

    public function handleException(
        Request                      $request,
        \Exception|\TypeError|\Error $exception,
        ResponseHandlerManager       $manager
    ): bool
    {
        return false;
    }
}
