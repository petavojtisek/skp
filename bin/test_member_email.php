<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../test_scripts/bootstrap.php';

use App\Modules\Members\Model\MembersFacade;
use App\Model\Emails\EmailsFacade;

$memberId = (int)($argv[1] ?? 0);
$action = $argv[2] ?? 'registration';

if (!$memberId) {
    echo "Použití: php test_member_email.php <member_id> [action]\n";
    echo "Dostupné akce:\n";
    echo " - registration (výchozí)\n";
    echo " - acceptance\n";
    echo " - payment_confirmation\n";
    echo " - payment_reminder\n";
    die();
}

try {
    /** @var EmailsFacade $emailsFacade */
    $emailsFacade = $container->getByType(EmailsFacade::class);
    
    /** @var MembersFacade $membersFacade */
    $membersFacade = $container->getByType(MembersFacade::class);

    // Vynutíme debug mode pro testovací účely
    $reflection = new ReflectionClass($emailsFacade);
    $property = $reflection->getProperty('debugMode');
    $property->setAccessible(true);
    $property->setValue($emailsFacade, true);

    echo "Provádím akci '$action' pro člena ID: $memberId (Debug Mode VYNUCEN)...\n";
    
    switch ($action) {
        case 'registration':
            $membersFacade->sendRegistrationEmail($memberId);
            break;
        case 'acceptance':
            $membersFacade->sendAcceptanceEmail($memberId);
            break;
        case 'payment_confirmation':
            $membersFacade->sendPaymentConfirmationEmail($memberId);
            break;
        case 'payment_reminder':
            $membersFacade->sendPaymentReminderEmail($memberId);
            break;
        default:
            die("Neznámá akce: $action\n");
    }
    
    echo "Hotovo. Zkontrolujte složku temp/sent_emails/ pro vygenerovaný .eml soubor.\n";

} catch (\Exception $e) {
    echo "Chyba: " . $e->getMessage() . "\n";
}
