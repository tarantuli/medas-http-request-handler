<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

use Medas\Core\File;

readonly class FileResponse implements Response
{
    public function __construct(
        public File $file,
    )
    {
    }
}
