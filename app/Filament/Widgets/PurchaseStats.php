<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PurchaseStats extends BaseWidget
{
    protected static ?int $sort = 1; // Orden en el dashboard

    protected function getStats(): array
    {
        // Total de compras
        $totalPurchases = Purchase::count();
        
        // Total de compras completadas
        $completedPurchases = Purchase::where('state', 'completada')->count();
        
        // Total de compras pendientes
        $pendingPurchases = Purchase::where('state', 'pendiente')->count();
        
        // Valor total de todas las compras
        $totalValue = Purchase::sum('total');
        
        // Valor promedio por compra
        $averageValue = $totalPurchases > 0 ? $totalValue / $totalPurchases : 0;

        return [
            Stat::make('Total Compras', $totalPurchases)
                ->description('Compras registradas')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart([7, 3, 5, 2, 8, 4, 6]) // Gráfica de tendencia
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Completadas', $completedPurchases)
                ->description('Compras finalizadas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Pendientes', $pendingPurchases)
                ->description('Compras por procesar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Valor Total', '$' . number_format($totalValue, 2))
                ->description('Monto total de compras')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Promedio por Compra', '$' . number_format($averageValue, 2))
                ->description('Valor promedio')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }
}