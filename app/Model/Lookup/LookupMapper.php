<?php

namespace App\Model\Lookup;

use App\Model\Base\BaseMapper;

class LookupMapper extends BaseMapper
{
    protected string $tableName = 'lookup';

    protected string $primaryKey = 'lookup_id';


    protected string $translateName = 'lookup_lang';
    protected string $translateKey = 'lookup_id';
    protected string $langKey = 'lang_id';

    public function getConstants(): array
    {
        return $this->db->select('constant, lookup_id')
            ->from($this->tableName)
            ->where('constant IS NOT NULL AND constant != ""')
            ->fetchPairs('constant', 'lookup_id');
    }

    /**
     * Get lookup list with translations
     * @param int $pid
     * @param int|null $langId
     * @return array
     */
    public function getLookupList(int $pid, ?int $langId = null): array
    {
        $selection = $this->db->select('l.lookup_id, COALESCE(ll.item, l.item) as item, l.constant')
            ->from($this->tableName)->as('l')
            ->leftJoin('lookup_lang')->as('ll')->on('l.lookup_id = ll.lookup_id AND ll.lang_id = %i', $langId)
            ->where('l.parent_id = %i', $pid);

        return $selection->fetchAssoc('lookup_id');
    }

    /**
     * Get single lookup item with translation
     * @param int $lookupId
     * @param int|null $langId
     * @return string|null
     */
    public function getLookupItem(int $lookupId, ?int $langId = null): ?string
    {
        return $this->db->select('COALESCE(ll.item, l.item)')
            ->from($this->tableName)->as('l')
            ->leftJoin('lookup_lang')->as('ll')->on('l.lookup_id = ll.lookup_id AND ll.lang_id = %i', $langId)
            ->where('l.lookup_id = %i', $lookupId)
            ->fetchSingle();
    }

    public function getTranslations(int $lookupId): array
    {
        return $this->db->select('lang_id, item')
            ->from('lookup_lang')
            ->where('lookup_id = %i', $lookupId)
            ->fetchPairs('lang_id', 'item');
    }

    public function saveTranslation(int $lookupId, int $langId, string $item): void
    {
        $this->db->query('REPLACE INTO lookup_lang', [
            'lookup_id' => $lookupId,
            'lang_id' => $langId,
            'item' => $item,
        ]);
    }

    public function deleteTranslations(int $lookupId): void
    {
        $this->db->delete('lookup_lang')->where('lookup_id = %i', $lookupId)->execute();
    }

    public function getMaxMasterId(): int
    {
        return (int) $this->db->select('MAX(lookup_id)')
            ->from($this->tableName)
            ->where('parent_id = 1')
            ->fetchSingle();
    }
}
