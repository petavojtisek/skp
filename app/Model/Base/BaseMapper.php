<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 9:53
 */

namespace App\Model\Base;



use App\Model\Log\LogEntity;
use App\Model\Log\LogMapper;
use Dibi\Connection;
use Dibi\DateTime;
use Nette\Application\Application;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

class BaseMapper extends AMapper
{
    /** @var Application */
    protected Application $application;

    public function __construct(Connection $connection, Application $application)
    {
        parent::__construct($connection);
        $this->application = $application;
    }

    public function save(IEntity $entity, bool $withTranslation = false): IEntity
    {
        $entity = parent::save($entity, $withTranslation);
        $this->logChanges($entity, 'save');
        return $entity;
    }

    public function delete(mixed $id): mixed
    {
        $data = $this->find($id);
        return  parent::delete($id);
        $this->logDelete($data, $id);
    }

   public function deleteBy(array $by): mixed
   {
       $data = $this->findOneBy($by, false, false, true);
       return  parent::deleteBy($by);
       $this->logChanges($data);
   }


   public function logDelete($data, $id = null): void
   {
       $presenter = $this->application->getPresenter();
       $before  = $data;
       $userId  = $presenter->getUser()->getId();
       $ssid    = $presenter->getSession()->getId();

       if(PHP_SAPI === 'cli')
       {
           $ipAddr = 'cli';
           $url    = $presenter->getAction(true);
       }
       else
       {
           $ipAddr = filter_input(INPUT_SERVER, 'REMOTE_ADDR');

           try
           {
               $url = $presenter->link('//this');
           }
           catch (InvalidLinkException $e)
           {
               $url = 'InvalidLink: ' . $e->getMessage();
           }
       }

       $data = [
           'name'                => get_class($this),
           'action'                => 'delete',
           'element_id' =>           $id,
           'admin_id'              => $userId,
           'before'                => json_encode($before),
           'created_dt'            => new DateTime(),
           'created_ip'            => $ipAddr,
           'url'                   => $url,
           'ssid'                  => $ssid,
       ];

       $logMapper = $this->container->getByType(\App\Model\Log\LogMapper::class);
       $this->db->insert($logMapper->tableName, $data)->execute();
       $lastInsertId = $this->db->getInsertId();
   }

    public function logChanges(IEntity $entity,?string $action = null): void
    {
        $diff = $this->getDiffData($entity);
        if (empty($diff['before']) && empty($diff['after']))
        {
            return;
        }

        /** @var Presenter $presenter */
        $presenter = $this->application->getPresenter();

        $before  = $diff['before'];
        $after   = $diff['after'];
        $userId  = $presenter->getUser()->getId();
        $ssid    = $presenter->getSession()->getId();

        if(PHP_SAPI === 'cli')
        {
            $ipAddr = 'cli';
            $url    = $presenter->getAction(true);
        }
        else
        {
            $ipAddr = filter_input(INPUT_SERVER, 'REMOTE_ADDR');

            try
            {
                $url = $presenter->link('//this');
            }
            catch (InvalidLinkException $e)
            {
                $url = 'InvalidLink: ' . $e->getMessage();
            }
        }

        $data = [
            'name'                => get_class($entity),
            'action'                => $action,
            'element_id' =>          $entity->getId(),
            'admin_id'              => $userId,
            'before'                => json_encode($before),
            'after'                 => json_encode($after),
            'created_dt'            => new DateTime(),
            'created_ip'            => $ipAddr,
            'url'                   => $url,
            'ssid'                  => $ssid,
        ];

        $logMapper = $this->container->getByType(\App\Model\Log\LogMapper::class);


        $this->db->insert($logMapper->tableName, $data)->execute();
        $lastInsertId = $this->db->getInsertId();

    }

    protected function getDiffData(IEntity $entity): ?array
    {
        return $entity->getDiffData();
    }

}
