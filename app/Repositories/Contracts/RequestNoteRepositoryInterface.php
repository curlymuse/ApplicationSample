<?php

namespace App\Repositories\Contracts;

interface RequestNoteRepositoryInterface
{
    /**
     * Add a new note for the supplied ProposalRequest and User
     *
     * @param int $requestId
     * @param int $authorId
     * @param string $body
     *
     * @return mixed
     */
    public function storeForProposalRequest($requestId, $authorId, $body);
}
