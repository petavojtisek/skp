<?php

declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../test_scripts/bootstrap.php';



$db = $container->getByType(\Dibi\Connection::class);

$rows = $db->query('select * from content_version')->fetchAll();
foreach ($rows as $row) {
// html_entity_decode převede &aacute; na á a &nbsp; na mezeru
    $clean = html_entity_decode($row->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Odstraň i ty zbloudilé &nbsp; které tam TinyMCE sype (pokud chceš)
    $clean = str_replace("\xc2\xa0", ' ', $clean);

    if ($clean !== $row->text) {
        try {
            $db->update('content_version', ['content' => $clean])->where('element_id' . ' = %i', $row->element_id)->execute();
        }catch (\Dibi\Exception $e){
            echo $e->getMessage();
        }
    }
}

