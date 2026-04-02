<?php

namespace App\Model\Element;

use App\Model\Base\BaseService;

class ElementService extends BaseService
{
    private ElementDao $elementDao;

    public function __construct(ElementDao $elementDao)
    {
        $this->elementDao = $elementDao;
    }

    public function find(int $id): ?ElementEntity
    {
        return $this->elementDao->find($id) ?: null;
    }

    public function save(ElementEntity $entity, ?int $authorId = null): int
    {
        if (!$entity->getId()) {
            if ($authorId) {
                $entity->setAuthorId($authorId);
            }
            $entity->setInserted(new \DateTime());
        }
        return (int)$this->elementDao->save($entity)->getId();
    }

    public function delete(int $id): void
    {
        $this->elementDao->delete($id);
    }
}
