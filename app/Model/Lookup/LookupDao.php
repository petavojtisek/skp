<?php

namespace App\Model\Lookup;

use App\Model\Base\BaseDao;

class LookupDao extends BaseDao
{
    protected string $entityName = 'Lookup\LookupEntity';

    /** @var LookupMapper */
    protected $mapper;

    public function __construct(LookupMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getConstants(): array
    {
        return $this->mapper->getConstants();
    }

    public function getLookupList($parentId, $langId = null): array
    {
        return $this->mapper->getLookupList($parentId, $langId);
    }

    public function getLookupItem($lookupId, $langId = null): ?string
    {
        return $this->mapper->getLookupItem($lookupId, $langId);
    }

    public function getTranslations(int $lookupId): array
    {
        return $this->mapper->getTranslations($lookupId);
    }

    public function saveTranslation(int $lookupId, int $langId, string $item): void
    {
        $this->mapper->saveTranslation($lookupId, $langId, $item);
    }

    public function deleteTranslations(int $lookupId): void
    {
        $this->mapper->deleteTranslations($lookupId);
    }


}
