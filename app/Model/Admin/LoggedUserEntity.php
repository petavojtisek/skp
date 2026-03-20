<?php

namespace App\Model\Admin;

use App\Model\Base\IEntity;

class LoggedUserEntity extends AdministratorEntity
{
    public ?IEntity $group = null;

    /** @var array [presentation_id => 1/0] */
    public array $presentations = [];

    public array $rights = [];

    public ?int $active_presentation_id = null;

    /**
     * Initializes the entity with data from identity (session)
     */
    public function initWithData(array $data): void
    {
        // Use fillEntity to ensure properties are tracked in valuesSet
        // and can be exported back via getEntityData()
        $this->fillEntity($data, false);

        // Fill non-DB extended properties manually (they aren't tracked by valuesSet)
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

    public function getGroup(): ?IEntity
    {
        return $this->group;
    }

    public function setGroup(?IEntity $group): void
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

    /**
     * Checks if the user has a specific group right or the master 'ALL' right.
     */
    public function hasGroupRight(string $code): bool
    {
        $groupRights = $this->rights['groups_right'] ?? [];

        return array_key_exists('ALL', $groupRights) or  array_key_exists($code, $groupRights);
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
