<?php

namespace App\FrontModule\Presenters;

use App\Model\FileManager\FileManagerFacade;
use App\Model\System\EncodeDecode;
use App\Presenters\BasePresenter;
use Nette\Application\Responses\FileResponse;

final class FilePresenter extends BasePresenter
{
    /** @var FileManagerFacade @inject */
    public $fileManagerFacade;

    /**
     * Zobrazí/stáhne soubor na základě ID
     * URL: /file/detail/<id>
     */
    public function actionDetail(int|string $id): void
    {
        if(is_string($id) && !is_numeric($id)) {
            $id = EncodeDecode::decodeSmallHash($id);
        }
        $file = $this->fileManagerFacade->getFile($id);

        if (!$file) {
            $this->error('Soubor nebyl nalezen.');
        }

        $physicalPath = $this->fileManagerFacade->getPhysicalPath($file);

        if (!file_exists($physicalPath)) {
            $this->error('Soubor fyzicky neexistuje na serveru.');
        }

        // Pokud jde o obrázek, zobrazíme ho v prohlížeči, jinak vynutíme stažení
        $forceDownload = ($file->getFileType() !== 'image');

        $response = new FileResponse(
            $physicalPath,
            $file->getOriginalName(),
            $file->getMimeType(),
            $forceDownload
        );

        $this->sendResponse($response);
    }

    public function actionGet(int|string $id): void
    {

        if(is_string($id) && !is_numeric($id)) {
            $id = EncodeDecode::decodeSmallHash($id);
        }

        $file = $this->fileManagerFacade->getFile($id);
        if (!$file) {
            $this->error('Soubor nebyl nalezen.');
        }

        $path = $this->fileManagerFacade->getPhysicalPath($file);

        if (!file_exists($path)) {
            $this->error('Fyzický soubor neexistuje.');
        }

        $this->sendResponse(new FileResponse($path, $file->getOriginalName(), $file->getMimeType()));
    }
}
