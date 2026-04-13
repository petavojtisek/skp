<?php

namespace App\Modules\WebTexts\Model;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class WebTextDao extends BaseDao
{
    protected string $entityName = 'App\Modules\WebTexts\Model\WebTextEntity';

    /** @var WebTextMapper */
    protected IMapper $mapper;

    public function __construct(WebTextMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function findWebTexts(?string $code = null, ?int $limit = null, ?int $offset = null): array
    {
        $data = $this->mapper->findWebTexts($code, $limit, $offset);
        return $this->getEntities($this->entityName, $data);
    }

    public function countWebTexts(?string $code = null): int
    {
        return $this->mapper->countWebTexts($code);
    }

    public function getWebTextByCode(string $code): ?WebTextEntity
    {
        $data = $this->mapper->findBy('code', $code);
        return $data ? $this->getEntity($this->entityName, $data) : null;
    }

    public function getAllWebTexts(): array
    {
        $data = $this->mapper->findAll();
        return $this->getEntities($this->entityName, $data);
    }
}
