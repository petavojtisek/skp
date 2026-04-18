<?php
declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use App\Modules\Members\Model\MembersFacade;
use App\Modules\Members\Model\MembersEntity;

$facade = $container->getByType(MembersFacade::class);

$csvFile = __DIR__ . 'members.csv';

if (!file_exists($csvFile)) {
    echo "Soubor members.csv nebyl nalezen v rootu projektu.\n";
    exit(1);
}

$file = fopen($csvFile, 'r');
if (!$file) {
    echo "Nelze otevřít soubor members.csv.\n";
    exit(1);
}

// Skip header
$header = fgetcsv($file);

$count = 0;
while (($data = fgetcsv($file)) !== false) {
    // Mapping based on CSV structure:
    // 0: titule, 1: name, 2: surname, 3: birth_date, 4: street, 5: city, 6: zip, 7: email, 8: phone, 9: note, 10: Č.ÚČTU, 11: last_member_payment

    $entity = new MembersEntity();
    $entity->setDegree($data[0] ?: null);
    $entity->setName($data[1] ?: '');
    $entity->setSurname($data[2] ?: '');

    // Date parsing helper
    $parseDate = function($dateStr) {
        if (!$dateStr) return null;
        $d = DateTime::createFromFormat('d.m.Y', $dateStr);
        if (!$d) {
            $d = DateTime::createFromFormat('j.n.Y', $dateStr);
        }
        return $d ? $d->format('Y-m-d') : null;
    };


    $entity->setBirthDate($parseDate($data[3]));
    $entity->setStreet($data[4] ?: null);
    $entity->setCity($data[5] ?: null);
    $entity->setZip($data[6] ?: null);
    $entity->setEmail($data[7] ?: null);
    $entity->setPhone($data[8] ?: null);
    $entity->setNote($data[9] ?: null);
    $entity->setLastMemberPayment($parseDate($data[11]));
    $entity->setActive(0);
    $entity->setRegistrationEmailDt(new \Dibi\DateTime());
    $entity->setSource(MembersEntity::SOURCE_IMPORT);

    try {
        $facade->saveMember($entity);
        $count++;
        echo "Importován člen: " . $entity->getFullName() . "\n";
    } catch (\Exception $e) {
        echo "Chyba při importu člena " . ($data[1] . ' ' . $data[2]) . ": " . $e->getMessage() . "\n";
    }
}

fclose($file);

echo "\nHotovo. Importováno $count členů.\n";
