<?php

namespace App\Model\Admin;

class LoggedUserEntity extends AdministratorEntity
{
    public ?array $group = null;

    /** @var array [presentation_id => 1/0] */
    public array $presentations = [];

    public array $rights = [];

    public ?int $active_presentation_id = null;

    /**
     * Initializes the entity with data from identity (session)
     */
    public function initWithData(array $data): void
    {
        // Fill base administrator properties
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        // Fill extended properties
        if (isset($data['group'])) $this->group = $data['group'];
        if (isset($data['presentations'])) $this->presentations = $data['presentations'];
        if (isset($data['rights'])) $this->rights = $data['rights'];
        if (isset($data['active_presentation_id'])) $this->active_presentation_id = $data['active_presentation_id'];
    }

    /**
     * Exports all entity data for SimpleIdentity storage
     */
    public function exportData(): array
    {
        $data = $this->getEntityData();
        $data['group'] = $this->group;
        $data['presentations'] = $this->presentations;
        $data['rights'] = $this->rights;
        $data['active_presentation_id'] = $this->active_presentation_id;
        
        return $data;
    }

    public function getGroup(): ?array
    {
        return $this->group;
    }

    public function setGroup(?array $group): void
    {
        $this->group = $group;
    }


    public function getPresentations(): array
    {
        return $this->presentations;
    }

    public function setPresentations(array $presentations): void
    {
        $this->presentations = $presentations;
    }

    public function getRights(): array
    {
        return $this->rights;
    }

    public function setRights(array $rights): void
    {
        $this->rights = $rights;
    }

    public function getActivePresentationId(): ?int
    {
        return $this->active_presentation_id;
    }

    public function setActivePresentationId(?int $id): void
    {
        $this->setVariable('active_presentation_id', $id, self::VALUE_TYPE_INTEGER);
    }
}
