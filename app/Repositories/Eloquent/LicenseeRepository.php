<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\LicenseeRepositoryInterface;

class LicenseeRepository extends Repository implements LicenseeRepositoryInterface
{
    /**
     * Attach user to licensee as brand contact
     *
     * @param int $licenseeId
     * @param int $userId
     *
     * @return mixed
     */
    public function attachBrandContact($licenseeId, $userId)
    {
        $licensee = $this->find($licenseeId);

        if ($licensee->brandContacts->contains($userId)) {
            return false;
        }

        $licensee->brandContacts()
            ->attach($userId);
    }

    /**
     * Detach user from licensee as brand contact
     *
     * @param int $licenseeId
     * @param int $userId
     *
     * @return mixed
     */
    public function detachBrandContact($licenseeId, $userId)
    {
        return $this->find($licenseeId)
            ->brandContacts()
            ->detach($userId);
    }
}
