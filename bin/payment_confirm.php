<?php
declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../test_scripts/bootstrap.php';

use App\Modules\Members\Model\MembersFacade;
use App\Model\Emails\EmailsFacade;

$memberIds = [

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

    $memberIds = $db->query("SELECT * FROM `members_live` WHERE
                        `active` = '1'
                        AND `last_member_payment`  IS NOT NULL
                        AND `payment_confirm_email_dt` IS NULL  ")->fetchPairs('member_id', 'member_id');

    $memberIds = [36=>36];
    foreach ($memberIds as $memberId) {
        $membersFacade->sendPaymentConfirmationEmail($memberId);
        echo "Email odeslan na " . $memberId . "\n";
    }
} catch (\Exception $e) {
    echo "Chyba: " . $e->getMessage() . "\n";
}
