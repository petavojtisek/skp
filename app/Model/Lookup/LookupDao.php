<?php

namespace App\Model\Lookup;

use App\Model\Base\BaseDao;
use App\Model\Base\BaseTranslateEntity;

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
        $list =  $this->mapper->getTranslations($lookupId);
        $translates = [];
        if($list){
            foreach ($list as $lang_id=> $item) {
                $translates[$lang_id] = new BaseTranslateEntity(
                    [
                        'element_id' => $lookupId,
                        'lang_id' => $lang_id,
                        'value' => $item
                    ]
                );
            }
        }
        return $translates;
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
