@php
$isProcessing = $record->status !== \App\Enums\Reports\Statuses::Completed || $record->status !== \App\Enums\Reports\Statuses::Failed;
@endphp

<div class="space-y-4" @if($isProcessing) wire:poll.3s="$refresh" @endif>
    @if (!empty($record->payload) && empty($record->error_message))
        <x-filament::section>
            <x-slot name="heading">
                <span style="color: #34d399; font-weight: 600;">
                    Данные отчета
                </span>
            </x-slot>

            <div style="display: flex; gap: 16px; width: 100%; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 250px; background-color: #09090b; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; font-weight: 500; color: #a1a1aa;">
                        Максимальная стоимость заказа
                    </div>
                    <div style="margin-top: 8px; font-size: 20px; font-weight: 700; color: #ffffff;">
                        {{ $record->payload['max_price'] ?? '0.00' }} <span style="font-size: 12px; font-weight: 400; color: #71717a;">бел. руб.</span>
                    </div>
                </div>

                <div style="flex: 1; min-width: 250px; background-color: #09090b; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; font-weight: 500; color: #a1a1aa;">
                        Общая стоимость заказов
                    </div>
                    <div style="margin-top: 8px; font-size: 20px; font-weight: 700; color: #ffffff;">
                        {{ $record->payload['total_cost'] ?? '0.00' }} <span style="font-size: 12px; font-weight: 400; color: #71717a;">бел. руб.</span>
                    </div>
                </div>

                <div style="flex: 1; min-width: 250px; background-color: #09090b; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; font-weight: 500; color: #a1a1aa;">
                        Максимальное количество за заказ
                    </div>
                    <div style="margin-top: 8px; font-size: 20px; font-weight: 700; color: #ffffff;">
                        {{ $record->payload['max_quantity'] ?? '0' }} <span style="font-size: 12px; font-weight: 400; color: #71717a;">штук</span>
                    </div>
                </div>

                <div style="flex: 1; min-width: 250px; background-color: #09090b; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; font-weight: 500; color: #a1a1aa;">
                        Общее количество
                    </div>
                    <div style="margin-top: 8px; font-size: 20px; font-weight: 700; color: #ffffff;">
                        {{ $record->payload['total_quantity'] ?? '0' }} <span style="font-size: 12px; font-weight: 400; color: #71717a;">штук</span>
                    </div>
                </div>

                @if (isset($record->payload['max_cost_by_type']))
                <div style="flex: 1; min-width: 100%; background-color: #09090b; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; font-weight: 500; color: #a1a1aa;">
                        Максимальная стоимость заказа по типам топлива
                    </div>
                    <div style="display: flex; gap: 20px; width: 100%; flex-wrap: wrap; margin-top: 8px; font-size: 20px; font-weight: 700; color: #ffffff;">
                        @foreach ($record->payload['max_cost_by_type'] as $code => $cost)
                            <div>
                                <span style="color: #e74f68">{{ strtoupper($code) }}</span> {{ $cost ?? 0.00 }} <span style="font-size: 12px; font-weight: 400; color: #71717a;">бел. руб.</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                @if (isset($record->payload['max_cost_in_time_by_type']))
                <div style="flex: 1; min-width: 100%; background-color: #09090b; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 16px;">
                    <div style="font-size: 12px; font-weight: 500; color: #a1a1aa;">
                        Максимальная стоимость единицы топлива в момент заказа
                    </div>
                    
                    <div style="display: flex; gap: 20px; width: 100%; flex-wrap: wrap; margin-top: 8px; font-size: 20px; font-weight: 700; color: #ffffff;">
                        @foreach ($record->payload['max_cost_in_time_by_type'] as $code => $cost)
                            <div>
                                <span style="color: #4fd0e7">{{ strtoupper($code) }}</span> {{ $cost ?? 0.00 }} <span style="font-size: 12px; font-weight: 400; color: #71717a;">бел. руб.</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </x-filament::section>
    @endif

    @if($record->error_message)
        <x-filament::section>
            <x-slot name="heading">
                <span class="text-danger-600 dark:text-danger-400">
                    Критическая ошибка при формировании отчета
                </span>
            </x-slot>
            
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $record->error_message }}
            </p>
        </x-filament::section>
    @endif
</div>
