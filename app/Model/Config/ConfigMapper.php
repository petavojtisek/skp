<?php

namespace App\Model\Config;

use App\Model\Base\BaseMapper;

class ConfigMapper extends BaseMapper
{
    protected string $tableName = 'config';
    protected string $primaryKey = 'config_id';


    public string $translateTableName = 'config_lang';
    public string $translatePrimaryKey = 'config_id';
    public string $translateLangId = 'lang_id';
    public string $translateValueKey = 'value';




}
