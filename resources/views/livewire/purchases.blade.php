<div>
    <div class="space-y-6">
        <!-- Formulario para agregar productos -->
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Agregar Producto</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto</label>
                    <select wire:model.live="productId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Seleccionar producto</option>
                        @foreach($availableProducts as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} - {{ $product->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('productId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <input type="number" wire:model="quantity" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Precio Unitario</label>
                    <input type="number" step="0.01" wire:model="priceUni" min="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('priceUni') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-end">
                    <button type="button" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" wire:click.prevent="addItems">
                        Agregar Producto
                    </button>
                </div>
        </div>
        <hr>
        <!-- Lista de productos agregados -->
        <div class="p-4 bg-white rounded-lg shadow">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Productos Agregados</h3>
                <span class="text-xl font-bold text-blue-600">
                    Total: ${{ number_format($purchaseTotal, 2) }}
                </span>
            </div>

            @if($purchase && $purchase->purchaseDetails && $purchase->purchaseDetails->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchase->purchaseDetails as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <button wire:click.prevent="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                                    class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">
                                                -
                                            </button>
                                            <span class="font-medium">{{ $item->quantity }}</span>
                                            <button wire:click.prevent="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                                    class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">${{ number_format($item->price_uni, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${{ number_format($item->subtotal, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button type="button" wire:click.prevent="removeItem({{ $item->id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay productos agregados</p>
            @endif
        </div>

        
    </div>
</div>