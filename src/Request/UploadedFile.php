<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

readonly class UploadedFile
{
    public function __construct(
        public string $name,
        public string $fullPath,
        public string $mimeType,
        public string $tempPath,
        public int    $error,
        public int    $size,
    )
    {
    }
}
