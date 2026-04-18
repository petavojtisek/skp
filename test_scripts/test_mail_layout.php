<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php';

use App\Modules\Members\Model\MembersFacade;
use App\Model\Emails\EmailsFacade;

$memberId = 36;

try {
    /** @var EmailsFacade $emailsFacade */
    $emailsFacade = $container->getByType(EmailsFacade::class);
    
    /** @var MembersFacade $membersFacade */
    $membersFacade = $container->getByType(MembersFacade::class);

    // Vynutíme debug mode pro testovací účely přímo na objektu (pomocí reflexe, protože je private)
    $reflection = new ReflectionClass($emailsFacade);
    $property = $reflection->getProperty('debugMode');
    $property->setAccessible(true);
    $property->setValue($emailsFacade, true);

    echo "Pokouším se odeslat registrační e-mail pro člena ID: $memberId (Debug Mode VYNUCEN)\n";
    
    $membersFacade->sendRegistrationEmail($memberId);
    
    echo "Akce dokončena. Zkontrolujte složku temp/sent_emails/ pro vygenerovaný .eml soubor.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
