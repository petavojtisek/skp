<?php

namespace App\Model\Config;

use App\Model\Base\BaseMapper;

class ConfigMapper extends BaseMapper
{
    protected $tableName = 'config';
    protected $primaryKey = 'config_id';

    /**
     * Get values from translation table
     */
    public function getTranslations(int $configId): array
    {
        return $this->db->select('language_id, value')
            ->from('config_description')
            ->where('config_id = %i', $configId)
            ->fetchPairs('language_id', 'value');
    }

    public function saveTranslation(int $configId, int $langId, string $value): void
    {
        $this->db->query('REPLACE INTO config_description', [
            'config_id' => $configId,
            'language_id' => $langId,
            'value' => $value,
        ]);
    }

    public function deleteTranslations(int $configId): void
    {
        $this->db->delete('config_description')->where('config_id = %i', $configId)->execute();
    }
}
