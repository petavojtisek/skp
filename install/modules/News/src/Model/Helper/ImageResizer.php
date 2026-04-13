<?php

namespace App\Modules\News\Model\Helper;

use Nette\Http\FileUpload;
use Nette\Utils\Image;
use Nette\Utils\FileSystem;

class ImageResizer
{
    private string $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = rtrim($storagePath, '/\\');
    }

    /**
     * @param FileUpload $file
     * @param string $subDir Subdirectory within storagePath/news/
     * @return string Filename
     */
    public function processNewsImage(FileUpload $file): string
    {
        if (!$file->isOk() || !$file->isImage()) {
            throw new \InvalidArgumentException("Soubor není platný obrázek.");
        }

        $baseDir = $this->storagePath . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'news';
        $dirs = [
            'origin' => $baseDir . DIRECTORY_SEPARATOR . 'origin',
            '800x600' => $baseDir . DIRECTORY_SEPARATOR . '800x600',
            'thumb' => $baseDir . DIRECTORY_SEPARATOR . 'thumb',
        ];

        foreach ($dirs as $dir) {
            FileSystem::createDir($dir);
        }

        $extension = strtolower(pathinfo($file->getUntrustedName(), PATHINFO_EXTENSION));
        $filename = uniqid('news_', true) . '.' . $extension;

        // Origin
        $file->move($dirs['origin'] . DIRECTORY_SEPARATOR . $filename);

        // 800x600 (Fit means proportional resize)
        $image800 = Image::fromFile($dirs['origin'] . DIRECTORY_SEPARATOR . $filename);
        $image800->resize(800, 600, Image::SHRINK_ONLY);
        $image800->save($dirs['800x600'] . DIRECTORY_SEPARATOR . $filename);

        // Thumb 300x300
        $imageThumb = Image::fromFile($dirs['origin'] . DIRECTORY_SEPARATOR . $filename);
        $imageThumb->resize(300, 300, Image::SHRINK_ONLY);
        $imageThumb->save($dirs['thumb'] . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function deleteNewsImage(string $filename): void
    {
        $baseDir = $this->storagePath . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'news';
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
