<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

class ServerData
{
    private array $acceptTypes;

    public function __construct(
        private readonly array $data
    )
    {
    }

    public function acceptsMimeType(string $mimeType, bool $ignoreDoubleWild = true): bool
    {
        if (array_key_exists($mimeType, $this->getAcceptTypes())) {
            return true;
        }

        foreach ($this->getAcceptTypes() as $acceptType => $quality) {
            if (!str_contains($acceptType, '*')) {
                continue;
            }

            if ($ignoreDoubleWild && $acceptType === '*/*') {
                continue;
            }

            $pattern = '/^' . str_replace('\\*', '\\w+', preg_quote($acceptType, '/')) . '$/';

            if (preg_match($pattern, $mimeType)) {
                return true;
            }
        }

        return false;
    }

    private function getAcceptTypes(): array
    {
        if (!isset($this->acceptTypes)) {
            $this->acceptTypes = [];
            $acceptHeader = strtolower(str_replace(' ', '', $this->data['HTTP_ACCEPT'] ?? ''));
            $headerParts = explode(',', $acceptHeader);

            foreach ($headerParts as $headerPart) {
                $quality = 1;

                if (strpos($headerPart, ';q=')) {
                    [$headerPart, $quality] = explode(';q=', $headerPart);
                }

                if ($quality === '0') {
                    continue;
                }

                $this->acceptTypes[$headerPart] = (float) $quality;
            }

            arsort($this->acceptTypes);
        }

        return $this->acceptTypes;
    }
}
