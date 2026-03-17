<?php

namespace App\AdminModule\Presenters;

use App\Model\FileManager\FileManagerFacade;
use App\Model\FileManager\FileManagerEntity;
use Nette\Application\Responses\JsonResponse;
use Nette\Utils\FileSystem;
use Nette\Utils\Random;

final class FilesPresenter extends AdminPresenter
{
    /** @inject */
    public FileManagerFacade $fileManagerFacade;

    /** @persistent */
    public ?string $sourceType = 'general';

    /** @persistent */
    public ?int $elementId = null;

    public function renderDefault(): void
    {
        $this->template->title = 'Správce souborů';
        $this->template->files = $this->fileManagerFacade->getFilesByElement($this->sourceType, (int)$this->elementId);
    }

    /**
     * Zpracování nahrávání souborů přes Dropzone (včetně chunků)
     */
    public function handleUpload(): void
    {
        $file = $this->getHttpRequest()->getFile('file');
        if (!$file) {
            $this->sendResponse(new JsonResponse(['status' => 'error', 'message' => 'Žádný soubor nebyl přijat.']));
        }

        // Dropzone parametry pro chunky
        $chunkIndex = (int) $this->getHttpRequest()->getPost('dzchunkindex');
        $totalChunks = (int) $this->getHttpRequest()->getPost('dztotalchunkcount');
        $uuid = $this->getHttpRequest()->getPost('dzuuid');

        $tempDir = $this->context->getParameters()['tempDir'] . '/uploads/' . $uuid;
        FileSystem::createDir($tempDir);

        // Uložíme chunk
        $file->move($tempDir . '/' . $chunkIndex);

        // Pokud jsou všechny chunky nahrané, poskládáme soubor
        if ($chunkIndex + 1 === $totalChunks) {
            $this->processCompleteUpload($tempDir, $totalChunks, $file->getUntrustedName());
        }

        $this->sendResponse(new JsonResponse(['status' => 'success', 'chunk' => $chunkIndex]));
    }

    public function handleDeleteFile(int $id): void
    {
        $this->fileManagerFacade->deleteFile($id);
        $this->flashMessage('Soubor byl smazán.');
        $this->redirect('this');
    }

    private function processCompleteUpload(string $tempDir, int $totalChunks, string $originalName): void
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $newFileName = sprintf('%s_%s.%s', date('Ymd_His'), Random::generate(8), $extension);
        
        // Cílová cesta: storage/{sourceType}/{rok-mesic}/
        $relativeDir = $this->sourceType . '/' . date('Y-m');
        $finalDir = $this->context->getParameters()['storageDir'] . '/' . $relativeDir;
        FileSystem::createDir($finalDir);

        $finalPath = $finalDir . '/' . $newFileName;

        // Poskládání souboru z chunků
        $out = fopen($finalPath, "wb");
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFile = $tempDir . '/' . $i;
            $in = fopen($chunkFile, "rb");
            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }
            fclose($in);
        }
        fclose($out);

        // Úklid tempu
        FileSystem::delete($tempDir);

        // Zápis do DB
        $entity = new FileManagerEntity();
        $entity->setSourceType($this->sourceType);
        $entity->setElementId($this->elementId);
        $entity->setOriginalName($originalName);
        $entity->setFileName($newFileName);
        $entity->setPath($relativeDir);
        $entity->setExtension($extension); // Tuto metodu musím přidat do entity
        $entity->setMimeType(mime_content_type($finalPath));
        $entity->setSize(filesize($finalPath));
        $entity->setAdminId($this->adminId);
        
        // Rozlišení typu
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $entity->setFileType(in_array($extension, $imageExtensions) ? 'image' : 'document');

        $this->fileManagerFacade->saveFile($entity);
    }
}
