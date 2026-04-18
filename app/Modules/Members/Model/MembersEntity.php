<?php

namespace App\Modules\Members\Model;

use App\Model\Base\BaseEntity;

class MembersEntity extends BaseEntity
{

    const string  SOURCE_IMPORT = 'import',
                  SOURCE_WEB = 'web';

    const array SOURCES = [
        self::SOURCE_IMPORT=> self::SOURCE_IMPORT,
        self::SOURCE_WEB=> self::SOURCE_WEB,
    ];

    public ?int $member_id = null;
    public ?string $member_number = null;
    public ?string $name = null;
    public ?string $surname = null;
    public ?string $degree = null;
    public mixed $birth_date = null;
    public ?string $street = null;
    public ?string $zip = null;
    public ?string $city = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $note = null;
    public mixed $last_member_payment = null;
    public ?int $active = 1;
    public ?string $source = null;
    public mixed $registration_email_dt = null;
    public mixed $registration_confirm_email_dt = null;
    public mixed $payment_confirm_email_dt = null;
    public mixed $payment_reminder_email_dt = null;
    public mixed $payment_renew_email_dt = null;
    public mixed $created_dt = null;

    public function getId(): ?int
    {
        return $this->member_id;
    }

    public function setId(mixed $id): void
    {
        $this->setVariable('member_id', $id, self::VALUE_TYPE_INTEGER);
    }

    public function getMemberNumber(): ?string
    {
        return $this->member_number;
    }

    public function setMemberNumber(?string $memberNumber): void
    {
        $this->setVariable('member_number', $memberNumber, self::VALUE_TYPE_STRING);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->setVariable('name', $name, self::VALUE_TYPE_STRING);
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): void
    {
        $this->setVariable('surname', $surname, self::VALUE_TYPE_STRING);
    }

    public function getDegree(): ?string
    {
        return $this->degree;
    }

    public function setDegree(?string $degree): void
    {
        $this->setVariable('degree', $degree, self::VALUE_TYPE_STRING);
    }

    public function getBirthDate($format = null)
    {
        return $this->getDateTime($this->birth_date, $format);
    }

    public function setBirthDate(mixed $birthDate): void
    {
        $this->setVariable('birth_date', $birthDate, self::VALUE_TYPE_DATE);
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->setVariable('street', $street, self::VALUE_TYPE_STRING);
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): void
    {
        $this->setVariable('zip', $zip, self::VALUE_TYPE_STRING);
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->setVariable('city', $city, self::VALUE_TYPE_STRING);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->setVariable('email', $email, self::VALUE_TYPE_STRING);
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->setVariable('phone', $phone, self::VALUE_TYPE_STRING);
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->setVariable('note', $note, self::VALUE_TYPE_STRING);
    }

    public function getLastMemberPayment($format = null)
    {
        return $this->getDateTime($this->last_member_payment, $format);
    }

    public function setLastMemberPayment(mixed $lastMemberPayment): void
    {
        $this->setVariable('last_member_payment', $lastMemberPayment, self::VALUE_TYPE_DATE);
    }

    public function isActive(): bool
    {
        return (bool)$this->active;
    }

    public function setActive(mixed $active): void
    {
        $this->setVariable('active', $active, self::VALUE_TYPE_INTEGER);
    }

    public function getFullName(): string
    {
        return trim(($this->degree ? $this->degree . ' ' : '') . $this->name . ' ' . $this->surname);
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->setVariable('source', $source, self::VALUE_TYPE_STRING);
    }

    public function getRegistrationEmailDt($format = 'd.m.Y H:i:s')
    {
        return $this->getDateTime($this->registration_email_dt, $format);
    }

    public function setRegistrationEmailDt(mixed $dt): void
    {
        $this->setVariable('registration_email_dt', $dt, self::VALUE_TYPE_DATE);
    }

    public function getRegistrationConfirmEmailDt($format = 'd.m.Y H:i:s')
    {
        return $this->getDateTime($this->registration_confirm_email_dt, $format);
    }

    public function setRegistrationConfirmEmailDt(mixed $dt): void
    {
        $this->setVariable('registration_confirm_email_dt', $dt, self::VALUE_TYPE_DATE);
    }

    public function getPaymentConfirmEmailDt($format = 'd.m.Y H:i:s')
    {
        return $this->getDateTime($this->payment_confirm_email_dt, $format);
    }

    public function setPaymentConfirmEmailDt(mixed $dt): void
    {
        $this->setVariable('payment_confirm_email_dt', $dt, self::VALUE_TYPE_DATE);
    }

    public function getPaymentReminderEmailDt($format = 'd.m.Y H:i:s')
    {
        return $this->getDateTime($this->payment_reminder_email_dt, $format);
    }

    public function setPaymentReminderEmailDt(mixed $dt): void
    {
        $this->setVariable('payment_reminder_email_dt', $dt, self::VALUE_TYPE_DATE);
    }

    public function getPaymentRenewEmailDt($format = 'd.m.Y H:i:s')
    {
        return $this->getDateTime($this->payment_renew_email_dt, $format);
    }

    public function setPaymentRenewEmailDt(mixed $dt): void
    {
        $this->setVariable('payment_renew_email_dt', $dt, self::VALUE_TYPE_DATE);
    }
}
