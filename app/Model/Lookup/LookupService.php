<?php

namespace App\Model\Lookup;

use App\Model\Base\BaseService;
use App\Model\System\Cache;

class LookupService extends BaseService
{
    /** @var LookupDao */
    private $lookupDao;

    /** @var Cache */
    private $cache;

    public function __construct(LookupDao $lookupDao, Cache $cache)
    {
        $this->lookupDao = $lookupDao;
        $this->cache = $cache;
    }

    public function getConstants(): array
    {
        return $this->cache->load('lookup_constants', function() {
            return $this->lookupDao->getConstants();
        }, ['lookup']);
    }

    public function getLookupList(int $parentId, ?int $langId = null): array
    {
        $key = 'lookup_list_' . $parentId . '_' . ($langId ?? 'default');
        return $this->cache->load($key, function() use ($parentId, $langId) {
            return $this->lookupDao->getLookupList($parentId, $langId);
        }, ['lookup']);
    }

    public function getLookupItem(int $lookupId, ?int $langId = null): ?string
    {
        $key = 'lookup_item_' . $lookupId . '_' . ($langId ?? 'default');
        return $this->cache->load($key, function() use ($lookupId, $langId) {
            return $this->lookupDao->getLookupItem($lookupId, $langId);
        }, ['lookup']);
    }

    public function getLookupTree(): array
    {
        return $this->cache->load('lookup_tree', function() {
            $all = $this->lookupDao->findAll();
            $tree = [];
            if ($all) {
                // First pass: find masters (root items)
                foreach ($all as $item) {
                    if ($item->parent_id == 1 or $item->parent_id == 0) {
                        $tree[$item->lookup_id] = [
                            'master' => $item,
                            'items' => []
                        ];
                    }
                }
                // Second pass: assign items to masters
                foreach ($all as $item) {
                    if (isset($tree[$item->parent_id])) {
                        $tree[$item->parent_id]['items'][] = $item;
                    }
                }
            }

            return $tree;
        }, ['lookup']);
    }

    public function getLookup(int $id): ?LookupEntity
    {

        $lookup = $this->lookupDao->find($id);
        if ($lookup) {
            $translates = $this->lookupDao->getTranslations($id);
            $lookup->setTranslates($translates);
        }
        return $lookup ?: null;
    }

    public function saveLookup(LookupEntity $lookup): int
    {
        if (!$lookup->getId() and $lookup->parent_id == 1) {
            // Logic for Master ID series (+100)
            $maxId = $this->lookupDao->getMapper()->getMaxMasterId();
            $newId = (int) (floor($maxId / 100) * 100 + 100);
            $lookup->setId($newId);
            $mapper = $this->lookupDao->getMapper();
            $mapper->insert($lookup);
            $mapper->deleteTranslations($lookup->getId());
            foreach ($lookup->getTranslates() as $langId => $translationEntity)
            {
                $mapper->saveTranslation($lookup->getId(), $langId, $translationEntity->getValue());
            }
        }else {
            $id = (int)$this->lookupDao->save($lookup)->getId();
        }

        $this->invalidateCache();
        return (int)$id;
    }

    public function deleteLookup(int $id): void
    {
        $this->lookupDao->deleteTranslations($id);
        $this->lookupDao->delete($id);
        $this->invalidateCache();
    }

    /**
     * Call this when lookup table changes
     */
    public function invalidateCache(): void
    {
        $this->cache->invalidateByTags(['lookup']);
    }
}
