<?php
// app/Services/PurchaseService.php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Repositories\PurchaseRepository;
use Illuminate\Support\Facades\Log;
use Exception;

class PurchaseService
{
    protected $repository;

    public function __construct(PurchaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Agregar un producto a la compra
     */
    public function addProductToPurchase(Purchase $purchase, int $productId, int $quantity, float $priceUni): Purchase
    {
        try {
            // Validar que el producto existe
            $product = Product::find($productId);
            if (!$product) {
                throw new Exception('El producto no existe');
            }

            // Validar cantidad
            if ($quantity <= 0) {
                throw new Exception('La cantidad debe ser mayor a 0');
            }

            // Validar precio
            if ($priceUni <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }

            $purchase = $this->repository->addProduct($purchase, $productId, $quantity, $priceUni);
            
            Log::info('Producto agregado a compra', [
                'purchase_id' => $purchase->id,
                'product_id' => $productId,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'price' => $priceUni
            ]);
            
            return $purchase;
            
        } catch (Exception $e) {
            Log::error('Error al agregar producto a compra: ' . $e->getMessage(), [
                'purchase_id' => $purchase->id,
                'product_id' => $productId
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar un ítem de la compra
     */
    public function removeItem(int $purchaseDetailId): bool
    {
        try {
            // Verificar que el ítem existe
            $detail = PurchaseDetail::find($purchaseDetailId);
            if (!$detail) {
                throw new Exception('El producto no existe en la compra');
            }

            $result = $this->repository->removeItem($purchaseDetailId);
            
            Log::info('Producto eliminado de compra', [
                'purchase_detail_id' => $purchaseDetailId,
                'purchase_id' => $detail->purchase_id,
                'product_id' => $detail->product_id
            ]);
            
            return $result;
            
        } catch (Exception $e) {
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
            // Validar cantidad
            if ($quantity <= 0) {
                throw new Exception('La cantidad debe ser mayor a 0');
            }

            // Verificar que el ítem existe
            $detail = PurchaseDetail::find($purchaseDetailId);
            if (!$detail) {
                throw new Exception('El producto no existe en la compra');
            }

            $updatedDetail = $this->repository->updateItemQuantity($purchaseDetailId, $quantity);
            
            Log::info('Cantidad actualizada en compra', [
                'purchase_detail_id' => $purchaseDetailId,
                'purchase_id' => $detail->purchase_id,
                'product_id' => $detail->product_id,
                'old_quantity' => $detail->quantity,
                'new_quantity' => $quantity
            ]);
            
            return $updatedDetail;
            
        } catch (Exception $e) {
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
        try {
            $price = $this->repository->getProductPrice($productId);
            
            if ($price === null) {
                Log::warning('Producto no encontrado para obtener precio', ['product_id' => $productId]);
            }
            
            return $price;
            
        } catch (Exception $e) {
            Log::error('Error al obtener precio de producto: ' . $e->getMessage(), [
                'product_id' => $productId
            ]);
            throw $e;
        }
    }

    /**
     * Obtener una compra con sus detalles
     */
    public function getPurchaseWithDetails(int $purchaseId): Purchase
    {
        try {
            $purchase = $this->repository->getPurchaseWithDetails($purchaseId);
            
            Log::debug('Compra obtenida con detalles', [
                'purchase_id' => $purchaseId,
                'details_count' => $purchase->purchaseDetails->count()
            ]);
            
            return $purchase;
            
        } catch (Exception $e) {
            Log::error('Error al obtener compra con detalles: ' . $e->getMessage(), [
                'purchase_id' => $purchaseId
            ]);
            throw $e;
        }
    }

    /**
     * Actualizar el total de la compra
     */
    public function updatePurchaseTotal(Purchase $purchase): Purchase
    {
        try {
            $this->repository->updateTotal($purchase);
            
            Log::info('Total de compra actualizado', [
                'purchase_id' => $purchase->id,
                'new_total' => $purchase->total
            ]);
            
            return $purchase->fresh();
            
        } catch (Exception $e) {
            Log::error('Error al actualizar total de compra: ' . $e->getMessage(), [
                'purchase_id' => $purchase->id
            ]);
            throw $e;
        }
    }

    /**
     * Procesar una compra (completar y actualizar stock)
     */
    public function processPurchase(Purchase $purchase): Purchase
    {
        try {
            // Validar que la compra tenga productos
            if ($purchase->purchaseDetails->isEmpty()) {
                throw new Exception('No se puede procesar una compra sin productos');
            }

            // Validar que la compra esté en estado pendiente
            if ($purchase->state === 'completada') {
                throw new Exception('La compra ya está completada');
            }

            // Cambiar estado a completada
            $purchase->update(['state' => 'completada']);
            
            // Procesar la compra (actualizar stock)
            $purchase = $this->repository->processPurchase($purchase);
            
            Log::info('Compra procesada exitosamente', [
                'purchase_id' => $purchase->id,
                'total' => $purchase->total,
                'items_count' => $purchase->purchaseDetails->count()
            ]);
            
            return $purchase;
            
        } catch (Exception $e) {
            Log::error('Error al procesar compra: ' . $e->getMessage(), [
                'purchase_id' => $purchase->id
            ]);
            throw $e;
        }
    }

    /**
     * Cancelar una compra
     */
    public function cancelPurchase(Purchase $purchase): Purchase
    {
        try {
            // Validar que la compra no esté ya completada
            if ($purchase->state === 'completada') {
                throw new Exception('No se puede cancelar una compra completada');
            }

            $purchase->update(['state' => 'cancelada']);
            
            Log::info('Compra cancelada', [
                'purchase_id' => $purchase->id
            ]);
            
            return $purchase;
            
        } catch (Exception $e) {
            Log::error('Error al cancelar compra: ' . $e->getMessage(), [
                'purchase_id' => $purchase->id
            ]);
            throw $e;
        }
    }

    /**
     * Verificar si un producto ya existe en la compra
     */
    public function productExistsInPurchase(Purchase $purchase, int $productId): bool
    {
        return $purchase->purchaseDetails()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Obtener el detalle de un producto en la compra
     */
    public function getPurchaseDetail(Purchase $purchase, int $productId): ?PurchaseDetail
    {
        return $purchase->purchaseDetails()
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Obtener el total de la compra
     */
    public function getPurchaseTotal(Purchase $purchase): float
    {
        return $purchase->purchaseDetails()->sum('subtotal');
    }

    /**
     * Contar productos en la compra
     */
    public function countItems(Purchase $purchase): int
    {
        return $purchase->purchaseDetails()->count();
    }

    /**
     * Verificar si la compra está vacía
     */
    public function isEmpty(Purchase $purchase): bool
    {
        return $purchase->purchaseDetails()->count() === 0;
    }
}