<?php

namespace App\Model\Lookup;

use App\Model\Base\BaseMapper;

class LookupMapper extends BaseMapper
{
    protected string $tableName = 'lookup';

    protected string $primaryKey = 'lookup_id';


    public string $translateTableName = 'lookup_lang';
    public string $translatePrimaryKey = 'lookup_id';
    public string $translateLangId = 'lang_id';
    public string $translateValueKey = 'value';

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
        $selection = $this->db->select('l.lookup_id, COALESCE(ll.value, l.item) as item, l.constant')
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
        return $this->db->select('lang_id, value')
            ->from('lookup_lang')
            ->where('lookup_id = %i', $lookupId)
            ->fetchPairs('lang_id', 'value');
    }

    public function getMaxMasterId(): int
    {
        return (int) $this->db->select('MAX(lookup_id)')
            ->from($this->tableName)
            ->where('parent_id = 1')
            ->fetchSingle();
    }
}
