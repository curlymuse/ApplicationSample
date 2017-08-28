<?php

namespace App\Support;

use App\Jobs\Attachment\DeleteAttachment;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\ContractTermGroupRepositoryInterface;
use App\Repositories\Contracts\ContractTermRepositoryInterface;
use App\Repositories\Contracts\RoomSetRepositoryInterface;

class ChangeOrderProcessor
{
    /**
     * @const string
     */
    const REGX_ATTACHMENT = '/^attachments\.id\:([0-9]+)$/';

    /**
     * @const string
     */
    const REGX_ROOM_SET = '/^rooms\.id\:([0-9]+)$/';

    /**
     * @const string
     */
    const REGX_RESERVATION_METHOD = '/^reservation_methods\.id\:([0-9]+)$/';

    /**
     * @const string
     */
    const REGX_PAYMENT_METHOD = '/^payment_methods\.id\:([0-9]+)$/';

    /**
     * @const string
     */
    const REGX_ROOM_SET_KEY = '/^rooms\.id\:([0-9]+)\.([a-z_]+)$/';

    /**
     * @const string
     */
    const REGX_TERM_GROUP = '/^term_groups\.id\:([0-9]+)$/';

    /**
     * @const string
     */
    const REGX_TERM_GROUP_NAME = '/^term_groups\.id\:([0-9]+)\.name$/';

    /**
     * @const string
     */
    const REGX_TERM = '/^term_groups\.id\:([0-9]+)\.terms\.id\:([0-9]+)$/';

    /**
     * @const string
     */
    const REGX_TERM_DESCRIPTION = '/^term_groups\.id\:([0-9]+)\.terms\.id\:([0-9]+)\.description$/';

    /**
     * @const string
     */
    const REGX_TERM_TITLE = '/^term_groups\.id\:([0-9]+)\.terms\.id\:([0-9]+)\.title$/';

    /**
     * @const string
     */
    const REGX_TERM_LIST = '/^term_groups\.id\:([0-9]+)\.terms$/';

    /**
     * @const string
     */
    const REGX_JSON_ITEM = '/^(%s)\.index\:([0-9]+)$/';

    /**
     * @const string
     */
    const REGX_JSON_ITEM_KEY = '/^(%s)\.index\:([0-9]+)\.([a-z_]+)$/';

    /**
     * @var array
     */
    protected static $jsonColumns = [
        'questions',
        'meeting_spaces',
        'food_and_beverage',
    ];

    /**
     * @var ChangeOrderRepositoryInterface
     */
    private $changeOrderRepo;

    /**
     * @var ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * @var RoomSetRepositoryInterface
     */
    private $roomSetRepo;

    /**
     * @var ContractTermGroupRepositoryInterface
     */
    private $termGroupRepo;

    /**
     * @var ContractTermRepositoryInterface
     */
    private $termRepo;

    /**
     * @var AttachmentRepositoryInterface
     */
    private $attachmentRepo;

    /**
     * ChangeOrderProcessor constructor.
     * @param ChangeOrderRepositoryInterface $changeOrderRepo
     * @param ContractRepositoryInterface $contractRepo
     * @param RoomSetRepositoryInterface $roomSetRepo
     * @param ContractTermGroupRepositoryInterface $termGroupRepo
     * @param ContractTermRepositoryInterface $termRepo
     * @param AttachmentRepositoryInterface $attachmentRepo
     */
    public function __construct(
        ChangeOrderRepositoryInterface $changeOrderRepo,
        ContractRepositoryInterface $contractRepo,
        RoomSetRepositoryInterface $roomSetRepo,
        ContractTermGroupRepositoryInterface $termGroupRepo,
        ContractTermRepositoryInterface $termRepo,
        AttachmentRepositoryInterface $attachmentRepo
    )
    {
        $this->changeOrderRepo = $changeOrderRepo;
        $this->contractRepo = $contractRepo;
        $this->roomSetRepo = $roomSetRepo;
        $this->termGroupRepo = $termGroupRepo;
        $this->termRepo = $termRepo;
        $this->attachmentRepo = $attachmentRepo;
    }

    /**
     * Process the change order
     *
     * @param int $changeOrderId
     *
     * @return bool
     */
    public function process($changeOrderId)
    {
        $changeOrder = $this->changeOrderRepo->find($changeOrderId);

        if (! $changeOrder) {
            return false;
        }

        switch ($this->parseType($changeOrder)) {
            case 'modify.meta':
                $this->modifyMeta(
                    $changeOrder->contract_id,
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'modify.json':
                $this->modifyJson(
                    $changeOrder->contract,
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'modify.room_set':
                $this->modifyRoomSet(
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'modify.term_group_name':
                $this->modifyTermGroupName(
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'modify.term_description':
                $this->modifyTermDescription(
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'modify.term_title':
                $this->modifyTermTitle(
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'add.json':
                $this->addJson(
                    $changeOrder->contract,
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'add.room_set':
                $this->addRoomSet(
                    $changeOrder->contract,
                    $changeOrder->proposed_value
                );
                break;
            case 'add.term_group':
                $this->addTermGroup(
                    $changeOrder->contract_id,
                    $changeOrder->proposed_value
                );
                break;
            case 'add.term':
                $this->addTerm(
                    $changeOrder->change_key,
                    $changeOrder->proposed_value
                );
                break;
            case 'add.reservation_method':
                $this->addReservationMethod(
                    $changeOrder->contract_id,
                    $changeOrder->proposed_value
                );
                break;
            case 'add.payment_method':
                $this->addPaymentMethod(
                    $changeOrder->contract_id,
                    $changeOrder->proposed_value
                );
                break;
            case 'add.attachment':
                $this->addAttachment(
                    $changeOrder->contract_id,
                    $changeOrder->proposed_value
                );
                break;
            case 'remove.json':
                $this->removeJsonItem(
                    $changeOrder->contract,
                    $changeOrder->change_key
                );
                break;
            case 'remove.attachment':
                $this->removeAttachment($changeOrder->change_key);
                break;
            case 'remove.room_set':
                $this->removeRoomSet($changeOrder->change_key);
                break;
            case 'remove.reservation_method':
                $this->removeReservationMethod(
                    $changeOrder->contract_id,
                    $changeOrder->change_key
                );
                break;
            case 'remove.payment_method':
                $this->removePaymentMethod(
                    $changeOrder->contract_id,
                    $changeOrder->change_key
                );
                break;
            case 'remove.term_group':
                $this->removeTermGroup($changeOrder->change_key);
                break;
            case 'remove.term':
                $this->removeTerm($changeOrder->change_key);
                break;
        }

        return true;
    }

    /**
     * Use the type and key params to determine change order type
     *
     * @param ChangeOrder $changeOrder
     *
     * @return string
     */
    private function parseType(ChangeOrder $changeOrder)
    {
        if ($changeOrder->change_type == 'remove') {

            if (preg_match(self::REGX_ROOM_SET, $changeOrder->change_key)) {
                return 'remove.room_set';
            }

            if (preg_match(self::REGX_RESERVATION_METHOD, $changeOrder->change_key)) {
                return 'remove.reservation_method';
            }

            if (preg_match(self::REGX_PAYMENT_METHOD, $changeOrder->change_key)) {
                return 'remove.payment_method';
            }

            if (preg_match(self::REGX_ATTACHMENT, $changeOrder->change_key)) {
                return 'remove.attachment';
            }

            if (preg_match(self::REGX_TERM_GROUP, $changeOrder->change_key)) {
                return 'remove.term_group';
            }

            if (preg_match(self::REGX_TERM, $changeOrder->change_key)) {
                return 'remove.term';
            }

            if (preg_match(self::getJsonItemPattern(), $changeOrder->change_key)) {
                return 'remove.json';
            }
        }

        if ($changeOrder->change_type == 'add') {

            if ($changeOrder->change_key == 'term_groups') {
                return 'add.term_group';
            }

            if ($changeOrder->change_key == 'attachments') {
                return 'add.attachment';
            }

            if ($changeOrder->change_key == 'reservation_methods') {
                return 'add.reservation_method';
            }

            if ($changeOrder->change_key == 'payment_methods') {
                return 'add.payment_method';
            }

            if (in_array($changeOrder->change_key, self::$jsonColumns)) {
                return 'add.json';
            }

            if ($changeOrder->change_key == 'rooms') {
                return 'add.room_set';
            }

            if (preg_match(self::REGX_TERM_LIST, $changeOrder->change_key)) {
                return 'add.term';
            }
        }

        if ($changeOrder->change_type == 'modify') {

            if (preg_match(self::getJsonItemKeyPattern(), $changeOrder->change_key)) {
                return 'modify.json';
            }

            if (preg_match(self::REGX_ROOM_SET_KEY, $changeOrder->change_key)) {
                return 'modify.room_set';
            }

            if (preg_match(self::REGX_TERM_GROUP_NAME, $changeOrder->change_key)) {
                return 'modify.term_group_name';
            }

            if (preg_match(self::REGX_TERM_DESCRIPTION, $changeOrder->change_key)) {
                return 'modify.term_description';
            }

            if (preg_match(self::REGX_TERM_TITLE, $changeOrder->change_key)) {
                return 'modify.term_title';
            }

            return 'modify.meta';
        }
    }

    /**
     * Modify a simple meta field in a Contract
     *
     * @param int $contractId
     * @param string $changeKey
     * @param string $changeTo
     */
    private function modifyMeta($contractId, $changeKey, $changeTo)
    {
        return $this->contractRepo->update(
            $contractId,
            [
                $changeKey  => $changeTo,
            ]
        );
    }

    /**
     * Modify an internal JSON field
     *
     * @param Contract $contract
     * @param string $key
     * @param string $proposed
     */
    private function modifyJson(Contract $contract, $key, $proposed)
    {
        preg_match(self::getJsonItemKeyPattern(), $key, $matches);

        $innerData = json_decode($contract->{$matches[1]});

        $innerData = collect($innerData)->map(function($item) use ($matches, $proposed) {
            if ($item->index == $matches[2]) {
                $item->{$matches[3]} = $proposed;
            }
            return $item;
        });

        $this->contractRepo->update(
            $contract->id,
            [
                $matches[1] => json_encode($innerData),
            ]
        );
    }

    /**
     * Modify a Room Set
     *
     * @param string $key
     * @param string $proposed
     */
    private function modifyRoomSet($key, $proposed)
    {
        preg_match(self::REGX_ROOM_SET_KEY, $key, $matches);

        $changeColumn = $matches[2];
        $changeColumn = ($changeColumn == 'date') ? 'reservation_date' : $changeColumn;
        $changeColumn = ($changeColumn == 'rooms') ? 'rooms_offered' : $changeColumn;

        $this->roomSetRepo->update(
            $matches[1],
            [
                $changeColumn => $proposed,
            ]
        );
    }

    /**
     * Modify term group name
     *
     * @param string $key
     * @param string $proposed
     */
    private function modifyTermGroupName($key, $proposed)
    {
        preg_match(self::REGX_TERM_GROUP_NAME, $key, $matches);

        $this->termGroupRepo->update(
            $matches[1],
            [
                'name'  => json_decode($proposed)->name,
            ]
        );
    }

    /**
     * Modify term description
     *
     * @param string $key
     * @param string $proposed
     */
    private function modifyTermDescription($key, $proposed)
    {
        preg_match(self::REGX_TERM_DESCRIPTION, $key, $matches);

        $this->termRepo->update(
            $matches[2],
            [
                'description'  => json_decode($proposed)->description,
            ]
        );
    }

    /**
     * Modify term title
     *
     * @param string $key
     * @param string $proposed
     */
    private function modifyTermTitle($key, $proposed)
    {
        preg_match(self::REGX_TERM_TITLE, $key, $matches);

        $this->termRepo->update(
            $matches[2],
            [
                'title'  => json_decode($proposed)->title,
            ]
        );
    }

    /**
     * Remove a RoomSet
     *
     * @param string $key
     */
    private function removeRoomSet($key)
    {
        preg_match(self::REGX_ROOM_SET, $key, $matches);

        return $this->roomSetRepo->delete($matches[1]);
    }

    /**
     * Remove a ReservationMethod
     *
     * @param int $contractId
     * @param string $key
     *
     * @return mixed
     */
    private function removeReservationMethod($contractId, $key)
    {
        preg_match(self::REGX_RESERVATION_METHOD, $key, $matches);

        return $this->contractRepo->removeReservationMethod(
            $contractId,
            $matches[1]
        );
    }

    /**
     * Remove a PaymentMethod
     *
     * @param int $contractId
     * @param string $key
     *
     * @return mixed
     */
    private function removePaymentMethod($contractId, $key)
    {
        preg_match(self::REGX_PAYMENT_METHOD, $key, $matches);

        return $this->contractRepo->removePaymentMethod(
            $contractId,
            $matches[1]
        );
    }

    /**
     * Remove an entire TermGroup
     *
     * @param string $key
     *
     * @return mixed
     */
    private function removeTermGroup($key)
    {
        preg_match(self::REGX_TERM_GROUP, $key, $matches);

        return $this->termGroupRepo->removeGroupAndTerms($matches[1]);
    }

    /**
     * Remove a single Term
     *
     * @param string $key
     *
     * @return mixed
     */
    private function removeTerm($key)
    {
        preg_match(self::REGX_TERM, $key, $matches);

        return $this->termRepo->delete($matches[2]);
    }

    /**
     * Add a new room set
     *
     * @param Contract $contract
     * @param string $jsonData
     *
     * @return mixed
     */
    private function addRoomSet(Contract $contract, $jsonData)
    {
        $inputData = json_decode($jsonData);

        $this->roomSetRepo->addToContract(
            $contract,
            $inputData->date,
            $inputData->rooms,
            $inputData->rate,
            $inputData->name,
            collect($inputData)->only('description')->toArray()
        );
    }

    /**
     * Add a ReservationMethod
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    private function addReservationMethod($contractId, $methodId)
    {
        return $this->contractRepo->addReservationMethod(
            $contractId,
            $methodId
        );
    }

    /**
     * Add a PaymentMethod
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    private function addPaymentMethod($contractId, $methodId)
    {
        return $this->contractRepo->addPaymentMethod(
            $contractId,
            $methodId
        );
    }

    /**
     * Add term to the group
     *
     * @param string $key
     * @param string $jsonData
     */
    private function addTerm($key, $jsonData)
    {
        $data = json_decode($jsonData);

        preg_match(self::REGX_TERM_LIST, $key, $matches);

        $this->termRepo->storeForGroup(
            $matches[1],
            $data->title,
            $data->description
        );
    }

    /**
     * Add a term group
     *
     * @param int $contractId
     * @param string $jsonData
     */
    private function addTermGroup($contractId, $jsonData)
    {
        $data = json_decode($jsonData);

        $group = $this->termGroupRepo->storeForContract(
            $contractId,
            $data->name
        );

        foreach ($data->terms as $term) {
            $this->termRepo->storeForGroup(
                $group->id,
                $term->title,
                $term->description
            );
        }
    }

    /**
     * Add an item to a JSON column on the contract
     *
     * @param Contract $contract
     * @param string $key
     * @param string $jsonData
     */
    private function addJson(Contract $contract, $key, $jsonData)
    {
        $existingData = json_decode($contract->$key);

        $item = json_decode($jsonData);
        $item->index = collect($existingData)->max('index') + 1;
        $existingData[] = $item;

        $this->contractRepo->update(
            $contract->id,
            [
                $key    => json_encode($existingData),
            ]
        );
    }

    /**
     * Add an attachment
     *
     * @param int $contractId
     * @param int $attachmentId
     */
    private function addAttachment($contractId, $attachmentId)
    {
        $this->attachmentRepo->update(
            $attachmentId,
            [
                'attachable_type'   => Contract::class,
                'attachable_id'     => $contractId,
            ]
        );
    }

    /**
     * Remove a JSON item
     *
     * @param Contract $contract
     * @param string $key
     */
    private function removeJsonItem(Contract $contract, $key)
    {
        preg_match(self::getJsonItemPattern(), $key, $matches);

        $existingData = json_decode($contract->{$matches[1]});

        $removeIndex = $matches[2];
        $replacementData = array_values(
            collect($existingData)->reject(function($item, $index) use ($removeIndex) {
                return $item->index == $removeIndex;
            })->toArray()
        );

        $this->contractRepo->update(
            $contract->id,
            [
                $matches[1] => json_encode($replacementData),
            ]
        );
    }

    /**
     * Remove an attachment
     *
     * @param string $key
     */
    private function removeAttachment($key)
    {
        preg_match(self::REGX_ATTACHMENT, $key, $matches);

        dispatch(
            new DeleteAttachment($matches[1])
        );
    }

    /**
     * Calculate the regx for JSON item
     *
     * @return string
     */
    private static function getJsonItemPattern()
    {
        return sprintf(
            self::REGX_JSON_ITEM,
            implode('|', self::$jsonColumns)
        );
    }

    /**
     * Calculate the regx for JSON item internal key
     *
     * @return string
     */
    private static function getJsonItemKeyPattern()
    {
        return sprintf(
            self::REGX_JSON_ITEM_KEY,
            implode('|', self::$jsonColumns)
        );
    }
}