<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseTypes\FileResponse;
use Medas\HttpRequestHandler\ResponseTypes\Response;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class FileHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -30;
    }

    public function handleResponse(Request $request, Response $response): bool
    {
        if (!$request->serverData->acceptsMimeType('*/*') || !$response instanceof FileResponse) {
            return false;
        }

        $file = $response->getFileResponse();

        $mimetype = $file->mimetype();
        $fileName = $file->name() ?: str_replace('/', '.', $mimetype);

        header('Access-Control-Allow-Origin: *');
        header(sprintf('Content-type: %s', $mimetype));
        header(sprintf('Content-Disposition: inline; filename="%s"', $fileName));

        echo $file->content();

        return true;
    }

    public function handleException(Request $request, \Exception|\TypeError|\Error $exception): bool
    {
        if (!$request->serverData->acceptsMimeType('*/*')) {
            return false;
        }

        echo json_encode([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'fileName' => $exception->getFile(),
            'lineNumber' => $exception->getLine(),

        ]);

        return true;
    }
}
