<?php

use App\Model\System\PdfService;
use App\Model\System\PaymentQrService;
use App\Modules\Members\Model\MembersFacade;
use App\Modules\SystemConstants\Model\SystemConstantsFacade;

require __DIR__ . '/../test_scripts/bootstrap.php';

/** @var \Nette\DI\Container $container */

// 1. Get Services
/** @var SystemConstantsFacade $constantsFacade */
$constantsFacade = $container->getByType(SystemConstantsFacade::class);
/** @var MembersFacade $membersFacade */
$membersFacade = $container->getByType(MembersFacade::class);
/** @var PaymentQrService $qrService */
$qrService = $container->getByType(PaymentQrService::class);
/** @var PdfService $pdfService */
$pdfService = $container->getByType(PdfService::class);

// 2. Load Constants
$allConstants = [];
foreach ($constantsFacade->getAllSystemConstants() as $c) {
    $allConstants[$c->getCode()] = $c->getValue();
}

$account = $allConstants['SKP_ACCOUNT'] ?? '2000000000/2010';
$amount = (float)($allConstants['REGISTRATION_AMOUNT'] ?? 300);
$contactEmail = $allConstants['SKP_EMAIL'] ?? 'info@krajinapolabi.cz';
$spolekName = $allConstants['SKP_NAME'] ?? 'Spolek krajina polabí';
$spolekIco = $allConstants['SKP_ICO'] ?? '24576972';
$spolekAddress = $allConstants['SKP_ADDRESS'] ?? 'Praha';

// 3. Load one member
$members = $membersFacade->findMembers(1, 0);
if (empty($members)) {
    die("Error: No members found in database.\n");
}
/** @var \App\Modules\Members\Model\MembersEntity $member */
$member = reset($members);

echo "Generating registration PDF for member: " . $member->getFullName() . "\n";

// 4. Prepare QR Code
$vs = $member->getMemberNumber();
if (!$vs) {
    $vs = '00000000';
}

// 5. Logo path
$logoPath = __DIR__ . '/../www/assets/images/logo-v1-spolek.jpeg';

try {
    // Generate QR Data URI
    $qrDataUri = $qrService->generateQr($account, $amount, $vs, 'CZK', $spolekName, "Členský příspěvek - " . $member->getSurname());
    
    // mPDF sometimes struggles with Data URIs in some environments, let's save it to a temp file for maximum reliability
    $qrFilePath = __DIR__ . '/../temp/qr_' . $vs . '.png';
    $qrBase64 = str_replace('data:image/png;base64,', '', $qrDataUri);
    file_put_contents($qrFilePath, base64_decode($qrBase64));

    // 6. Generate PDF
    $templatePath = __DIR__ . '/../app/SystemTemplates/pdf/registration.latte';
    
    $params = [
        'logo' => file_exists($logoPath) ? $logoPath : null,
        'spolek_nazev' => $spolekName,
        'spolekIco' => $spolekIco,
        'spolekAddress' => $spolekAddress,
        'member' => $member,
        'account' => $account,
        'amount' => $amount,
        'vs' => $vs,
        'qr_code_file' => $qrFilePath,
        'today' => new \DateTime(),
        'kontaktni_email' => $contactEmail
    ];
    
    $pdfBinary = $pdfService->generate($templatePath, $params);
    
    $outputPath = __DIR__ . '/../temp/registration_' . $vs . '.pdf';
    file_put_contents($outputPath, $pdfBinary);
    
    echo "Success! PDF generated to: " . realpath($outputPath) . "\n";
    
} catch (\Throwable $e) {
    echo "Error generating registration: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
