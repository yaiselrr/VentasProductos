<?php
// app/Repositories/PurchaseRepository.php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseRepository
{
    public function createWithDetails(array $purchaseData, array $details): Purchase
    {
        return DB::transaction(function () use ($purchaseData, $details) {
            $purchase = Purchase::create($purchaseData);
            
            foreach ($details as $detail) {
                $purchase->purchaseDetails()->create($detail);
            }
            
            $this->updateTotal($purchase);
            
            return $purchase;
        });
    }

    public function addProduct(Purchase $purchase, $productId, $quantity, $priceUni)
    {
        return DB::transaction(function () use ($purchase, $productId, $quantity, $priceUni) {
            $existingDetail = $purchase->purchaseDetails()
                ->where('product_id', $productId)
                ->first();

            if ($existingDetail) {
                $newQuantity = $existingDetail->quantity + $quantity;
                $existingDetail->update([
                    'quantity' => $newQuantity,
                    'subtotal' => $newQuantity * $priceUni,
                    'price_uni' => $priceUni
                ]);
            } else {
                $purchase->purchaseDetails()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_uni' => $priceUni,
                    'subtotal' => $quantity * $priceUni
                ]);
            }

            $this->updateTotal($purchase);
            
            return $purchase;
        });
    }
    /**
     * Eliminar un ítem de la compra
     */
    public function removeItem(int $purchaseDetailId): bool
    {
        try {
            $item = PurchaseDetail::findOrFail($purchaseDetailId);
            $purchase = $item->purchase;
            
            DB::transaction(function () use ($item, $purchase) {
                // Eliminar el detalle
                $item->delete();
                
                // Actualizar el total de la compra
                $this->updateTotal($purchase);
            });
            
            Log::info('Producto eliminado de la compra', [
                'purchase_detail_id' => $purchaseDetailId,
                'purchase_id' => $purchase->id
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar producto de compra: ' . $e->getMessage(), [
                'purchase_detail_id' => $purchaseDetailId
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar la cantidad de un ítem
     */
    public function updateItemQuantity(int $purchaseDetailId, int $quantity): PurchaseDetail
    {
        try {
            $item = PurchaseDetail::findOrFail($purchaseDetailId);
            $purchase = $item->purchase;
            
            DB::transaction(function () use ($item, $quantity, $purchase) {
                // Validar que la cantidad sea positiva
                if ($quantity <= 0) {
                    throw new \Exception('La cantidad debe ser mayor a 0');
                }
                
                // Actualizar cantidad y subtotal
                $item->quantity = $quantity;
                $item->subtotal = $quantity * $item->price_uni;
                $item->save();
                
                // Actualizar el total de la compra
                $this->updateTotal($purchase);
            });
            
            Log::info('Cantidad actualizada en compra', [
                'purchase_detail_id' => $purchaseDetailId,
                'new_quantity' => $quantity
            ]);
            
            return $item->fresh();
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar cantidad en compra: ' . $e->getMessage(), [
                'purchase_detail_id' => $purchaseDetailId,
                'quantity' => $quantity
            ]);
            throw $e;
        }
    }

    /**
     * Obtener el precio de un producto
     */
    public function getProductPrice(int $productId): ?float
    {
        $product = Product::find($productId);
        return $product ? $product->price_sale : null;
    }

    /**
     * Obtener una compra con sus detalles
     */
    public function getPurchaseWithDetails(int $purchaseId): Purchase
    {
        return Purchase::with(['purchaseDetails.product', 'provider'])
            ->findOrFail($purchaseId);
    }

    public function updateTotal(Purchase $purchase)
    {
        $total = $purchase->purchaseDetails()->sum('subtotal');
        $purchase->update(['total' => $total]);
    }

    public function processPurchase(Purchase $purchase)
    {
        return DB::transaction(function () use ($purchase) {
            if ($purchase->state === 'completada') {
                $this->updateProductStock($purchase);
            }
            return $purchase;
        });
    }

    private function updateProductStock(Purchase $purchase)
    {
        $purchase->load('purchaseDetails.product');
        
        foreach ($purchase->purchaseDetails as $detail) {
            if ($detail->product) {
                $detail->product->decrement('stock_max', $detail->quantity);
            }
        }
    }
}