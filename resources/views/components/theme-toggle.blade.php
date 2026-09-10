<button
    type="button"
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    @click="
        dark = ! dark;
        document.documentElement.classList.toggle('dark', dark);
        localStorage.setItem('khalilshop-admin-theme', dark ? 'dark' : 'light');
    "
    class="flex h-9 w-9 items-center justify-center text-secondary-shade/70 transition hover:text-secondary-shade dark:text-white/60 dark:hover:text-white"
    aria-label="Basculer le mode sombre"
>
    <i class="fa-solid" :class="dark ? 'fa-sun' : 'fa-moon'"></i>
</button>
