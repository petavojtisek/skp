<?php
/**
 * Created by PhpStorm.
 * User: PetrV
 * Date: 3.2.2019
 * Time: 9:54
 */

namespace App\Model\Base;


class BaseDao extends ADao
{


    public function saveTranslation(int $primaryId, int $langId, string $item): void
    {
            $this->mapper->saveTranslation($primaryId, $langId, $item);
    }

    public function getTranslations(int $id): array
    {
        $list =  $this->mapper->getTranslations($id);
        $translates = [];
        if($list){
            foreach ($list as $lang_id=> $item) {
                $translates[$lang_id] = new BaseTranslateEntity(
                    [
                        'entity_id' => $id,
                        'lang_id' => $lang_id,
                        'value' => $item
                    ]
                );
            }
        }
        return $translates;
    }

}
