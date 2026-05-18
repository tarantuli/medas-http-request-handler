<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseDispatcher;

use Medas\Core\Attributes\Service;

#[Service]
readonly class OutputDataPrinter
{
    /**
     * Applies ETag negotiation and mutates responseCode / headers / output accordingly.
     * Does not send any HTTP headers or echo output — safe to call in test contexts.
     */
    public function prepare(OutputData $outputData): void
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
    }

    public function print(OutputData $outputData): void
    {
        $this->prepare($outputData);

        if (!headers_sent()) {
            http_response_code($outputData->responseCode);

            foreach ($outputData->headers as $name => $value) {
                foreach (is_array($value) ? $value : [$value] as $subValue) {
                    header(sprintf('%s: %s', $name, $subValue));
                }
            }
        }

        echo $outputData->output;
    }
}
