<?php

namespace App\AdminModule\Presenters;

final class PagesPresenter extends AdminPresenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'Stránky';
        
        $this->template->pages = [
            ['id' => 1, 'name' => 'Úvod', 'url' => '/', 'active' => true],
            ['id' => 2, 'name' => 'O nás', 'url' => '/o-nas', 'active' => true, 'children' => [
                ['id' => 3, 'name' => 'Historie', 'url' => '/o-nas/historie', 'active' => true],
                ['id' => 4, 'name' => 'Tým', 'url' => '/o-nas/tym', 'active' => false],
            ]],
            ['id' => 5, 'name' => 'Kontakt', 'url' => '/kontakt', 'active' => true],
        ];
    }
    
    public function renderEdit(?int $id = null): void
    {
        $this->template->title = $id ? 'Editace stránky' : 'Nová stránka';
        $this->template->pageId = $id;
        
        $this->template->pageObjects = [
            ['id' => 1, 'type' => 'Obsah', 'code' => 'content.about_us', 'title' => 'Hlavní text', 'content' => '<p>Jsme sdružení...</p>'],
            ['id' => 2, 'type' => 'Galerie', 'code' => 'gallery.about', 'title' => 'Fotky z akcí', 'content' => '[Galerie 5 obrázků]'],
        ];
    }
}
