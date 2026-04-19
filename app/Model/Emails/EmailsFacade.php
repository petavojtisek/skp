<?php

namespace App\Model\Emails;

use App\Modules\SystemConstants\Model\SystemConstantsFacade;
use Latte\Engine;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

class EmailsFacade
{
    private SystemConstantsFacade $constantsFacade;
    private Mailer $mailer;
    private bool $debugMode;
    private array $config = [];

    public function __construct(SystemConstantsFacade $constantsFacade, Mailer $mailer, bool $debugMode = false)
    {
        $this->constantsFacade = $constantsFacade;
        $this->mailer = $mailer;
        $this->debugMode = $debugMode;

        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        $constants = $this->constantsFacade->getAllSystemConstants();
        $map = [];
        foreach ($constants as $c) {
            $map[$c->getCode()] = $c->getValue();
        }
        $this->config = $map;

    }

    public function createMessage(string $templateName, array $params = [], ?string $to = null, array $attachments = [], ?string $qrPath = null): Message
    {
        $latte = new Engine();
        $logoPath = ASSETS_DIR . DS . 'images' . DS . 'logo-v1-spolek-mail.jpeg';

        $message = new Message();
        $message->setFrom($this->config['SKP_EMAIL'], $this->config['SKP_NAME']);

        if ($to) {
            $message->addTo($to);
        }

        // Logo
        $logoCid = null;
        if (file_exists($logoPath)) {
            $part = $message->addEmbeddedFile($logoPath);
            $logoCid = $part->getHeader('Content-ID');
            $logoCid = trim($logoCid, '<>');
        }

        // QR Kód
        $qrCid = null;
        if ($qrPath && file_exists($qrPath)) {
            $part = $message->addEmbeddedFile($qrPath);
            $qrCid = $part->getHeader('Content-ID');
            $qrCid = trim($qrCid, '<>');
        }

        foreach ($attachments as $filePath) {
            if (file_exists($filePath)) {
                $message->addAttachment($filePath);
            }
        }


        // Parametry pro šablonu
        $tplParams = array_merge($this->config, $params, [
            'logoPath' => $logoCid ? 'cid:' . $logoCid : null,
            'qrCodePath' => $qrCid ? 'cid:' . $qrCid : null
        ]);

        $html = $latte->renderToString(APP_DIR . DS . 'SystemTemplates' . DS . 'emails' . DS . $templateName . '.latte', $tplParams);
        $message->setHtmlBody($html);

        return $message;
    }

    public function send(Message $message): void
    {
        if ($this->debugMode) {
            $logPath = TEMP_DIR . DS . 'sent_emails';
            if (!is_dir($logPath)) {
                FileSystem::createDir($logPath);
            }

            $filename = date('Y-m-d_H-i-s') . '_' . uniqid() . '.eml';
            file_put_contents($logPath . DS . $filename, $message->generateMessage());

            \Tracy\Debugger::log("E-mail uložen do $filename (Debug Mode)", 'info');
            return;
        }

        $this->mailer->send($message);
    }

    public function sendGenericEmail(string $to, string $subject, string $text): void
    {
        $message = $this->createMessage('generic', ['text' => $text]);
        $message->setSubject($subject);
        $message->addTo($to);
        $this->send($message);
    }
}
