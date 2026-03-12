<?php

namespace App\Model\Admin;

class LoggedUserEntity extends AdministratorEntity
{
    public ?array $group = null;

    /** @var array [presentation_id => 1/0] */
    public array $presentations = [];

    public array $rights = [];

    public ?int $active_presentation_id = null;

    public function setGroup(?array $group): void
    {
        $this->group = $group;
    }

    public function setPresentations(array $presentations): void
    {
        $this->presentations = $presentations;
    }

    public function setRights(array $rights): void
    {
        $this->rights = $rights;
    }
}
