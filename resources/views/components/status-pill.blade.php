@props(['status', 'label', 'tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-grey-tint text-grey dark:bg-white/10 dark:text-white/60',
        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400',
        'primary' => 'bg-primary-tint text-primary-shade dark:bg-primary/15 dark:text-primary',
        'green' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400',
        'red' => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.08em] '.($tones[$tone] ?? $tones['neutral'])]) }}>
    {{ $label }}
</span>
