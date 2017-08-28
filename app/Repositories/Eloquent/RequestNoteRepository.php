<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RequestNoteRepositoryInterface;

class RequestNoteRepository extends Repository implements RequestNoteRepositoryInterface
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
    public function storeForProposalRequest($requestId, $authorId, $body)
    {
        return $this->store([
            'proposal_request_id'   => $requestId,
            'author_id'             => $authorId,
            'body'                  => $body,
        ]);
    }
}
