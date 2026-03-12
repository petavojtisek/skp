<?php

namespace App\Model\Admin;

class LoggedUserEntity extends AdministratorEntity
{
    /** @var array|null */
    public $group;

    /** @var array [presentation_id => 1/0] */
    public $presentations = [];

    /** @var array */
    public $rights = [];

    /** @var int|null */
    public $active_presentation_id;

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
