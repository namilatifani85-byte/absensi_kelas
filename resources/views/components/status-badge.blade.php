@props(['status'])

@php
    $statusMap = [
        'present' => [
            'label' => 'Hadir',
            'class' => 'badge-present',
            'style' => 'background-color: #E8F7F0; color: #065F46; border: 1px solid #A7F3D0;',
            'icon'  => 'heroicon-o-check-circle',
        ],
        'permit' => [
            'label' => 'Izin',
            'class' => 'badge-permit',
            'style' => 'background-color: #FDEEE9; color: #9A3412; border: 1px solid #FED7AA;',
            'icon'  => 'heroicon-o-document-text',
        ],
        'sick' => [
            'label' => 'Sakit',
            'class' => 'badge-sick',
            'style' => 'background-color: #EBF3FC; color: #1E40AF; border: 1px solid #BFDBFE;',
            'icon'  => 'heroicon-o-heart',
        ],
        'absent' => [
            'label' => 'Alpa',
            'class' => 'badge-absent',
            'style' => 'background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;',
            'icon'  => 'heroicon-o-x-circle',
        ],
    ];

    $current = $statusMap[$status] ?? [
        'label' => ucfirst($status),
        'class' => '',
        'style' => 'background-color: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;',
        'icon'  => 'heroicon-o-question-mark-circle',
    ];
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $current['class'] }}"
      style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px; {{ $current['style'] }}">
    @if($status === 'present')
        <x-heroicon-o-check-circle style="width: 0.875rem; height: 0.875rem;" />
    @elseif($status === 'permit')
        <x-heroicon-o-document-text style="width: 0.875rem; height: 0.875rem;" />
    @elseif($status === 'sick')
        <x-heroicon-o-heart style="width: 0.875rem; height: 0.875rem;" />
    @elseif($status === 'absent')
        <x-heroicon-o-x-circle style="width: 0.875rem; height: 0.875rem;" />
    @endif
    <span>{{ $current['label'] }}</span>
</span>
