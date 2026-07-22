<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Collections\GenericCollection;

class FileData extends GenericCollection
{
    public function data(): array
    {
        return $this->data;
    }

    public function get(string $name): UploadedFile
    {
        $data = $this->data[$name];

        return new UploadedFile(
            $data['name'],
            $data['full_path'] ?? $data['name'],
            $data['type'],
            $data['tmp_name'],
            $data['error'],
            $data['size']
        );
    }
}
