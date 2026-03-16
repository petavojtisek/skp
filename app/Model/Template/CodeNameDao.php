<?php

namespace App\Model\Template;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class CodeNameDao extends BaseDao
{
    protected string $entityName = 'CodeNameEntity';

    /** @var CodeNameMapper */
    protected IMapper $mapper;

    public function __construct(CodeNameMapper $mapper)
    {
        $this->mapper = $mapper;
    }

}
