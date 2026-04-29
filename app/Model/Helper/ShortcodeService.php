<?php

namespace App\Model\Helper;

use App\Model\FileManager\FileManagerFacade;
use Latte\Engine;

class ShortcodeService
{
    private FileManagerFacade $fileManagerFacade;
    private Engine $latte;
    private string $appDir;

    public function __construct(FileManagerFacade $fileManagerFacade, string $appDir)
    {
        $this->fileManagerFacade = $fileManagerFacade;
        $this->latte = new Engine();
        $this->appDir = $appDir;
    }

    /**
     * Parses [gallery path="..."] shortcodes in content and replaces them with rendered HTML.
     */
    public function parse(string $content): string
    {
        // Debug log
        // file_put_contents($this->appDir . '/../log/shortcode.log', "Parsing content: " . substr($content, 0, 100) . "...\n", FILE_APPEND);

        if (strpos($content, '[gallery') === false) {
            return $content;
        }

        return preg_replace_callback('/\[gallery\s+([^\]]+)\]/', function ($matches) {
            $paramString = html_entity_decode($matches[1]);
            $params = $this->parseParams($paramString);
            // file_put_contents($this->appDir . '/../log/shortcode.log', "Found gallery with params: " . json_encode($params) . "\n", FILE_APPEND);
            $result = $this->renderGallery($params);
            if (empty($result)) {
                return "<!-- Gallery not found or empty: " . htmlspecialchars($paramString) . " -->";
            }
            return $result;
        }, $content);
    }

    private function parseParams(string $paramString): array
    {
        $params = [];
        // Match attribute="value", attribute='value', or attribute=value
        // Also handles multiple quotes like ""path"" or &quot;path&quot;
        preg_match_all('/(\w+)=["\'&quot;]*([^"\'\s&quot;\]]+)["\'&quot;]*/', $paramString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $params[$match[1]] = trim($match[2], "\"' ");
        }
        return $params;
    }

    private function renderGallery(array $params): string
    {
        $path = $params['path'] ?? null;
        if (!$path) return '';

        $type = $params['type'] ?? 'gallery';
        $limit = isset($params['limit']) ? (int)$params['limit'] : 100;

        // Split path to baseType and subDir
        $parts = explode('/', $path, 2);
        $baseType = $parts[0];
        $subDir = $parts[1] ?? '';

        $files = $this->fileManagerFacade->getFilesByPath($baseType, $subDir);
        
        // Filter only images
        $images = array_filter($files, function($file) {
            return $file->getFileType() === 'image';
        });

        if (empty($images)) return '';

        // Select template based on type
        $templateName = $type === 'slider' ? 'slider.latte' : 'gallery.latte';
        $templatePath = $this->appDir . '/FrontModule/templates/components/' . $templateName;

        if (!file_exists($templatePath)) {
            // Fallback to gallery.latte if specific template not found
            $templatePath = $this->appDir . '/FrontModule/templates/components/gallery.latte';
        }

        if (!file_exists($templatePath)) {
            // Very basic fallback if no template exists at all
            $html = '<div class="gallery-fallback row">';
            foreach (array_slice($images, 0, $limit) as $img) {
                $url = '/storage/' . $img->getPath() . '/' . $img->getFileName();
                $html .= '<div class="col-md-3 mb-3"><a href="'.$url.'" data-lightbox="gallery"><img src="'.$url.'" class="img-fluid"></a></div>';
            }
            $html .= '</div>';
            return $html;
        }

        return $this->latte->renderToString($templatePath, [
            'images' => $images,
            'params' => $params,
            'limit' => $limit
        ]);
    }
}
