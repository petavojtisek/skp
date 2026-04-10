<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Dibi\Connection;
use App\Model\System\Cache;

final class RouterFactory
{
	use Nette\StaticClass;

    public static function createRouter(
        Connection $db,
        Cache $cache
    ): RouteList {
		$router = new RouteList;

		$router[] = new Route('admin/<presenter>/<action>[/<id>]', [
			'module' => 'Admin',
			'presenter' => 'Dashboard',
			'action' => 'default'
		]);


        $router[] = new Route('file/<action>/<id>', [
            'module' => 'Front',
            'presenter' => 'File',
        ]);

        $router[] = new PageRewriteRoute($db, $cache);

        $router[] = new Route('<presenter>/<action>[/<id>]', [
                    'module' => 'Front',
                    'presenter' => 'Home',
                    'action' => 'default'
        ]);

		return $router;
	}
}

class PageRewriteRoute implements Nette\Application\IRouter
{
    private Connection $db;
    private Cache $cache;

    public function __construct(Connection $db, Cache $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    public function match(Nette\Http\IRequest $httpRequest): ?array
    {
        $this->loadConstants();

        $url = $httpRequest->getUrl();
        $path = ltrim($url->getPathInfo(), '/');
        $domain = $url->getHost();

        // Skip admin and file
        if (str_starts_with($path, 'admin') || str_starts_with($path, 'file')) {
            return null;
        }

        // 1. Presentation
        $pId = $this->cache->load('router_presentation_id_' . $domain, function() use ($domain) {
            $p = $this->db->select('presentation_id, presentation_status')
                ->from('presentation')
                ->where('domain = %s', $domain)
                ->fetch();

            if (!$p || $p->presentation_status != C_PRESENTATION_STATUS_ACTIVE) {
                $p = $this->db->select('presentation_id, presentation_status')
                    ->from('presentation')
                    ->where('is_default = 1')
                    ->fetch();
            }
            return $p ? (int)$p->presentation_id : null;
        }, ['presentation']);

        if (!$pId) return null;

        $pageId = null;

        if ($path === '') {
            // 2. Homepage (is_homepage = 1)
            $pageId = $this->cache->load('router_homepage_id_' . $pId, function() use ($pId) {
                $id = $this->db->select('p.page_id')
                    ->from('page')->as('p')
                    ->innerJoin('spec_param_page')->as('sp')->on('p.page_id = sp.page_id')
                    ->where('p.presentation_id = %i', $pId)
                    ->and('p.page_status = %i', C_PRESENTATION_STATUS_ACTIVE)
                    ->and('sp.name = %s', 'is_homepage')
                    ->and('sp.value = %s', '1')
                    ->fetchSingle();

                if (!$id) {
                    $id = $this->db->select('page_id')
                        ->from('page')
                        ->where('presentation_id = %i', $pId)
                        ->and('page_status = %i', C_PRESENTATION_STATUS_ACTIVE)
                        ->and('page_parent_id = 0')
                        ->orderBy('position ASC')
                        ->fetchSingle();
                }
                return $id ? (int)$id : null;
            }, ['page', 'spec_param_page']);
        } elseif (preg_match('#^(.+)\.html$#', $path, $matches)) {
            // 3. Rewrite ({slug}.html)
            $rewrite = $matches[1];
            $pageId = $this->cache->load('router_rewrite_id_' . $pId . '_' . $rewrite, function() use ($rewrite, $pId) {
                $id = $this->db->select('page_id')
                    ->from('page')
                    ->where('page_rewrite = %s', $rewrite)
                    ->and('presentation_id = %i', $pId)
                    ->and('page_status = %i', C_PRESENTATION_STATUS_ACTIVE)
                    ->fetchSingle();
                return $id ? (int)$id : null;
            }, ['page']);
        }

        if ($pageId) {
            return [
                'presenter' => 'Front:Home',
                'action' => 'default',
                'page_id' => $pageId,
            ];
        }

        return null;
    }

    public function constructUrl(array $params, Nette\Http\UrlScript $refUrl): ?string
    {
        $presenter = $params['presenter'] ?? null;
        if ($presenter !== 'Front:Home' && $presenter !== 'Home') return null;
        if (($params['action'] ?? null) !== 'default' || !isset($params['page_id'])) return null;

        $pageId = (int)$params['page_id'];

        // Check if this page is the homepage
        $isHomepage = $this->cache->load('router_is_homepage_' . $pageId, function() use ($pageId) {
            return (bool)$this->db->select('page_id')
                ->from('spec_param_page')
                ->where('page_id = %i', $pageId)
                ->and('name = %s', 'is_homepage')
                ->and('value = %s', '1')
                ->fetchSingle();
        }, ['spec_param_page']);

        if ($isHomepage) {
            $url = new Nette\Http\Url($refUrl);
            $url->setPath($refUrl->getBasePath()); // Return root path for homepage
            unset($params['page_id'], $params['presenter'], $params['action']);
            $url->setQuery($params);
            return $url->getAbsoluteUrl();
        }

        $rewrite = $this->cache->load('router_page_rewrite_' . $pageId, function() use ($pageId) {
            return $this->db->select('page_rewrite')
                ->from('page')
                ->where('page_id = %i', $pageId)
                ->fetchSingle();
        }, ['page']);

        if ($rewrite) {
            $url = new Nette\Http\Url($refUrl);
            $url->setPath($refUrl->getBasePath() . $rewrite . '.html');
            unset($params['page_id'], $params['presenter'], $params['action']);
            $url->setQuery($params);
            return $url->getAbsoluteUrl();
        }

        return null;
    }

    private function loadConstants(): void
    {
        $constants = $this->cache->load('lookup_constants', function() {
            return $this->db->select('constant, lookup_id')
                ->from('lookup')
                ->where('constant IS NOT NULL AND constant != ""')
                ->fetchPairs('constant', 'lookup_id');
        }, ['lookup']);

        foreach ($constants as $name => $id) {
            $constName = 'C_' . strtoupper((string) $name);
            if (!defined($constName)) {
                define($constName, $id);
            }
        }
    }
}
