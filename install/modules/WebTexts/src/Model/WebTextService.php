<?php

namespace App\Modules\WebTexts\Model;

class WebTextService
{
    private WebTextDao $webTextDao;

    public function __construct(WebTextDao $webTextDao)
    {
        $this->webTextDao = $webTextDao;
    }

    public function findWebTexts(?string $code = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->webTextDao->findWebTexts($code, $limit, $offset);
    }

    public function countWebTexts(?string $code = null): int
    {
        return $this->webTextDao->countWebTexts($code);
    }

    public function getAllWebTexts(): array
    {
        return $this->webTextDao->getAllWebTexts();
    }

    public function getWebTextByCode(string $code): ?WebTextEntity
    {
        return $this->webTextDao->getWebTextByCode($code);
    }

    public function saveWebText(WebTextEntity $webTextEntity): int
    {
        if ($webTextEntity->getWebTextId()) {
            $this->webTextDao->update($webTextEntity);
            return $webTextEntity->getWebTextId();
        } else {
            return $this->webTextDao->insert($webTextEntity);
        }
    }

    public function deleteWebText(int $id): void
    {
        $this->webTextDao->delete($id);
    }

    public function getWebText(int $id): ?WebTextEntity
    {
        return $this->webTextDao->findById($id);
    }
}
