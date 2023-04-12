<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

use Medas\Core\Interfaces\FileEntity;

interface FileResponse extends Response
{
    public function getFileResponse(): FileEntity;

    public function setFile(FileEntity $file): void;
}
