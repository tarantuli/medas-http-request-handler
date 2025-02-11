<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

use Medas\Core\File;

interface FileResponse extends Response
{
    public function getFileResponse(): File;

    public function setFile(File $file): void;
}
