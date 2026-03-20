<?php

namespace App\Model\Page;

use App\Model\Base\BaseService;

class SpecParamPageService extends BaseService
{
    private SpecParamPageDao $specParamPageDao;

    public function __construct(SpecParamPageDao $specParamPageDao)
    {
        $this->specParamPageDao = $specParamPageDao;
    }

    /**
     * @return SpecParamPageEntity[]
     */
    public function findByPage(int $pageId): array
    {
        return $this->specParamPageDao->findAllBy(['page_id' => $pageId]) ?: [];
    }

    public function save(SpecParamPageEntity $entity): void
    {
        $this->specParamPageDao->save($entity);
    }

    public function delete(int $id): void
    {
        $this->specParamPageDao->delete($id);
    }
}
