<?php

namespace PinduoduoApiBundle\Tests;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemOperator;

class MockFilesystemOperator implements FilesystemOperator
{
    public function fileExists(string $location): bool
    {
        return false;
    }

    public function has(string $location): bool
    {
        return false;
    }

    public function read(string $location): string
    {
        return '';
    }

    /**
     * @return resource
     */
    public function readStream(string $location)
    {
        $resource = fopen('php://memory', 'r');
        if (false === $resource) {
            throw new \RuntimeException('Failed to open memory stream');
        }

        return $resource;
    }

    /**
     * @param array<mixed> $config
     */
    public function write(string $location, string $contents, array $config = []): void
    {
    }

    /**
     * @param array<mixed> $config
     * @param mixed $contents
     */
    public function writeStream(string $location, $contents, array $config = []): void
    {
    }

    public function delete(string $location): void
    {
    }

    public function deleteDirectory(string $location): void
    {
    }

    /**
     * @param array<mixed> $config
     */
    public function createDirectory(string $location, array $config = []): void
    {
    }

    public function listContents(string $location, bool $deep = false): DirectoryListing
    {
        return new DirectoryListing([]);
    }

    /**
     * @param array<mixed> $config
     */
    public function move(string $source, string $destination, array $config = []): void
    {
    }

    /**
     * @param array<mixed> $config
     */
    public function copy(string $source, string $destination, array $config = []): void
    {
    }

    public function lastModified(string $location): int
    {
        return 0;
    }

    public function fileSize(string $location): int
    {
        return 0;
    }

    public function mimeType(string $location): string
    {
        return '';
    }

    public function visibility(string $location): string
    {
        return '';
    }

    public function setVisibility(string $location, string $visibility): void
    {
    }

    public function directoryExists(string $location): bool
    {
        return false;
    }
}
