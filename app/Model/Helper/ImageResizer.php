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
     * @param string $modulePath Subdirectory within storagePath
     * @return string Filename
     */
    public function processNewsImage(FileUpload $file, string $modulePath): string
    {
        if (!$file->isOk() || !$file->isImage()) {
            throw new \InvalidArgumentException("Soubor není platný obrázek.");
        }

        $baseDir = $this->storagePath . DIRECTORY_SEPARATOR . $modulePath;

        foreach ($this->dirs as $dirName => $dirData) {
            FileSystem::createDir($baseDir . DIRECTORY_SEPARATOR . $dirName);
        }

        $extension = strtolower(pathinfo($file->getUntrustedName(), PATHINFO_EXTENSION));
        $filename = uniqid('news_', true) . '.' . $extension;

        // Origin path
        $originPath = $baseDir . DIRECTORY_SEPARATOR . 'origin' . DIRECTORY_SEPARATOR . $filename;

        // Origin
        $file->move($originPath);

        foreach ($this->dirs as $dirName => $dirData) {
            if($dirName === 'origin') {
                continue;
            }

            $img = Image::fromFile($originPath);
            $img->resize($dirData['width'], $dirData['height'], Image::SHRINK_ONLY);
            $img->save($baseDir . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $filename);
        }

        return $filename;
    }

    public function deleteNewsImage(string $filename, string $modulePath): void
    {
        $baseDir = $this->storagePath . DIRECTORY_SEPARATOR . $modulePath;
        foreach (array_keys($this->dirs) as $dirName) {
            $path = $baseDir . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($path)) {
                FileSystem::delete($path);
            }
        }
    }
}
