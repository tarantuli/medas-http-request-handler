<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlerManager;

use Medas\Core\Attributes\Service;

#[Service]
readonly class OutputDataPrinter
{
    public function print(OutputData $outputData): void
    {
        $eTag = sha1($outputData->output);
        $requestHeader = $outputData->request->serverData['HTTP_IF_NONE_MATCH'] ?? null;

        if ($requestHeader && $requestHeader === $eTag) {
            $outputData->responseCode = 304;
            $outputData->output = '';
        }
        else {
            $outputData->headers['ETag'] = $eTag;
        }

        if (!headers_sent()) {
            http_response_code($outputData->responseCode);

            foreach ($outputData->headers as $name => $value) {
                header(sprintf('%s: %s', $name, $value));
            }
        }

        echo $outputData->output;
    }
}
