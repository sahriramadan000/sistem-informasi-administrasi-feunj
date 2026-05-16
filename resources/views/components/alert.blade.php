@props(['type' => 'info', 'message' => '', 'errorId' => null, 'dismissible' => true])

@php
    $bgColors = [
        'success' => 'bg-green-50',
        'error' => 'bg-red-50',
        'warning' => 'bg-yellow-50',
        'info' => 'bg-blue-50',
    ];
    
    $borderColors = [
        'success' => 'border-green-200',
        'error' => 'border-red-200',
        'warning' => 'border-yellow-200',
        'info' => 'border-blue-200',
    ];
    
    $iconColors = [
        'success' => 'text-green-400',
        'error' => 'text-red-400',
        'warning' => 'text-yellow-400',
        'info' => 'text-blue-400',
    ];
    
    $textColors = [
        'success' => 'text-green-800',
        'error' => 'text-red-800',
        'warning' => 'text-yellow-800',
        'info' => 'text-blue-800',
    ];
    
    $hoverBgColors = [
        'success' => 'hover:bg-green-100',
        'error' => 'hover:bg-red-100',
        'warning' => 'hover:bg-yellow-100',
        'info' => 'hover:bg-blue-100',
    ];
    
    $buttonTextColors = [
        'success' => 'text-green-500',
        'error' => 'text-red-500',
        'warning' => 'text-yellow-500',
        'info' => 'text-blue-500',
    ];
    
    $icons = [
        'success' => 'check-circle',
        'error' => 'alert-circle',
        'warning' => 'alert-triangle',
        'info' => 'info',
    ];
@endphp

<div class="rounded-lg {{ $bgColors[$type] ?? $bgColors['info'] }} p-4 border {{ $borderColors[$type] ?? $borderColors['info'] }}" 
     role="alert" 
     x-data="{ visible: true }" 
     x-show="visible"
     @click.away="visible = false"
     wire:key="alert-{{ $errorId ?? time() }}">
    
    <div class="flex gap-3">
        {{-- Icon --}}
        <div class="flex-shrink-0">
            <i data-lucide="{{ $icons[$type] ?? 'info' }}" class="h-5 w-5 {{ $iconColors[$type] ?? $iconColors['info'] }}"></i>
        </div>
        
        {{-- Message Content --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium {{ $textColors[$type] ?? $textColors['info'] }}">
                {{ $message }}
            </p>
            
            {{-- Error ID Display (if provided) --}}
            @if ($errorId)
                <div class="mt-2 flex items-center gap-2 flex-wrap">
                    <div class="bg-{{ $type }}-100 px-2 py-1 rounded text-xs font-mono {{ $textColors[$type] ?? $textColors['info'] }}">
                        Error ID: {{ $errorId }}
                    </div>
                    <button 
                        type="button"
                        @click="
                            navigator.clipboard.writeText('{{ $errorId }}').then(() => {
                                const btn = $el;
                                const originalText = btn.textContent;
                                btn.textContent = 'Copied!';
                                setTimeout(() => { btn.textContent = originalText; }, 2000);
                            });
                        "
                        class="text-xs {{ $buttonTextColors[$type] ?? $buttonTextColors['info'] }} hover:underline flex items-center gap-1">
                        <i data-lucide="copy" class="h-3 w-3"></i>
                        <span>Copy ID</span>
                    </button>
                </div>
            @endif
            
            {{-- Additional slot content --}}
            @if ($slot->isNotEmpty())
                <div class="mt-2 text-sm {{ $textColors[$type] ?? $textColors['info'] }}">
                    {{ $slot }}
                </div>
            @endif
        </div>
        
        {{-- Close Button (if dismissible) --}}
        @if ($dismissible)
            <div class="flex-shrink-0">
                <button 
                    type="button" 
                    @click="visible = false"
                    class="inline-flex rounded-md p-1.5 {{ $buttonTextColors[$type] ?? $buttonTextColors['info'] }} {{ $hoverBgColors[$type] ?? $hoverBgColors['info'] }} focus:outline-none">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
        @endif
    </div>
</div>
