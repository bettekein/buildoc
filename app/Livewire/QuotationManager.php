<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\QuotationItem;
use App\Models\QuotationDetail;
use Livewire\Component;

class QuotationManager extends Component
{
    public Project $project;
    public $items = []; // Array to hold item data
    public $grandTotal = 0;
    public $totalCost = 0;
    public $profitMargin = 0;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->loadItems();
    }

    public function loadItems()
    {
        // Load existing items with details
        $items = $this->project->quotationItems()->with('details')->orderBy('sort_order')->get();

        if ($items->isEmpty()) {
            // Initialize with one empty item if none exist
            $this->items = [
                $this->getNewItemStructure()
            ];
        } else {
            $this->items = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'total_amount' => $item->total_amount,
                    'expanded' => true, // UI state
                    'details' => $item->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'name' => $detail->name,
                            'specification' => $detail->specification,
                            'quantity' => $detail->quantity,
                            'unit' => $detail->unit,
                            'unit_price' => $detail->unit_price,
                            'cost_price' => $detail->cost_price,
                            'total_price' => $detail->total_price,
                        ];
                    })->toArray()
                ];
            })->toArray();
        }
        
        $this->calculateTotals();
    }

    public function getNewItemStructure()
    {
        return [
            'id' => null,
            'name' => '新規大項目',
            'total_amount' => 0,
            'expanded' => true,
            'details' => [
                $this->getNewDetailStructure()
            ]
        ];
    }

    public function getNewDetailStructure()
    {
        return [
            'id' => null,
            'name' => '',
            'specification' => '',
            'quantity' => 1,
            'unit' => '式',
            'unit_price' => 0,
            'cost_price' => 0,
            'total_price' => 0,
        ];
    }

    public function addItem()
    {
        $this->items[] = $this->getNewItemStructure();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index
        $this->calculateTotals();
    }

    public function addDetail($itemIndex)
    {
        $this->items[$itemIndex]['details'][] = $this->getNewDetailStructure();
    }

    public function removeDetail($itemIndex, $detailIndex)
    {
        unset($this->items[$itemIndex]['details'][$detailIndex]);
        $this->items[$itemIndex]['details'] = array_values($this->items[$itemIndex]['details']); // Re-index
        $this->calculateTotals();
    }

    public function updated($propertyName)
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $grandTotal = 0;
        $totalCost = 0;

        foreach ($this->items as $itemIndex => $item) {
            $itemTotal = 0;
            $itemCost = 0;

            foreach ($item['details'] as $detailIndex => $detail) {
                // Calculate line total
                $qty = (float) ($detail['quantity'] ?? 0);
                $unitPrice = (float) ($detail['unit_price'] ?? 0);
                $costPrice = (float) ($detail['cost_price'] ?? 0);

                $lineTotal = $qty * $unitPrice;
                $lineCost = $qty * $costPrice;

                // Update detail in array
                $this->items[$itemIndex]['details'][$detailIndex]['total_price'] = $lineTotal;

                $itemTotal += $lineTotal;
                $itemCost += $lineCost;
            }

            // Update item total
            $this->items[$itemIndex]['total_amount'] = $itemTotal;
            $grandTotal += $itemTotal;
            $totalCost += $itemCost;
        }

        $this->grandTotal = $grandTotal;
        $this->totalCost = $totalCost;
        
        if ($this->grandTotal > 0) {
            $this->profitMargin = (($this->grandTotal - $this->totalCost) / $this->grandTotal) * 100;
        } else {
            $this->profitMargin = 0;
        }
    }

    public function save()
    {
        // Transaction logic would be good here
        \DB::transaction(function () {
            // Delete existing items? Or sync?
            // For simplicity in this iteration: delete all old items and recreate (careful with IDs if we want to preserve history, but for now full replace is easier or update if ID exists)
            
            // Better approach: Update if ID exists, create if null. Delete missing.
            
            $existingItemIds = collect($this->items)->pluck('id')->filter()->toArray();
            $this->project->quotationItems()->whereNotIn('id', $existingItemIds)->delete();

            foreach ($this->items as $i => $itemData) {
                $item = $this->project->quotationItems()->updateOrCreate(
                    ['id' => $itemData['id']],
                    [
                        'name' => $itemData['name'],
                        'total_amount' => $itemData['total_amount'],
                        'sort_order' => $i,
                    ]
                );

                $existingDetailIds = collect($itemData['details'])->pluck('id')->filter()->toArray();
                $item->details()->whereNotIn('id', $existingDetailIds)->delete();

                foreach ($itemData['details'] as $detailData) {
                    $item->details()->updateOrCreate(
                        ['id' => $detailData['id']],
                        [
                            'name' => $detailData['name'],
                            'specification' => $detailData['specification'],
                            'quantity' => $detailData['quantity'],
                            'unit' => $detailData['unit'],
                            'unit_price' => $detailData['unit_price'],
                            'cost_price' => $detailData['cost_price'],
                            'total_price' => $detailData['total_price'],
                        ]
                    );
                }
            }
        });

        session()->flash('message', '見積を保存しました。');
        $this->loadItems(); // Reload to get fresh IDs
    }

    public function render()
    {
        return view('livewire.quotation-manager')->layout('layouts.app');
    }
}
