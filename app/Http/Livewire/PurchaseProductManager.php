<?php
// app/Livewire/PurchaseProductManager.php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
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

    protected $rules = [
        'productId' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:1',
        'priceUni' => 'required|numeric|min:0.01',
    ];

    public function mount()
    {
        if ($this->purchaseId) {
            $this->purchase = Purchase::with('purchaseDetails')->find($this->purchaseId);
        }
    }

    public function dataLoad()
    {
        $this->purchase->load('purchaseDetails.product');
        $this->products = $this->purchase->purchaseDetails;
        $this->purchaseTotal = $this->purchase->purchaseDetails->sum('subtotal');

        $this->reset(['productId', 'quantity', 'priceUni', 'products']);
        $this->quantity = 1;
    }

    private function resetFields()
    {
        $this->reset(['productId', 'quantity', 'priceUni']);
        $this->quantity = 1;
    }

    public function addItems()
    {
        $this->validate();

        $product = Product::find($this->productId);
        if (!$product) {
            $this->addError('productId', 'El producto no existe');
            return;
        }

        DB::beginTransaction();
        try {
            $this->addOrUpdatePurchaseDetail($product);
            $this->updateTotal();

            DB::commit();

            $this->dataLoad();
            $this->reset(['productId', 'quantity', 'priceUni']);
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error al agregar producto: ' . $e->getMessage());
            $this->addError('error', 'Error al agregar el producto: ' . $e->getMessage());
        }
    }

    private function addOrUpdatePurchaseDetail($product)
    {
        $existingDetail = $this->purchase->purchaseDetails()
            ->where('product_id', $product->id)
            ->first();

        if ($existingDetail) {
            $newQuantity = $existingDetail->quantity + $this->quantity;
            $existingDetail->update([
                'quantity' => $newQuantity,
                'subtotal' => $newQuantity * $this->priceUni,
                'price_uni' => $this->priceUni
            ]);
        } else {
            $this->purchase->purchaseDetails()->create([
                'product_id' => $product->id,
                'quantity' => $this->quantity,
                'price_uni' => $this->priceUni,
                'subtotal' => $this->quantity * $this->priceUni
            ]);
        }
    }

    private function updateTotal()
    {
        $total = $this->purchase->purchaseDetails()->sum('subtotal');
        $this->purchase->update(['total' => $total]);
    }

    public function removeItem(int $id)
    {
        DB::beginTransaction();
        try {
            $item = PurchaseDetail::find($id);

            $item->delete();
            // Recargar los datos
            $this->purchase->total = $this->purchase->purchaseDetails->sum('subtotal');
            $this->purchase->save();

            DB::commit();

            $this->dataLoad();
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar producto: ' . $e->getMessage());
            $this->addError('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function updatedproductId($value)
    {
        $product = Product::find($value);
        if ($product) {
            $this->priceUni = $product->price_sale;
        } else {
            $this->reset(['priceUni']);
        }
    }

    public function updateQuantity($id, $quantity)
    {
        DB::beginTransaction();
        try {
            $item = PurchaseDetail::find($id);
            $item->quantity = $quantity;
            $item->subtotal = $quantity * $item->price_uni;
            $item->save();
            // Recargar los datos
            $this->purchase->total = $this->purchase->purchaseDetails->sum('subtotal');
            $this->purchase->save();

            DB::commit();

            $this->dataLoad();
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar la cantidad en el producto: ' . $e->getMessage());
            $this->addError('error', 'Error al actualizar la cantidad en el producto: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.purchases', [
            'availableProducts' => Product::all(),
            'products' => $this->products,
            'total' => $this->total ?? 0,
            'purchase' => $this->purchase ?? null
        ]);
    }
}
