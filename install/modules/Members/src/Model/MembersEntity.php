<?php

namespace App\Modules\Members\Model;

use App\Model\Base\BaseEntity;

class MembersEntity extends BaseEntity
{
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
}
