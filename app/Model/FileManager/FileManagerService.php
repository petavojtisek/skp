<?php

namespace App\Model\FileManager;

use App\Model\Base\BaseService;
use Nette\Utils\FileSystem;

class FileManagerService extends BaseService
{
    private FileManagerDao $fileManagerDao;
    private string $storageDir;

    public function __construct(string $storageDir, FileManagerDao $fileManagerDao)
    {
        $this->fileManagerDao = $fileManagerDao;
        $this->storageDir = $storageDir;
    }

    public function findAll(): array
    {
        return $this->fileManagerDao->findAll();
    }

    public function find(int $id): ?FileManagerEntity
    {
        return $this->fileManagerDao->find($id);
    }

    public function save(FileManagerEntity $entity): int
    {
        return (int)$this->fileManagerDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $file = $this->find($id);
        if ($file) {
            $fyzicalPath = $this->getPhysicalPath($file);
            if (file_exists($fyzicalPath)) {
                FileSystem::delete($fyzicalPath);
            }
            $this->fileManagerDao->delete($id);
        }
    }

    public function getFilesByElement(string $sourceType, int $elementId): array
    {
        return $this->fileManagerDao->getFilesByElement($sourceType, $elementId);
    }

    public function getPhysicalPath(FileManagerEntity $file): string
    {
        return $this->storageDir . DIRECTORY_SEPARATOR . $file->getPath() . DIRECTORY_SEPARATOR . $file->getFileName();
    }
}
