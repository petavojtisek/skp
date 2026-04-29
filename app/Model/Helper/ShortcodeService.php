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
        // Podpora pro nový formát ((gallery|path:value)) i starší varianty
        if (strpos($content, 'gallery') === false) {
            return $content;
        }

        // Regex pro ((gallery ...)), {{gallery ...}} i [gallery ...]
        return preg_replace_callback('/(?:\(\(|\{\{|\[)gallery[\| ]([^\]\} \)]+)(?:\)\)|\}\}|\])/', function ($matches) {
            $paramString = html_entity_decode($matches[1]);

            if (strpos($paramString, '|') !== false || strpos($paramString, ':') !== false) {
                $params = $this->parseNewParams($paramString);
            } else {
                $params = $this->parseParams($paramString);
            }

            $result = $this->renderGallery($params);
            if (empty($result)) {
                return "<!-- Gallery not found or empty: " . htmlspecialchars($paramString) . " -->";
            }
            return $result;
        }, $content);
    }

    private function parseNewParams(string $paramString): array
    {
        $params = [];
        $parts = explode('|', $paramString);
        foreach ($parts as $part) {
            $kv = explode(':', $part, 2);
            if (count($kv) === 2) {
                $val = trim($kv[1], "\"' ");
                $params[trim($kv[0])] = str_replace('"', '', $val);
            } elseif (!isset($params['path']) && !empty(trim($kv[0]))) {
                $val = trim($kv[0], "\"' ");
                $params['path'] = str_replace('"', '', $val);
            }
        }
        return $params;
    }
    private function parseParams(string $paramString): array
    {
        $params = [];
        preg_match_all('/(\w+)=["\'&quot;]*([^"\'\s&quot;\]]+)["\'&quot;]*/', $paramString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $val = trim($match[2], "\"' ");
            $params[$match[1]] = str_replace('"', '', $val);
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

        // Sort images if needed (already sorted by sort_order from mapper)

        // Select template based on type
        $templateName = $type === 'slider' ? 'slider.latte' : 'gallery.latte';
        $templatePath = $this->appDir . '/FrontModule/templates/components/' . $templateName;

        if (!file_exists($templatePath)) {
            $templatePath = $this->appDir . '/FrontModule/templates/components/gallery.latte';
        }

        // Prepare image data with correct URLs
        $imageData = [];
        foreach ($images as $img) {
            $imageData[] = [
                'url' => '/file/get/' . $img->getEncodedId(),
                'original_name' => $img->getOriginalName(),
                'is_main' => $img->is_main,
                'entity' => $img
            ];
        }

        if (!file_exists($templatePath)) {
            $html = '<div class="gallery-fallback row">';
            foreach (array_slice($imageData, 0, $limit) as $img) {
                $html .= '<div class="col-md-3 mb-3"><a href="'.$img['url'].'" data-lightbox="gallery"><img src="'.$img['url'].'" class="img-fluid" alt="'.$img['original_name'].'"></a></div>';
            }
            $html .= '</div>';
            return $html;
        }

        return $this->latte->renderToString($templatePath, [
            'images' => $imageData,
            'params' => $params,
            'limit' => $limit
        ]);
    }}
