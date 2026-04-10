<?php

namespace App\Model\Element;

use App\Model\Base\BaseDao;
use App\Model\Base\IMapper;

class ElementDao extends BaseDao
{
    protected string $entityName = 'Element\\ElementEntity';

    /** @var ElementMapper */
    protected IMapper $mapper;

    public function __construct(ElementMapper $mapper)
    {
        $this->mapper = $mapper;
    }


    public function findFront(int $id): ?ElementEntity
    {
        $by = [
            $this->mapper->getPrimaryKey() => $id,
            'status_id'=>C_ELEMENT_STATUS_READY,
        ];

        $data = $this->mapper->findOneBy($by);
        if($data and $data['valid_from'] and $data['valid_from'] > new \DateTime()) {
            return null;
        }
        if($data and $data['valid_to'] and $data['valid_to'] < new \DateTime()) {
            return null;
        }


        if ($data) {
            return $this->getEntity($this->entityName, (array) $data );
        }
        return null;
    }
}
