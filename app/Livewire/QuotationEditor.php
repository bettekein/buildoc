<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\QuotationItem;
use App\Models\QuotationDetail;
use Livewire\Component;
use Illuminate\Support\Collection;

class QuotationEditor extends Component
{
    public Project $project;
    
    // Main Data Structure
    // [
    //    'id' => int|null, 'name' => string, 'sort_order' => int, 
    //    'details' => [
    //       ['id' => int|null, 'name' => '', 'specification' => '', 'quantity' => 0, 'unit' => '', 'unit_price' => 0, 'cost_price' => 0, 'sort_order' => 0, 'is_new' => bool]
    //    ]
    // ]
    public array $items = [];

    // Header Stats
    public float $totalOrderAmount = 0;
    public float $totalEstimatedCost = 0;
    public float $totalProfit = 0;
    public float $totalProfitRate = 0;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->loadData();
    }

    public function loadData()
    {
        $quotationItems = $this->project->quotationItems()
            ->with(['details' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        foreach ($quotationItems as $item) {
            $details = [];
            foreach ($item->details as $detail) {
                $details[] = [
                    'id' => $detail->id,
                    'name' => $detail->name,
                    'specification' => $detail->specification,
                    'quantity' => $detail->quantity,
                    'unit' => $detail->unit,
                    'unit_price' => $detail->unit_price,
                    'cost_price' => $detail->cost_price,
                    'sort_order' => $detail->sort_order,
                    'total_price' => $detail->quantity * $detail->unit_price, // Calculated
                    // 'profit_rate' => ... calculated on fly
                ];
            }

            $this->items[] = [
                'id' => $item->id,
                'name' => $item->name,
                'sort_order' => $item->sort_order,
                'details' => $details,
            ];
        }

        $this->recalculateTypes();
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => null,
            'name' => '新しい工種',
            'sort_order' => count($this->items),
            'details' => [],
        ];
    }

    public function removeItem($index)
    {
        $item = $this->items[$index];
        if ($item['id']) {
            QuotationItem::find($item['id'])->delete(); // Soft delete
        }
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculateTypes();
    }

    public function addDetail($itemIndex)
    {
        $this->items[$itemIndex]['details'][] = [
            'id' => null,
            'name' => '',
            'specification' => '',
            'quantity' => 1,
            'unit' => '式',
            'unit_price' => 0,
            'cost_price' => 0,
            'sort_order' => count($this->items[$itemIndex]['details']),
            'total_price' => 0,
        ];
    }

    public function removeDetail($itemIndex, $detailIndex)
    {
        $detail = $this->items[$itemIndex]['details'][$detailIndex];
        if ($detail['id']) {
            QuotationDetail::find($detail['id'])->delete();
        }
        unset($this->items[$itemIndex]['details'][$detailIndex]);
        $this->items[$itemIndex]['details'] = array_values($this->items[$itemIndex]['details']);
        $this->recalculateTypes();
    }

    // Livewire updated hook
    public function updatedItems()
    {
        $this->recalculateTypes();
    }

    public function recalculateTypes()
    {
        $this->totalOrderAmount = 0;
        $this->totalEstimatedCost = 0;

        foreach ($this->items as $i => $item) {
            foreach ($item['details'] as $j => $detail) {
                $qty = (float)($detail['quantity'] ?? 0);
                $price = (float)($detail['unit_price'] ?? 0);
                $cost = (float)($detail['cost_price'] ?? 0);

                $lineTotal = $qty * $price;
                $lineCost = $qty * $cost;

                // Update computed value in array for view
                $this->items[$i]['details'][$j]['total_price'] = $lineTotal;

                $this->totalOrderAmount += $lineTotal;
                $this->totalEstimatedCost += $lineCost;
            }
        }

        $this->totalProfit = $this->totalOrderAmount - $this->totalEstimatedCost;
        if ($this->totalOrderAmount > 0) {
            $this->totalProfitRate = round(($this->totalProfit / $this->totalOrderAmount) * 100, 2);
        } else {
            $this->totalProfitRate = 0;
        }
    }

    public function save()
    {
        // Transaction ideally
        foreach ($this->items as $i => $itemData) {
            // Save Item
            $item = QuotationItem::updateOrCreate(
                ['id' => $itemData['id']],
                [
                    'tenant_id' => $this->project->tenant_id, // Ensure scope
                    'project_id' => $this->project->id,
                    'name' => $itemData['name'],
                    'sort_order' => $i,
                    'total_amount' => 0, // Recalc later if stored
                ]
            );
            
            // Assign ID back if new
            $this->items[$i]['id'] = $item->id;
            
            foreach ($itemData['details'] as $j => $detailData) {
                // Save Detail
                $detail = QuotationDetail::updateOrCreate(
                    ['id' => $detailData['id']],
                    [
                        'tenant_id' => $this->project->tenant_id,
                        'quotation_item_id' => $item->id,
                        'name' => $detailData['name'],
                        'specification' => $detailData['specification'],
                        'quantity' => $detailData['quantity'],
                        'unit' => $detailData['unit'],
                        'unit_price' => $detailData['unit_price'],
                        'cost_price' => $detailData['cost_price'],
                        'total_price' => $detailData['quantity'] * $detailData['unit_price'],
                        'sort_order' => $j,
                    ]
                );
                 $this->items[$i]['details'][$j]['id'] = $detail->id;
            }
        }
        
        $this->loadData(); // Reload to refresh state
        session()->flash('message', '見積データを保存しました。');
    }

    public function updateOrder($itemIndex, $newOrderItems) 
    {
        // For drag and drop reordering details
        // $newOrderItems comes from SortableJS usually containing 'value' (detail index)
        // Simplified approach: Reorder array based on indices.
        // This usually requires a specific sortable plugin implementation.
        // I will implement "Move Up/Down" logic for simplicity and robustness in this environment.
    }
    
    public function moveItemUp($index)
    {
        if ($index > 0) {
            $temp = $this->items[$index];
            $this->items[$index] = $this->items[$index - 1];
            $this->items[$index - 1] = $temp;
        }
    }

    public function moveItemDown($index)
    {
        if ($index < count($this->items) - 1) {
            $temp = $this->items[$index];
            $this->items[$index] = $this->items[$index + 1];
            $this->items[$index + 1] = $temp;
        }
    }

    public function moveDetailUp($itemIndex, $detailIndex)
    {
        if ($detailIndex > 0) {
            $temp = $this->items[$itemIndex]['details'][$detailIndex];
            $this->items[$itemIndex]['details'][$detailIndex] = $this->items[$itemIndex]['details'][$detailIndex - 1];
            $this->items[$itemIndex]['details'][$detailIndex - 1] = $temp;
        }
    }

    public function moveDetailDown($itemIndex, $detailIndex)
    {
        if ($detailIndex < count($this->items[$itemIndex]['details']) - 1) {
             $temp = $this->items[$itemIndex]['details'][$detailIndex];
            $this->items[$itemIndex]['details'][$detailIndex] = $this->items[$itemIndex]['details'][$detailIndex + 1];
            $this->items[$itemIndex]['details'][$detailIndex + 1] = $temp;
        }
    }

    public function render()
    {
        return view('livewire.quotation-editor');
    }
}
