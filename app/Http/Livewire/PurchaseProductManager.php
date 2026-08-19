<?php
// app/Livewire/PurchaseProductManager.php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Repositories\PurchaseRepository;
use App\Services\PurchaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseProductManager extends Component
{
    public Purchase $purchase;
    public $productId;
    public $quantity = 1;
    public $priceUni = 0;
    public $products;
    public $purchaseTotal;
    public $purchaseId;

    protected PurchaseService $purchaseService;

    // Inyectar el servicio
    public function boot(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    protected $rules = [
        'productId' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
        'priceUni' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        if ($this->purchaseId) {
            $this->purchase = $this->purchaseService->getPurchaseWithDetails($this->purchaseId);
            $this->products = $this->purchase->purchaseDetails;
            $this->purchaseTotal = $this->purchase->purchaseDetails->sum('subtotal');
        }
    }

    public function dataLoad()
    {
        // $this->purchase->load('purchaseDetails.product');
        $this->purchase = $this->purchaseService->getPurchaseWithDetails($this->purchase->id);
        $this->products = $this->purchase->purchaseDetails;
        $this->purchaseTotal = $this->purchase->purchaseDetails->sum('subtotal');

        $this->reset(['productId', 'quantity', 'priceUni', 'products']);
        $this->quantity = 1;
    }

    public function addItems()
    {
        $this->validate();

        try {
            $this->purchase = $this->purchaseService->addProductToPurchase(
                $this->purchase,
                $this->productId,
                $this->quantity,
                $this->priceUni
            );

            $this->dataLoad();
            $this->reset(['productId', 'quantity', 'priceUni']);
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function removeItem(int $id)
    {
        try {
            $this->purchaseService->removeItem($id);
            $this->dataLoad();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updatedproductId(int $value)
    {
        if ($value) {
            try {
                $price = $this->purchaseService->getProductPrice($value);
                $this->priceUni = $price ?? 0;
            } catch (Exception $e) {
                $this->priceUni = 0;
            }
        } else {
            $this->reset(['priceUni']);
        }
    }

    public function updateQuantity(int $id, float $quantity)
    {
        try {
            $this->purchaseService->updateItemQuantity($id, $quantity);
            $this->dataLoad();
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function render()
    {
        return view('livewire.purchases', [
            'availableProducts' => Product::all(),
            'products' => $this->products,
            'total' => $this->total ?? 0,
            'purchase' => $this->purchase ?? null,
            'itemCount' => $this->purchase ? $this->purchaseService->countItems($this->purchase) : 0,
            'isEmpty' => $this->purchase ? $this->purchaseService->isEmpty($this->purchase) : true
        ]);
    }
}
