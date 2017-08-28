<?php

namespace Tests\Traits;

use App\Models\Contract;
use App\Transformers\Contract\ContractTransformer;

trait IncorporatesChangeOrderRequests
{
    /**
     * @var array
     */
    protected $convertJsonColumns = [
        'questions',
        'meeting_spaces',
        'food_and_beverage',
    ];

    /**
     * @var \App\Models\Contract
     */
    protected $contract;

    /**
     * @var array
     */
    protected $inputData;

    /**
     * Convert the contract to an input data set that is equivalent and will results in no changes being parsed
     */
    protected function convertContractToInputData()
    {
        $transformer = app(ContractTransformer::class);
        $this->contract = Contract::find($this->contract->id);
        $this->inputData = collect($transformer->transform($this->contract))->toArray();
        $this->inputData['rooms'] = collect($this->inputData['room_sets'])->map(function($set) {
            return collect($set)
                ->only(['id', 'name', 'description', 'rate'])
                ->merge([
                    'date'      => $set->reservation_date,
                    'rooms'     => $set->rooms_offered,
                ])->toArray();
        })->toArray();
        $this->inputData['term_groups'] = collect($this->inputData['term_groups'])->map(function($group) {
            return collect($group)
                ->only(['id', 'name'])
                ->merge([
                    'terms' => collect($group->terms)->map(function($term) {
                        return collect($term)->only([
                            'id', 'description', 'title',
                        ])->toArray();
                    })->toArray(),
                ])->toArray();
        })->toArray();
        $this->inputData['reservation_methods'] = $this->contract
            ->reservationMethods()
            ->pluck('reservation_methods.id')
            ->toArray();
        $this->inputData['payment_methods'] = $this->contract
            ->paymentMethods()
            ->pluck('payment_methods.id')
            ->toArray();
        foreach ($this->convertJsonColumns as $column) {
            if ($column == 'questions') {
                $this->inputData['questions'] = $this->convertQuestions($this->inputData['questions']);
            }
            foreach ($this->inputData[$column] as $i => &$item) {
                $item->index = $i;
                $item = (array)($item);
            }
        }
        unset($this->inputData['room_sets']);
    }

    private function convertQuestions($questionGroups)
    {
        $questions = [];

        foreach ($questionGroups as $group) {
            foreach ($group['questions'] as $question) {
                $questions[] = (object)collect($question)->merge([
                    'group' => $group['group'],
                ])->toArray();
            }
        }

        return $questions;
    }
}