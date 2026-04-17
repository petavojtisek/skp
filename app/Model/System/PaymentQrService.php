<?php

namespace App\Model\System;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Service for generating QR Payment codes (SPD for CZ/SK and EPC for SEPA).
 */
class PaymentQrService
{
    /**
     * Generates a QR Payment code image data URI.
     * Automatically chooses between SPD (CZ/SK) and EPC (SEPA) based on currency.
     */
    public function generateQr(
        string $account,
        float $amount,
        string $variableSymbol,
        string $currency = 'CZK',
        ?string $recipientName = null,
        ?string $message = null,
        ?string $bic = null
    ): string {
        if (strtoupper($currency) === 'EUR') {
            return $this->generateSepaQr($account, $amount, $recipientName ?? 'Recipient', $variableSymbol, $message, $bic);
        }

        return $this->generateSpdQr($account, $amount, $variableSymbol, $currency, $message);
    }

    /**
     * Generates SPD (Short Payment Descriptor) - Standard for CZ/SK.
     */
    public function generateSpdQr(string $account, float $amount, string $vs, string $currency = 'CZK', ?string $message = null): string
    {
        $iban = $this->ensureIban($account);
        $spd = "SPD*1.0*ACC:{$iban}*AM:" . number_format($amount, 2, '.', '') . "*CC:{$currency}*VS:{$vs}";
        
        if ($message) {
            $spd .= "*MSG:" . mb_substr($this->sanitize($message), 0, 60);
        }

        return $this->createQrImage($spd);
    }

    /**
     * Generates EPC-QR (European Payments Council) - Standard for SEPA (EUR).
     */
    public function generateSepaQr(string $iban, float $amount, string $recipientName, ?string $reference = null, ?string $message = null, ?string $bic = null): string
    {
        $iban = str_replace(' ', '', strtoupper($iban));
        
        // EPC-QR Format (Line by line)
        $lines = [
            'BCD',              // Service Tag
            '002',              // Version
            '1',                // Character Set (1 = UTF-8)
            'SCT',              // Identification (SCT = SEPA Credit Transfer)
            $bic ?? '',         // BIC (Optional for some banks, but recommended)
            $recipientName,     // Recipient Name
            $iban,              // IBAN
            'EUR' . number_format($amount, 2, '.', ''), // Amount (Preceded by EUR)
            '',                 // Purpose Code (Optional)
            $reference ?? '',   // Structured Reference (Optional)
            $message ?? '',     // Unstructured Message (Optional)
            ''                  // Footer
        ];

        $data = implode("\n", $lines);
        return $this->createQrImage($data);
    }

    private function createQrImage(string $data): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            data: $data,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $result = $builder->build();

        return $result->getDataUri();
    }

    /**
     * Converts CZ account number to IBAN if needed.
     */
    private function ensureIban(string $account): string
    {
        $account = strtoupper(str_replace(' ', '', $account));
        if (str_starts_with($account, 'CZ')) {
            return $account;
        }

        if (preg_match('/^(?:(\d{1,6})-)?(\d{1,10})\/(\d{4})$/', $account, $matches)) {
            $prefix = $matches[1] ? str_pad($matches[1], 6, '0', STR_PAD_LEFT) : '000000';
            $number = str_pad($matches[2], 10, '0', STR_PAD_LEFT);
            $bank = $matches[3];
            
            $bban = $bank . $prefix . $number;
            
            // Calculate check digits
            $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $numericCountry = "123500"; // CZ is 12 and 35, + 00
            $checksumStr = $bban . $numericCountry;
            
            $remainder = 0;
            foreach (str_split($checksumStr, 7) as $chunk) {
                $remainder = ($remainder . $chunk) % 97;
            }
            
            $checkDigits = str_pad(98 - $remainder, 2, '0', STR_PAD_LEFT);
            return 'CZ' . $checkDigits . $bban;
        }

        return $account;
    }

    private function sanitize(string $text): string
    {
        return str_replace(['*', ':'], [' ', ' '], $text);
    }
}
