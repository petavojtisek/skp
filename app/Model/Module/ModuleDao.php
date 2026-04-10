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





    public function getModuleByInstallId(int $installId): ?ModuleEntity
    {
        $res = $this->mapper->findOneBy(['install_id'=>$installId]);
        return new ModuleEntity($res);
    }

    public function findActiveByType(int $type): array
    {
        $data = $this->mapper->findAllBy(['module_type' => $type, 'module_active' => 'Y']);
        return $this->getEntities($this->entityName, $data);
    }

}
