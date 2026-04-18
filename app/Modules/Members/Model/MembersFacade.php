<?php

namespace App\Modules\Members\Model;

class MembersFacade
{
    private MembersService $service;

    public function __construct(MembersService $service)
    {
        $this->service = $service;
    }

    public function findMembers( int $limit , int $offset, ?string $search = null, ?string $source = null): array
    {
        return $this->service->findMembers($limit, $offset, $search, $source);
    }

    public function countMembers(?string $search = null, ?string $source = null): int
    {
        return $this->service->countMembers($search, $source);
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

    public function generateQr(MembersEntity $membersEntity): void
    {

    }

    public function generateRegistrationConfirmation(MembersEntity $membersEntity): void
    {

    }

    public function sendRegistrationEmail(MembersEntity $membersEntity): void
    {
        //todo
    }
    public function sendPaymentConfirmationEmail(int $memberId): void
    {
        //todo
    }

    public function sendPaymentReminderEmail(int $memberId): void
    {
            //todo
    }


}
