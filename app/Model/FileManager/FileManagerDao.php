<?php

namespace App\Model\FileManager;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class FileManagerDao extends BaseDao
{
    protected string $entityName = 'FileManager\\FileManagerEntity';

    /** @var FileManagerMapper */
    protected IMapper $mapper;

    public function __construct(FileManagerMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }

    public function getFilesByElement(string $sourceType, int $elementId): array
    {
        $data = $this->mapper->getFilesByElement($sourceType, $elementId);
        return $this->getEntities($this->entityName, $data ?: []) ?: [];
    }
}
