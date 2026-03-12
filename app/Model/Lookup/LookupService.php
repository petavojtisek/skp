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
                    if ($item->parent_id == 1 || $item->parent_id == 0) {
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
            $lookup->setTranslations($this->lookupDao->getTranslations($id));
        }
        return $lookup ?: null;
    }

    public function saveLookup(LookupEntity $lookup): int
    {
        if (!$lookup->getId() && $lookup->parent_id == 1) {
            // Logic for Master ID series (+100)
            $maxId = $this->lookupDao->getMapper()->getMaxMasterId();
            $newId = (int) (floor($maxId / 100) * 100 + 100);
            $lookup->setId($newId);
        }

        $id = (int) $this->lookupDao->save($lookup)->getId();

        foreach ($lookup->getTranslations() as $langId => $item) {
            if (defined('C_LANGUAGE_CS') && $langId == C_LANGUAGE_CS) continue;
            $this->lookupDao->saveTranslation((int)$id, (int)$langId, (string)$item);
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
