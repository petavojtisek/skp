<?php

namespace App\Presenters;

use App\Model\FileManager\FileManagerFacade;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Presenter;

final class FilePresenter extends Presenter
{
    /** @inject */
    public FileManagerFacade $fileManagerFacade;

    /**
     * Zobrazí/stáhne soubor na základě ID
     * URL: /file/detail/<id>
     */
    public function actionDetail(int $id): void
    {
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
}
