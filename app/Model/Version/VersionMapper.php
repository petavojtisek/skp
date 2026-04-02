<?php

namespace App\Model\Version;

use App\Model\Base\BaseMapper;

class VersionMapper extends BaseMapper
{
    protected string $tableName = 'version';

    public function setActiveVersion(int $componentId, int $elementId): void
    {
        $this->db->begin();
        try {
            // Smažeme existující vazbu pro tuto komponentu
            $this->db->delete($this->tableName)
                ->where('component_id = %i', $componentId)
                ->execute();

            // Vložíme novou vazbu
            $this->db->insert($this->tableName, [
                'component_id' => $componentId,
                'element_id' => $elementId
            ])->execute();

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getActiveElementId(int $componentId): ?int
    {
        return $this->db->select('element_id')
            ->from($this->tableName)
            ->where('component_id = %i', $componentId)
            ->fetchSingle();
    }
}
