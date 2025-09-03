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
                printf("%s:%u\n", $trace['file'], $trace['line']);
            }
            else {
                echo "[main]\n";
            }

            if (isset($trace['class'])) {
                printf("  %s::%s()\n", $trace['class'], $trace['function']);

                try {
                    $reflector = (new \ReflectionMethod($trace['class'], $trace['function']))->getParameters();
                }
                catch (\ReflectionException) {
                    $reflector = null;
                }
            }
            else {
                printf("  %s()\n", $trace['function']);

                $reflector = null;
            }

            foreach ($trace['args'] ?? [] as $index => $argument) {
                printf("    %s: ", $reflector ? $reflector[$index]->name : $index);

                if (is_array($argument)) {
                    try {
                        $argument = json_encode($argument);
                    }
                    catch (\Exception) {
                        $argument = "array (... cannot be serialized ...)";
                    }
                }

                if (is_string($argument) && mb_detect_encoding($argument, 'UTF-8')) {
                    printf("%s\n", mb_substr($argument, 0, 156));
                }
                else {
                    printf("%s\n", get_debug_type($argument));
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
