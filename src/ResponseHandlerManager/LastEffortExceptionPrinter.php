<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlerManager;

use Medas\Core\Attributes\Service;

#[Service]
readonly class LastEffortExceptionPrinter
{
    public function print(\Throwable $exception): void
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            echo '<pre>';
        }

        foreach (array_reverse($exception->getTrace()) as $trace) {
            if (isset($trace['file'])) {
                printf(
                    "%s:%u\n   %s::%s()\n",
                    $trace['file'],
                    $trace['line'],
                    $trace['class'] ?? '[main]',
                    $trace['function']
                );
            }
            else {
                printf("[main]\n   %s::%s()\n", $trace['class'] ?? '[main]', $trace['function']);
            }

            foreach ($trace['args'] ?? [] as $index => $argument) {
                if (is_string($argument) && mb_detect_encoding($argument, 'UTF-8')) {
                    printf("    %u: %s\n", $index, mb_substr($argument, 0, 78));
                }
                else {
                    printf(
                        "    %u: %s(%u)\n",
                        $index,
                        get_debug_type($argument),
                        is_string($argument) ? strlen($argument) : 0
                    );
                }
            }

            printf("\n");
        }

        printf(
            "\n%s:%u [%u]\n%s\n\n",
            $exception->getFile(),
            $exception->getLine(),
            $exception->getCode(),
            $exception->getMessage()
        );

        if (isset($_SERVER['HTTP_HOST'])) {
            echo '</pre>';
        }
    }
}
