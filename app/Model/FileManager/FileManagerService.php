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

    public function getFilesByPath(string $baseType, string $subDir): array
    {
        return $this->fileManagerDao->getFilesByPath($baseType, $subDir);
    }

    public function getPhysicalPath(FileManagerEntity $file): string
    {
        return $this->storageDir . DIRECTORY_SEPARATOR . $file->getPath() . DIRECTORY_SEPARATOR . $file->getFileName();
    }

    public function createDirectory(string $baseType, string $path): bool
    {
        $fullPath = $this->storageDir . DIRECTORY_SEPARATOR . $baseType . DIRECTORY_SEPARATOR . $path;
        if (!file_exists($fullPath)) {
            return FileSystem::createDir($fullPath);
        }
        return true;
    }

    public function getDirectories(string $baseType, string $subPath = ''): array
    {
        $dir = $this->storageDir . DIRECTORY_SEPARATOR . $baseType . ($subPath ? DIRECTORY_SEPARATOR . $subPath : '');
        if (!is_dir($dir)) return [];

        $items = scandir($dir);
        $dirs = [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            if (is_dir($dir . DIRECTORY_SEPARATOR . $item)) {
                $dirs[] = $item;
            }
        }
        return $dirs;
    }
}
