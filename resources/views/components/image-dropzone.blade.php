@props(['name' => 'images[]', 'multiple' => true, 'autoSubmit' => false])

<div
    x-data="{
        files: [],
        dragging: false,
        addFiles(fileList) {
            const incoming = [...fileList].filter(f => f.type.startsWith('image/'));
            if (incoming.length === 0) return;
            const dt = new DataTransfer();
            const existing = {{ $multiple ? 'true' : 'false' }} ? this.files.map(f => f.file) : [];
            [...existing, ...incoming].forEach(f => dt.items.add(f));
            this.$refs.input.files = dt.files;
            this.syncPreviews();
            @if($autoSubmit) this.$nextTick(() => this.$refs.input.form.requestSubmit()); @endif
        },
        syncPreviews() {
            this.files.forEach(f => URL.revokeObjectURL(f.url));
            this.files = [...this.$refs.input.files].map(file => ({ file, url: URL.createObjectURL(file) }));
        },
        removeAt(index) {
            const dt = new DataTransfer();
            this.files.forEach((f, i) => { if (i !== index) dt.items.add(f.file); });
            this.$refs.input.files = dt.files;
            this.syncPreviews();
        },
    }"
>
    <div
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="dragging = false; addFiles([...$event.dataTransfer.files])"
        @click="$refs.input.click()"
        :class="dragging ? 'border-primary bg-primary-tint/40 dark:bg-primary/10' : 'border-secondary-shade/20 hover:border-secondary-shade/40 dark:border-white/15 dark:hover:border-white/30'"
        class="cursor-pointer border-2 border-dashed px-6 py-8 text-center transition"
    >
        <input
            x-ref="input"
            type="file"
            name="{{ $name }}"
            accept="image/*"
            {{ $multiple ? 'multiple' : '' }}
            class="hidden"
            @change="syncPreviews(); {{ $autoSubmit ? '$refs.input.form.requestSubmit()' : '' }}"
        >
        <i class="fa-solid fa-cloud-arrow-up text-xl text-secondary-shade/30 dark:text-white/30"></i>
        <p class="mt-3 text-sm text-secondary-shade dark:text-white/80">
            Glissez vos photos ici ou <span class="text-primary underline">parcourez</span>
        </p>
        <p class="mt-1 text-xs text-grey dark:text-white/40">PNG ou JPG, 4 Mo max par photo</p>
    </div>

    <div x-show="files.length > 0" x-cloak class="mt-4 grid grid-cols-4 gap-3 sm:grid-cols-6">
        <template x-for="(item, index) in files" :key="index">
            <div class="group relative aspect-square overflow-hidden bg-grey-tint dark:bg-white/10">
                <img :src="item.url" class="h-full w-full object-cover">
                <button
                    type="button"
                    @click.stop="removeAt(index)"
                    class="absolute inset-0 flex items-center justify-center bg-secondary-shade/60 opacity-0 transition group-hover:opacity-100"
                    aria-label="Retirer"
                >
                    <i class="fa-solid fa-xmark text-white"></i>
                </button>
                <span x-show="index === 0" class="absolute left-1 top-1 bg-secondary-shade px-1.5 py-0.5 text-[8px] font-semibold uppercase text-white">Principale</span>
            </div>
        </template>
    </div>
</div>
