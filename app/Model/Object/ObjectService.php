<?php

namespace App\Model\Object;

use App\Model\Base\BaseService;

class ObjectService extends BaseService
{
    /** @var ObjectDao */
    private $objectDao;

    public function __construct(ObjectDao $objectDao)
    {
        $this->objectDao = $objectDao;
    }

    public function findAll(): array
    {
        return $this->objectDao->findAll();
    }

    public function find(int $id): ?ObjectEntity
    {
        return $this->objectDao->find($id);
    }

    public function save(ObjectEntity $entity): int
    {
        return (int)$this->objectDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->objectDao->delete($id);
    }
}
