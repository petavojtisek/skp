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

    /** @inject */
    public \App\Model\Page\PageFacade $pageFacade;

    /** @persistent */
    public string $baseType = 'images'; // images | documents | pages

    /** @persistent */
    public string $subDir = '';

    /** @persistent */
    public ?int $elementId = null;

    /** @persistent */
    public string $sourceType = 'general';

    /** @persistent */
    public bool $picker = false;

    /** @persistent */
    public ?string $callback = null;

    public function actionDefault(): void
    {

        // For picker, we allow access if the user has been recently logged in (via cookie)
        // even if the main session expired, to avoid interrupting the workflow.

        if ($this->picker and (!isset($_SERVER['HTTP_REFERER']) or !preg_match("#admin#",$_SERVER['HTTP_REFERER'])) ){
            $this->terminate();
            /*
            && !$this->getUser()->isLoggedIn()) {

            $cookie = $this->getHttpRequest()->getCookie('admin_active');
            if ($cookie) {
                // We set the adminId from cookie if session is dead but cookie lives
                $this->adminId = (int)$cookie;
            } else {
                // If no session and no cookie, they must log in
                $this->redirect('Sign:in');
            }
            */
        }

        if ($this->picker) {
            $this->setView('picker');
        }
        $this->template->baseType = $this->baseType;
        $this->template->callback = $this->callback;
    }


    public function renderPicker()
    {
        $this->setRenderParams();
    }


    public function renderDefault(): void
    {
        $this->setRenderParams();

    }

    private function setRenderParams()
    {
        $this->template->title = 'Správce souborů';

        if ($this->baseType === 'pages') {
            $presentationId = $this->loggedUserEntity->active_presentation_id;
            $this->template->pagesTree = $this->pageFacade->getPages($presentationId);
        } else {
            // Ensure base directories exist
            $this->fileManagerFacade->createDirectory('images', '');
            $this->fileManagerFacade->createDirectory('documents', '');

            $this->template->directories = $this->fileManagerFacade->getDirectories($this->baseType, $this->subDir);
            $this->template->files = $this->fileManagerFacade->getFilesByPath($this->baseType, $this->subDir);
        }

        $this->template->baseType = $this->baseType;
        $this->template->subDir = $this->subDir;
    }



    public function handleSwitchBase(string $type): void
    {
        $this->baseType = $type;
        $this->subDir = '';
        $this->redirect('this');
    }

    public function handleOpenFolder(string $name): void
    {
        $this->subDir = ($this->subDir ? $this->subDir . '/' : '') . $name;
        $this->redirect('this');
    }

    public function handleGoUp(): void
    {
        $parts = explode('/', $this->subDir);
        array_pop($parts);
        $this->subDir = implode('/', $parts);
        $this->redirect('this');
    }

    public function handleCreateFolder(string $name): void
    {
        if (!$name) {
            $this->flashMessage('Název složky nesmí být prázdný.', 'error');
            $this->redirect('this');
        }

        $cleanName = \Nette\Utils\Strings::webalize($name);
        $newPath = ($this->subDir ? $this->subDir . '/' : '') . $cleanName;

        $this->fileManagerFacade->createDirectory($this->baseType, $newPath);
        $this->flashMessage('Složka byla vytvořena.');
        $this->redirect('this');
    }

    public function handleUpload(): void
    {
        $file = $this->getHttpRequest()->getFile('file');
        $uuid = $this->getHttpRequest()->getPost('dzuuid');

        if (!$file or !$uuid) {
            $this->sendResponse(new JsonResponse(['status' => 'error', 'message' => 'Chybějící data souboru nebo UUID.']));
        }

        $chunkIndex = (int) $this->getHttpRequest()->getPost('dzchunkindex');
        $totalChunks = (int) $this->getHttpRequest()->getPost('dztotalchunkcount');

        $tempDir = $this->fileManagerFacade->getTempDir() . '/uploads/' . $uuid;
        FileSystem::createDir($tempDir);
        $file->move($tempDir . '/' . $chunkIndex);

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

        // Determine base type if not fixed
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $detectedBaseType = in_array($extension, $imageExtensions) ? 'images' : 'documents';

        // If we are in a specific view, use that baseType, otherwise use detected
        $targetBase = $this->baseType ?: $detectedBaseType;
        $targetPath = $targetBase . ($this->subDir ? '/' . $this->subDir : '');

        $finalDir = $this->fileManagerFacade->getStorageDir() . '/' . $targetPath;
        FileSystem::createDir($finalDir);

        $finalPath = $finalDir . '/' . $newFileName;

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

        FileSystem::delete($tempDir);

        $entity = new FileManagerEntity();
        $entity->setSourceType($this->sourceType);
        $entity->setElementId($this->elementId);
        $entity->setOriginalName($originalName);
        $entity->setFileName($newFileName);
        $entity->setPath($targetPath);
        $entity->setExtension($extension);
        $entity->setMimeType(mime_content_type($finalPath));
        $entity->setSize(filesize($finalPath));
        $entity->setAdminId($this->adminId);
        $entity->setFileType($detectedBaseType === 'images' ? 'image' : 'document');

        $this->fileManagerFacade->saveFile($entity);
    }
}
