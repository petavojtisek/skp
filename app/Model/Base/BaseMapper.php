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

    public function save(IEntity $entity): IEntity
    {

        $entity = parent::save($entity);
        $this->logChanges($entity, 'save');

        if(isset($this->translateTableName) and $entity->getId() and $entity->hasTranslates())
        {
            $this->deleteTranslations($entity->getId());
            foreach ($entity->getTranslates() as $langId => $translationEntity)
            {
                $this->saveTranslation($entity->getId(), $langId, $translationEntity->getValue());
            }
        }

        return $entity;
    }

    public function saveTranslation(int $primaryId, int $langId, string $item): void
    {
        $this->db->query("REPLACE INTO {$this->translateTableName}", [
            $this->translatePrimaryKey=>$primaryId,
            $this->translateLangId => $langId,
            $this->translateValueKey => $item,
        ]);
    }

    public function deleteTranslations(int $primaryId): void
    {
        $this->db->delete($this->translateTableName)->where( $this->translatePrimaryKey.' = %i', $primaryId)->execute();
    }


    /**
     * Get values from translation table
     */
    public function getTranslations(int $id): array
    {
        return $this->db->select($this->translateLangId .','.$this->translateValueKey)
            ->from($this->translateTableName)
            ->where($this->translatePrimaryKey.'= %i', $id)
            ->fetchPairs($this->translateLangId , $this->translateValueKey);
    }

    public function delete(mixed $id): mixed
    {
        $data = $this->find($id);
        return  parent::delete($id);
        $this->logDelete($data, $id);
        if(isset($this->translateTableName) and !empty($this->translateTableName))
        {
            $this->deleteTranslations($id);
        }
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

       $reflection = new \ReflectionClass($this);
       $module = str_replace('Mapper', '', $reflection->getShortName());
       $name = ($module . ' ID: ' . $id);

       $data = [
           'module'                => $module,
           'name'                  => $name,
           'action'                => 'delete',
           'element_id' =>         $id,
           'admin_id'              => $userId,
           'before'                => json_encode($before),
           'created_dt'            => new DateTime(),
           'created_ip'            => $ipAddr,
           'url'                   => $url,
           'ssid'                  => $ssid,
       ];

       $logMapper = $this->container->getByType(\App\Model\Log\LogMapper::class);
       $this->db->insert($logMapper->tableName, $data)->execute();

   }

    public function logChanges(IEntity $entity,?string $action = null): void
    {
        $diff = $this->getDiffData($entity);
        if (empty($diff['before']) and empty($diff['after']))
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

        $id = 0;
        if ($entity) {
            $id = $entity->getId();
            $reflection = new \ReflectionClass($entity);
            $module = str_replace('Entity', '', $reflection->getShortName());
            $fullData = $entity->getEntityData();
        } else {
            $reflection = new \ReflectionClass($this);
            $module = str_replace('Mapper', '', $reflection->getShortName());
        }
        $name = $fullData['user_name'] ?? $fullData['name'] ?? $fullData['title'] ?? $fullData['item'] ?? ($module . ' ID: ' . $id);

        $data = [
            'module'                => $module,
            'name'                => $name,
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
        $diffs  = $entity->getDiffData();

        if($entity->hasTranslates()){
            $diffs =  $entity->getDiffData();
            foreach ($entity->getTranslates() as $langId => $translationEntity)
            {
               $d  = $translationEntity->getDiffData();
               if(!empty($d['before']) or !empty($d['after']))
                {
                    $diffs['translates'][$langId] = $d;
                }
            }
        }
        return $diffs;
    }

}
