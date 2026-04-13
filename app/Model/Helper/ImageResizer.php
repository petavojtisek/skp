<?php

namespace App\Model\Helper;

use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;

class ImageResizer
{
    private string $storagePath;

    public array $dirs = [
        'origin' => 'origin',
        '800x600' => ['width'=> 800, 'height'=>600],
        'thumb' => ['width'=> 300, 'height'=>300],
    ];

    public function __construct(string $storagePath)
    {
        $this->storagePath = rtrim($storagePath, '/\\');
    }


    /**
     * @param FileUpload $file
     * @param string $subDir Subdirectory within storagePath
     * @return string Filename
     */
    public function processNewsImage(FileUpload $file, string $modulePath): string
    {
        xdebug_break();
        if (!$file->isOk() || !$file->isImage()) {
            throw new \InvalidArgumentException("Soubor není platný obrázek.");
        }

        $baseDir = $this->storagePath . DIRECTORY_SEPARATOR .  DIRECTORY_SEPARATOR . $modulePath;


        foreach ($this->dirs as $dirPath=>$dirData) {
            FileSystem::createDir($dirPath);
        }

        $extension = strtolower(pathinfo($file->getUntrustedName(), PATHINFO_EXTENSION));
        $filename = uniqid('news_', true) . '.' . $extension;

        // Origin
        $file->move($this->dirs['origin'] . DIRECTORY_SEPARATOR . $filename);

        foreach ($this->dirs as $dirPath=>$dirData) {
            if($dirPath === 'origin') {
                continue;
            }

            $image800 = Image::fromFile($this->dirs['origin'] . DIRECTORY_SEPARATOR . $filename);
            $image800->resize($dirData['width'], $dirData['height'], Image::SHRINK_ONLY);
            $image800->save($dirPath . DIRECTORY_SEPARATOR . $filename);
        }


        return $filename;
    }

    public function deleteNewsImage(string $filename, string $modulePath): void
    {
        $baseDir = $this->storagePath . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $modulePath;
        $paths = [
            $baseDir . DIRECTORY_SEPARATOR . 'origin' . DIRECTORY_SEPARATOR . $filename,
            $baseDir . DIRECTORY_SEPARATOR . '800x600' . DIRECTORY_SEPARATOR . $filename,
            $baseDir . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR . $filename,
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                FileSystem::delete($path);
            }
        }
    }
}
