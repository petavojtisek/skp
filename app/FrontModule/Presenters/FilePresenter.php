<?php

namespace App\FrontModule\Presenters;

use App\Model\FileManager\FileManagerFacade;
use Nette\Application\Responses\FileResponse;

final class FilePresenter extends FrontPresenter
{
    /** @var FileManagerFacade @inject */
    public $fileManagerFacade;

    public function actionGet(int $id): void
    {
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
