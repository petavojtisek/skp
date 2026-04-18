<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../test_scripts/bootstrap.php';

use App\Modules\Members\Model\MembersFacade;

$memberId = (int)($argv[1] ?? 0);
$force = isset($argv[2]) && $argv[2] === 'force';

if (!$memberId) {
    die("Použití: php generate_member_pdf.php <member_id> [force]\n");
}

try {
    /** @var MembersFacade $membersFacade */
    $membersFacade = $container->getByType(MembersFacade::class);

    echo "Generuji registrační PDF pro člena ID: $memberId" . ($force ? " (VYNUCENO)" : "") . "...\n";
    $path = $membersFacade->generateRegistrationConfirmation($memberId, $force);
    
    echo "Hotovo. Soubor uložen do: $path\n";

} catch (\Exception $e) {
    echo "Chyba: " . $e->getMessage() . "\n";
}
