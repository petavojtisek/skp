<?php

namespace App\Modules\WebTexts\Model;

class WebTextFacade
{
    private WebTextService $webTextService;

    public function __construct(WebTextService $webTextService)
    {
        $this->webTextService = $webTextService;
    }

    public function findWebTexts(?string $code = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->webTextService->findWebTexts($code, $limit, $offset);
    }

    public function countWebTexts(?string $code = null): int
    {
        return $this->webTextService->countWebTexts($code);
    }

    public function getAllWebTexts(): array
    {
        return $this->webTextService->getAllWebTexts();
    }

    public function getWebTextByCode(string $code): ?WebTextEntity
    {
        return $this->webTextService->getWebTextByCode($code);
    }

    public function saveWebText(WebTextEntity $webTextEntity): int
    {
        return $this->webTextService->saveWebText($webTextEntity);
    }

    public function deleteWebText(int $id): void
    {
        $this->webTextService->deleteWebText($id);
    }

    public function getWebText(int $id): ?WebTextEntity
    {
        return $this->webTextService->getWebText($id);
    }
}
