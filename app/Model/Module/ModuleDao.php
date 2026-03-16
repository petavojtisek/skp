<?php

namespace App\Model\Module;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ModuleDao extends BaseDao
{
    protected string $entityName = 'Module\\ModuleEntity';

    /** @var ModuleMapper */
    protected IMapper $mapper;

    public function __construct(ModuleMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper(): IMapper
    {
        return $this->mapper;
    }



    public function getModuleByInstallId(int $installId): ?ModuleEntity
    {
        $res = $this->mapper->findOneBy(['install_id'=>$installId]);
        return new ModuleEntity($res);

    }
}
