<?php

namespace App\Model\FileManager;

class FileManagerFacade
{
    private FileManagerService $fileManagerService;

    public function __construct(FileManagerService $fileManagerService)
    {
        $this->fileManagerService = $fileManagerService;
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

    public function getPhysicalPath(FileManagerEntity $file): string
    {
        return $this->fileManagerService->getPhysicalPath($file);
    }
}
