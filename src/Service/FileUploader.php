<?php

namespace App\Service;

use League\Flysystem\FilesystemOperator;

class FileUploader
{
    private $storage;

    public function __construct(FilesystemOperator $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }

    public function uploadBase64File(string $base64file): string
    {
        $extension = explode('/', mime_content_type($base64file))[1];
        $data = explode(',', $base64file);
        $filename = sprintf('%s.%s', uniqid('book_', true), $extension);
        $this->storage->write($filename, base64_decode($data[1]));

        return $filename;
    }
}
