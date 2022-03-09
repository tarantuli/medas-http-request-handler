<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

use Medas\Core\FileEntity;

interface FileResponse extends Response
{
    public function getFileResponse(): FileEntity;

    public function setFile(FileEntity $file): void;
}
