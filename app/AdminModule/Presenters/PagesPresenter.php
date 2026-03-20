<?php

namespace App\AdminModule\Presenters;

final class PagesPresenter extends AdminPresenter
{
    /** @var int|null @persistent */
    public $id;

    public function renderDefault(): void
    {
        $this->template->title = 'Stránky';
        
        $this->template->pages = [
            (object)['id' => 1, 'name' => 'Úvod', 'url' => '/', 'active' => true, 'children' => []],
            (object)['id' => 2, 'name' => 'O nás', 'url' => '/o-nas', 'active' => true, 'children' => [
                (object)['id' => 3, 'name' => 'Historie', 'url' => '/o-nas/historie', 'active' => true, 'children' => []],
                (object)['id' => 4, 'name' => 'Tým', 'url' => '/o-nas/tym', 'active' => false, 'children' => [
                     (object)['id' => 6, 'name' => 'Vedení', 'url' => '/o-nas/tym/vedeni', 'active' => true, 'children' => []],
                ]],
            ]],
            (object)['id' => 5, 'name' => 'Kontakt', 'url' => '/kontakt', 'active' => true, 'children' => []],
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

    public function handleMove(?int $id = null, ?int $parentId = null, ?int $position = null): void
    {
        if ($id) {
            $this->flashMessage("Stránka byla přesunuta (ID: $id, Parent: $parentId, Pos: $position).", 'success');
        }

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }
}
