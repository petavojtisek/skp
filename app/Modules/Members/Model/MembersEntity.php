<?php

namespace App\Modules\Members\Model;

use App\Model\Base\BaseEntity;

class MembersEntity extends BaseEntity
{
    public ?int $member_id = null;
    public ?int $member_number = null;
    public ?string $name = null;
    public ?string $surname = null;
    public ?string $degree = null;
    public mixed $birth_date = null;
    public ?string $address = null;
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

    public function getMemberNumber(): ?int
    {
        return $this->member_number;
    }

    public function setMemberNumber(?int $memberNumber): void
    {
        $this->setVariable('member_number', $memberNumber, self::VALUE_TYPE_INTEGER);
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

    public function getFullName(): string
    {
        return trim(($this->degree ? $this->degree . ' ' : '') . $this->name . ' ' . $this->surname);
    }

    public function getBirthDate($format = null)
    {
        return $this->getDateTime($this->birth_date, $format);
    }

    public function getLastMemberPayment($format = null)
    {
        return $this->getDateTime($this->last_member_payment, $format);
    }

    public function isActive(): bool
    {
        return (bool)$this->active;
    }
}
