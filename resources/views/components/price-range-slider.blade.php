@props(['min' => 0, 'max' => 100000, 'selectedMin' => null, 'selectedMax' => null])

@php
    $min = (int) $min;
    $max = max((int) $max, $min + 1000);
@endphp

<div
    x-data="{
        min: {{ $min }},
        max: {{ $max }},
        valMin: {{ (int) ($selectedMin ?: $min) }},
        valMax: {{ (int) ($selectedMax ?: $max) }},
        dragging: null,
        form: null,
        pct(v) { return ((v - this.min) / (this.max - this.min)) * 100; },
        valueFromEvent(e) {
            const rect = this.$refs.track.getBoundingClientRect();
            const x = Math.min(Math.max(e.clientX - rect.left, 0), rect.width);
            const ratio = rect.width ? x / rect.width : 0;
            return Math.round(this.min + ratio * (this.max - this.min));
        },
        startDrag(handle, e) {
            this.dragging = handle;
            e.preventDefault();
        },
        onMove(e) {
            if (! this.dragging) return;
            const v = this.valueFromEvent(e);
            if (this.dragging === 'min') this.valMin = Math.min(v, this.valMax);
            else this.valMax = Math.max(v, this.valMin);
        },
        endDrag() {
            if (! this.dragging) return;
            this.dragging = null;
            this.form?.requestSubmit();
        },
    }"
    x-init="form = $el.closest('form')"
    @pointermove.window="onMove($event)"
    @pointerup.window="endDrag()"
    class="select-none"
>
    <div class="flex items-center justify-between text-xs font-medium text-secondary-shade dark:text-white/80">
        <span x-text="new Intl.NumberFormat('fr-FR').format(valMin) + ' FCFA'"></span>
        <span x-text="new Intl.NumberFormat('fr-FR').format(valMax) + ' FCFA'"></span>
    </div>

    <div x-ref="track" class="relative mt-4 h-1.5 rounded-full bg-grey-tint dark:bg-white/10">
        <div
            class="absolute h-1.5 rounded-full bg-primary"
            :style="`left: ${pct(valMin)}%; right: ${100 - pct(valMax)}%`"
        ></div>

        <button
            type="button"
            @pointerdown="startDrag('min', $event)"
            :style="`left: ${pct(valMin)}%`"
            class="absolute top-1/2 h-4 w-4 -translate-x-1/2 -translate-y-1/2 cursor-grab rounded-full border-2 border-primary bg-white shadow active:cursor-grabbing"
            aria-label="Prix minimum"
        ></button>
        <button
            type="button"
            @pointerdown="startDrag('max', $event)"
            :style="`left: ${pct(valMax)}%`"
            class="absolute top-1/2 h-4 w-4 -translate-x-1/2 -translate-y-1/2 cursor-grab rounded-full border-2 border-primary bg-white shadow active:cursor-grabbing"
            aria-label="Prix maximum"
        ></button>
    </div>

    <input type="hidden" name="prix_min" :value="valMin">
    <input type="hidden" name="prix_max" :value="valMax">
</div>
