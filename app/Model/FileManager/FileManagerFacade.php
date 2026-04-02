<?php

namespace App\Model\FileManager;

class FileManagerFacade
{
    private FileManagerService $fileManagerService;

    public function __construct(FileManagerService $fileManagerService)
    {
        $this->fileManagerService = $fileManagerService;
    }

    public function getStorageDir(): string
    {
        return $this->fileManagerService->getStorageDir();
    }

    public function getTempDir(): string
    {
        return $this->fileManagerService->getTempDir();
    }

    public function getFile(int $id): ?FileManagerEntity
    {
        return $this->fileManagerService->find($id);
    }

    public function saveFile(FileManagerEntity $entity): int
    {
        return $this->fileManagerService->save($entity);
    }

    public function deleteFile(int $id): void
    {
        $this->fileManagerService->delete($id);
    }

    public function getFilesByElement(string $sourceType, int $elementId): array
    {
        return $this->fileManagerService->getFilesByElement($sourceType, $elementId);
    }

    public function getFilesByPath(string $baseType, string $subDir): array
    {
        return $this->fileManagerService->getFilesByPath($baseType, $subDir);
    }

    public function getPhysicalPath(FileManagerEntity $file): string
    {
        return $this->fileManagerService->getPhysicalPath($file);
    }

    public function createDirectory(string $baseType, string $path): bool
    {
        return $this->fileManagerService->createDirectory($baseType, $path);
    }

    public function getDirectories(string $baseType, string $subPath = ''): array
    {
        return $this->fileManagerService->getDirectories($baseType, $subPath);
    }
}
