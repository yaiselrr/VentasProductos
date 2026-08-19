<?php

namespace App\Observers;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        // Si la compra se crea directamente como completada
        if ($purchase->state === 'completada') {
            $this->updateProductStock($purchase);
        }
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {
        // Verificar si el estado cambió a 'completada' y no ha sido procesada
        if ($purchase->wasChanged('state') && 
            $purchase->state === 'completada') {
            
            $this->updateProductStock($purchase);
        }
    }

    /**
     * Actualizar el stock de los productos
     */
    protected function updateProductStock(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // Cargar los detalles con sus productos
            $purchase->load('purchaseDetails.product');
            
            foreach ($purchase->purchaseDetails as $detail) {
                if ($detail->product) {
                    // Restar la cantidad del stock_max
                    $detail->product->decrement('stock_max', $detail->quantity);
                }
            }
        });
    }

    /**
     * Handle the Purchase "deleted" event.
     */
    public function deleted(Purchase $purchase): void
    {
        // Opcional: Si se elimina una compra completada, restaurar el stock
        if ($purchase->state === 'completada') {
            $this->restoreProductStock($purchase);
        }
    }

    /**
     * Restaurar el stock (opcional)
     */
    protected function restoreProductStock(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $purchase->load('purchaseDetails.product');
            
            foreach ($purchase->purchaseDetails as $detail) {
                if ($detail->product) {
                    // Sumar la cantidad al stock_max
                    $detail->product->increment('stock_max', $detail->quantity);
                }
            }
        });
    }

    /**
     * Handle the Purchase "restored" event.
     */
    public function restored(Purchase $purchase): void
    {
        // Si se restaura una compra, volver a procesar
        if ($purchase->state === 'completada') {
            $this->updateProductStock($purchase);
        }
    }

    /**
     * Handle the Purchase "forceDeleted" event.
     */
    public function forceDeleted(Purchase $purchase): void
    {
        // Similar al deleted pero sin restaurar
    }
}