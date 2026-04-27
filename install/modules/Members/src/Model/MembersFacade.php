<?php

namespace App\Modules\Members\Model;

use App\Model\Emails\EmailsFacade;
use App\Model\System\PaymentQrService;
use App\Model\System\PdfService;
use App\Modules\SystemConstants\Model\SystemConstantsFacade;
use Nette\Utils\FileSystem;
use League\Csv;

class MembersFacade
{
    private MembersService $service;
    private EmailsFacade $emailsFacade;
    private SystemConstantsFacade $constantsFacade;
    private PaymentQrService $qrService;
    private PdfService $pdfService;

    public function __construct(
        MembersService $service,
        EmailsFacade $emailsFacade,
        SystemConstantsFacade $constantsFacade,
        PaymentQrService $qrService,
        PdfService $pdfService
    ) {
        $this->service = $service;
        $this->emailsFacade = $emailsFacade;
        $this->constantsFacade = $constantsFacade;
        $this->qrService = $qrService;
        $this->pdfService = $pdfService;
    }

    public function findMembers( int $limit , int $offset, ?string $search = null, ?string $source = null, ?bool $registrationEmail = null, ?bool $registrationConfirm = null, ?bool $paymentConfirm = null, ?bool $isPaid = null, ?bool $active = null): array
    {
        return $this->service->findMembers($limit, $offset, $search, $source, $registrationEmail, $registrationConfirm, $paymentConfirm, $isPaid, $active);
    }

    public function export(?array $ids) : string
    {

        if(is_array($ids)) {
            $members = $this->findByIds($ids);
        }else{
            $members = $this->service->findMembers(100000,0);
        }
        $csv = Csv\Writer::from(new \SplTempFileObject());
        $csv->setDelimiter(';'); // Excel má v CZ raději středník

        $csv->setOutputBOM( Csv\Bom::Utf8);
        $csv->insertOne(['Číslo člena', 'Jméno','Přijmeni', 'E-mail', 'Telefon', 'Adresa', 'Datum narození']);


        foreach ($members as $member) {
            $csv->insertOne([
                $member->getMemberNmber(),
                $member->getName(),
                $member->getSurname(),
                $member->getEmail(),
                $member->getPhone(),
                $member->getStreet()." ".$member->getCity().' '.$member->getZip(),
                $member->getBirthDate('d.m.Y'),
            ]);
        }

        return $csv->toString();

    }


    public function findByIds(array $ids) : array
    {
        return $this->service->findByIds($ids);
    }

    public function countMembers(?string $search = null, ?string $source = null, ?bool $registrationEmail = null, ?bool $registrationConfirm = null, ?bool $paymentConfirm = null, ?bool $isPaid = null, ?bool $active = null): int
    {
        return $this->service->countMembers($search, $source, $registrationEmail, $registrationConfirm, $paymentConfirm, $isPaid, $active);
    }

    public function findLatestRegistrations(int $limit = 5): array
    {
        return $this->service->findLatestRegistrations($limit);
    }

    public function getMember(int $id): ?MembersEntity
    {
        return $this->service->find($id);
    }

    public function saveMember(MembersEntity $entity): int
    {
        return $this->service->save($entity);
    }

    public function deleteMember(int $id): void
    {
        $this->service->delete($id);
    }

    private function getMemberStoragePath(MembersEntity $member): string
    {
        $path = STORAGE_DIR . DS . 'data' . DS . 'members' . DS . $member->getMemberNumber();
        if (!is_dir($path)) {
            FileSystem::createDir($path);
        }
        return $path;
    }

    private function getSystemConfig(): array
    {
        $constants = $this->constantsFacade->getAllSystemConstants();
        $map = [];
        foreach ($constants as $c) {
            $map[$c->getCode()] = $c->getValue();
        }
        return $map;
    }

    public function generateQr(int $memberId, bool $force = false): string
    {
        $member = $this->getMember($memberId);
        if (!$member) throw new \Exception("Člen s ID $memberId nenalezen.");

        $storagePath = $this->getMemberStoragePath($member);
        $qrFile = $storagePath . DS . 'qr.png';

        if (!file_exists($qrFile) || $force) {
            $config = $this->getSystemConfig();
            $qrDataUri = $this->qrService->generateQr(
                $config['SKP_ACCOUNT'] ?? '',
                (float)($config['SKP_REGISTRATION_AMOUNT'] ?? 0),
                $member->getMemberNumber(),
                'CZK',
                $config['SKP_NAME'] ?? ''
            );
            $base64 = substr($qrDataUri, strpos($qrDataUri, ',') + 1);
            file_put_contents($qrFile, base64_decode($base64));
        }

        return $qrFile;
    }




    public function generateRegistrationConfirmation(int $memberId, bool $force = false): string
    {
        $member = $this->getMember($memberId);
        if (!$member) throw new \Exception("Člen s ID $memberId nenalezen.");

        $storagePath = $this->getMemberStoragePath($member);
        $pdfFile = $storagePath . DS . 'registration.pdf';

        if (!file_exists($pdfFile) || $force) {
            $config = $this->getSystemConfig();

            // Logo pro PDF jako Base64
            $logoPath = ASSETS_DIR . DS . 'images' . DS . 'logo-v1-spolek-mail.jpeg';
            $logoBase64 = file_exists($logoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath)) : null;

            // QR kód pro PDF jako Base64
            $qrPath = $this->generateQr($memberId, $force);
            $qrBase64 = file_exists($qrPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($qrPath)) : null;

            $pdfContent = $this->pdfService->generate(
                APP_DIR . DS . 'SystemTemplates' . DS . 'pdf' . DS . 'registration_pdf.latte',
                array_merge($config, [
                    'member' => $member,
                    'logoBase64' => $logoBase64,
                    'qrBase64' => $qrBase64
                ])
            );
            file_put_contents($pdfFile, $pdfContent);
        }

        return $pdfFile;
    }

    public function sendRegistrationEmail(int $memberId): void
    {
        $member = $this->getMember($memberId);
        if (!$member || !$member->email) return;

        $config = $this->getSystemConfig();
        $qrFile = $this->generateQr($memberId);

        $pdfFile = $this->generateRegistrationConfirmation($memberId);

        $message = $this->emailsFacade->createMessage(
            'registration',
            array_merge($config, [
                'member' => $member,
                'SKP_ACCOUNT' => $config['SKP_ACCOUNT'] ?? '',
                'SKP_REGISTRATION_AMOUNT' => $config['SKP_REGISTRATION_AMOUNT'] ?? $config['SKP_MEMBERSHIP_FEE'] ?? 0,
            ]),
            $member->email,
            [$pdfFile],
            $qrFile
        );
        $message->setSubject('Registrace do spolku ' . ($config['SKP_NAME'] ?? ''));

        $this->emailsFacade->send($message);

        $member->setRegistrationEmailDt(new \DateTime());
        $this->saveMember($member);
    }

    public function sendAcceptanceEmail(int $memberId): void
    {
        $member = $this->getMember($memberId);
        if (!$member || !$member->email) return;

        $config = $this->getSystemConfig();
        $message = $this->emailsFacade->createMessage(
            'acceptance',
            array_merge($config, ['member' => $member]),
            $member->email
        );
        $message->setSubject('Potvrzení o přijetí do spolku ' . ($config['SKP_NAME'] ?? ''));

        $this->emailsFacade->send($message);

        $member->setRegistrationConfirmEmailDt(new \DateTime());
        $this->saveMember($member);
    }

    public function sendPaymentConfirmationEmail(int $memberId): void
    {
        $member = $this->getMember($memberId);
        if (!$member || !$member->email) return;

        $config = $this->getSystemConfig();
        $message = $this->emailsFacade->createMessage(
            'payment_confirmation',
            array_merge($config, ['member' => $member]),
            $member->email
        );
        $message->setSubject('Potvrzení o zaplacení příspěvků - ' . ($config['SKP_NAME'] ?? ''));

        $this->emailsFacade->send($message);

        $member->setPaymentConfirmEmailDt(new \DateTime());
        $this->saveMember($member);
    }

    public function sendPaymentReminderEmail(int $memberId): void
    {
        $member = $this->getMember($memberId);
        if (!$member || !$member->email) return;

        $config = $this->getSystemConfig();
        $qrFile = $this->generateQr($memberId);

        $message = $this->emailsFacade->createMessage(
            'payment_reminder',
            array_merge($config, [
                'member' => $member,
                'SKP_ACCOUNT' => $config['SKP_ACCOUNT'] ?? '',
                'SKP_REGISTRATION_AMOUNT' => $config['SKP_REGISTRATION_AMOUNT'] ?? $config['SKP_MEMBERSHIP_FEE'] ?? 0,
            ]),
            $member->email,
            [],
            $qrFile
        );
        $message->setSubject('Upomínka platby členských příspěvků - ' . ($config['SKP_NAME'] ?? ''));

        $this->emailsFacade->send($message);

        $member->setPaymentReminderEmailDt(new \DateTime());
        $this->saveMember($member);
    }

    public function sendEmail(int $memberId, string $subject = 'Zpráva ze spolku', ?string $text = null): void
    {
        $member = $this->getMember($memberId);
        if (!$member || !$member->email || !$text) return;

        $this->emailsFacade->sendGenericEmail($member->email, $subject, $text);
    }

    public function setMemberLastPaymentData(int $memberId, $date):?int
    {
        return $this->service->setMemberLastPaymentData($memberId,$date);
    }
}
