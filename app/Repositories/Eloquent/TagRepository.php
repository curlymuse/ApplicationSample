<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\TagRepositoryInterface;

class TagRepository extends Repository implements TagRepositoryInterface
{

    /**
     * Accept an array of strings, insert the new ones, and pull IDs for both new and existing
     *
     * @param array $tagStrings
     *
     * @return array
     */
    public function exchangeForIds($tagStrings = [])
    {
        $tagStrings = collect($tagStrings)->map(function($item) {
            return strtolower($item);
        });

        $existingTags = $this->model
            ->whereIn('name', $tagStrings)
            ->get();

        $newIds = [];
        foreach ($tagStrings as $string) {
            if (! $existingTags->pluck('name')->contains($string)) {
                $tag = $this->store([
                    'name'  => $string
                ]);
                $newIds[] = $tag->id;
            }
        }

        return $existingTags->pluck('id')
            ->merge($newIds)
            ->toArray();
    }

    /**
     * Get all tags for this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->whereHas('events', function($query) use ($licenseeId) {
                $query->whereLicenseeId($licenseeId);
            })->get();
    }
}