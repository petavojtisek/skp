<?php

namespace App\Model\Template;

use App\Model\Base\BaseDao;

class CodeNameDao extends BaseDao
{
    protected string $entityName = 'CodeNameEntity';

    /** @var CodeNameMapper */
    protected $mapper;

    public function __construct(CodeNameMapper $mapper)
    {
        $this->mapper = $mapper;
    }

}
