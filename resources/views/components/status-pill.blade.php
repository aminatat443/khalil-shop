@props(['status', 'label', 'tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-grey-tint text-grey',
        'blue' => 'bg-blue-50 text-blue-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'primary' => 'bg-primary-tint text-primary-shade',
        'green' => 'bg-green-50 text-green-700',
        'red' => 'bg-red-50 text-red-600',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.08em] '.($tones[$tone] ?? $tones['neutral'])]) }}>
    {{ $label }}
</span>
