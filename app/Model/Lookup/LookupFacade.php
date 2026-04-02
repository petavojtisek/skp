<?php

namespace App\Model\Lookup;

class LookupFacade
{
    /** @var LookupService */
    private $lookupService;

    public function __construct(LookupService $lookupService)
    {
        $this->lookupService = $lookupService;
    }

    public function getConstants(): array
    {
        return $this->lookupService->getConstants();
    }

    public function getLookupList(int $parentId, ?int $langId = null): array
    {
        return $this->lookupService->getLookupList($parentId, $langId);
    }

    public function getLookupListOption(int $parentId, ?int $langId = null): array
    {
        return $this->lookupService->getLookupListOption($parentId, $langId);
    }

    public function getLookupItem(int $lookupId, ?int $langId = null): ?string
    {
        return $this->lookupService->getLookupItem($lookupId, $langId);
    }

    public function getLookupTree(): array
    {
        return $this->lookupService->getLookupTree();
    }

    public function getLookup(int $id): ?LookupEntity
    {
        return $this->lookupService->getLookup($id);
    }

    public function saveLookup(LookupEntity $lookup): int
    {
        return $this->lookupService->saveLookup($lookup);
    }

    public function deleteLookup(int $id): void
    {
        $this->lookupService->deleteLookup($id);
    }

    public function invalidateCache(): void
    {
        $this->lookupService->invalidateCache();
    }
}
