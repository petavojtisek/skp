<?php
declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../test_scripts/bootstrap.php';

use App\Modules\Members\Model\MembersFacade;
use App\Model\Emails\EmailsFacade;

$memberIds = [
    1 => 1,
    2 => 2,
    3 => 3,
    4 => 4,
    5 => 5,
    6 => 6,
    7 => 7,
    8 => 8,
    9 => 9,
    10 => 10,
    11 => 11,
    12 => 12,
    13 => 13,
    14 => 14,
    15 => 15,
    16 => 16,
    17 => 17,
    18 => 18,
    19 => 19,
    20 => 20,
    21 => 21,
    22 => 22,
    23 => 23,
    24 => 24,
    25 => 25,
    26 => 26,
    27 => 27,
    28 => 28,
    30 => 30,
    31 => 31,
    32 => 32,
    33 => 33,
    34 => 34,
    35 => 35,
    37 => 37,
    38 => 38,
    39 => 39,
];

try {
    $emailsFacade = $container->getByType(EmailsFacade::class);
    $db = $container->getByType(\Dibi\Connection::class);


    /** @var MembersFacade $membersFacade */
    $membersFacade = $container->getByType(MembersFacade::class);

// Vynutíme debug mode pro testovací účely

    $reflection = new ReflectionClass($emailsFacade);
    $property = $reflection->getProperty('debugMode');
    $property->setAccessible(true);
    $property->setValue($emailsFacade, true);


//$memberIds = $db->query("SELECT member_id FROM `members_live` WHERE `active` = '1' AND `registration_confirm_email_dt` IS NULL ")->fetchPairs('member_id', 'member_id');

$memberIds = [36=>36];
    foreach ($memberIds as $memberId) {
        $membersFacade->sendAcceptanceEmail($memberId);
        echo "Email odeslan na " . $memberId . "\n";
    }
} catch (\Exception $e) {
    echo "Chyba: " . $e->getMessage() . "\n";
}
