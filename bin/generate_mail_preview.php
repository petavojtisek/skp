<?php declare(strict_types=1);

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../test_scripts/bootstrap.php';

use App\Modules\Members\Model\MembersFacade;

$memberId = (int)($argv[1] ?? 0);
$template = $argv[2] ?? 'registration';

if (!$memberId) {
    echo "Použití: php generate_mail_preview.php <member_id> [template_name]\n";
    echo "Dostupné šablony:\n";
    echo " - registration (výchozí)\n";
    echo " - acceptance\n";
    echo " - payment_confirmation\n";
    echo " - payment_reminder\n";
    echo " - generic\n";
    die();
}

try {
    /** @var MembersFacade $membersFacade */
    $membersFacade = $container->getByType(MembersFacade::class);
    $member = $membersFacade->getMember($memberId);

    if (!$member) die("Člen s ID $memberId nenalezen.\n");

    $latte = new \Latte\Engine();

    // Logo pro náhled (Base64)
    $logoPath = ASSETS_DIR . DS . 'images' . DS . 'logo-v1-spolek.jpeg';
    $base64 = file_exists($logoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath)) : null;

    // Načteme konstanty
    $constantsFacade = $container->getByType(\App\Modules\SystemConstants\Model\SystemConstantsFacade::class);
    $constants = $constantsFacade->getAllSystemConstants();
    $map = [];
    foreach ($constants as $c) {
        $map[$c->getCode()] = $c->getValue();
    }

    $text= "<h1>toto je nadpis</h1><br>
            <p>Toto je text</br>
            ";

    $qrCodePath = $membersFacade->generateQr($memberId);
    $params = array_merge($map, [
        'member' => $member,
        'logoPath' => $base64,
        'text'=>$text,
        'SKP_NAME' => $map['SKP_NAME'] ?? 'Spolek',
        'SKP_EMAIL' => $map['SKP_EMAIL'] ?? '',
        'SKP_ICO' => $map['SKP_ICO'] ?? '',
        'SKP_ADDRESS' => $map['SKP_ADDRESS'] ?? '',
        'SKP_REGISTRATION_AMOUNT' => $map['SKP_REGISTRATION_AMOUNT'] ?? $map['SKP_MEMBERSHIP_FEE'] ?? 0,
        'qrCodePath' => $qrCodePath // V náhledu zatím bez QR
    ]);

    $html = $latte->renderToString(APP_DIR . DS . 'SystemTemplates' . DS . 'emails' . DS . $template . '.latte', $params);

    $outputPath = TEMP_DIR . DS . 'mail_preview_' . $template . '.html';
    file_put_contents($outputPath, $html);

    echo "Náhled e-mailu '$template' byl vygenerován do: $outputPath\n";

} catch (\Exception $e) {
    echo "Chyba: " . $e->getMessage() . "\n";
}
