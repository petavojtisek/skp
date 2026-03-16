<?php

namespace App\Model\Presentation;

use App\Model\Base\BaseMapper;

class PresentationMapper extends BaseMapper
{
    protected string $tableName = 'presentation';
    protected string $primaryKey = 'presentation_id';

    public function getAdminPresentations(int $adminId): array
    {
        return $this->db->select('presentation_id')
            ->from('admin_presentation')
            ->where('admin_id = %i', $adminId)
            ->fetchPairs(null, 'presentation_id');
    }

    public function saveAdminPresentations(int $adminId, array $presentationIds): void
    {
        $this->db->delete('admin_presentation')->where('admin_id = %i', $adminId)->execute();
        foreach ($presentationIds as $presentationId) {
            $this->db->insert('admin_presentation', [
                'admin_id' => $adminId,
                'presentation_id' => $presentationId
            ])->execute();
        }
    }
}
