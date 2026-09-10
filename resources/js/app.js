import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';
import Alpine from 'alpinejs';

// Le jeton CSRF lu depuis le cookie XSRF-TOKEN (plutôt que la balise <meta>, figée au
// chargement de la page) est renouvelé par Laravel à chaque réponse — plus robuste pour
// les pages restées ouvertes longtemps, dont le jeton statique finit par expirer.
function csrfToken() {
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/);
    return match ? decodeURIComponent(match[1]) : '';
}
window.csrfToken = csrfToken;

document.addEventListener('alpine:init', () => {
    // Panier — état source de vérité côté serveur, hydraté au chargement puis mis à jour par
    // fetch (pas de rechargement de page à l'ajout, cohérent avec la section 33 du cahier des charges).
    // La taille/couleur peut être choisie plus tard, directement dans le panier, plutôt qu'à l'ajout.
    Alpine.store('cart', {
        items: [],
        count: 0,
        subtotal: 0,
        open: false,

        hydrate(data) {
            this.items = data.items ?? [];
            this.count = data.count ?? 0;
            this.subtotal = data.subtotal ?? 0;
        },

        async add(form) {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
            });

            if (!response.ok) {
                return false;
            }

            this.hydrate(await response.json());
            this.open = true;

            return true;
        },

        async chooseVariant(productId, variantId) {
            return this.postJson('/panier/variante', { product_id: productId, variant_id: variantId });
        },

        async remove(productId, variantId) {
            return this.postJson('/panier/retirer', { product_id: productId, variant_id: variantId });
        },

        async postJson(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                return false;
            }

            this.hydrate(await response.json());

            return true;
        },
    });

    // État d'interface global — permet d'ouvrir la fenêtre de connexion depuis n'importe quelle
    // page (ex : le formulaire d'avis client), pas seulement depuis le header.
    Alpine.store('ui', {
        loginOpen: false,
        authMode: 'login',

        openLogin(mode = 'login') {
            this.authMode = mode;
            this.loginOpen = true;
        },
    });

    // Favoris stockés côté navigateur (pas de compte requis, section 32 du cahier des charges)
    Alpine.store('favorites', {
        items: JSON.parse(localStorage.getItem('khalilshop_favorites') || '[]'),

        persist() {
            localStorage.setItem('khalilshop_favorites', JSON.stringify(this.items));
        },

        isFavorite(id) {
            return this.items.some((item) => item.id === id);
        },

        toggle(product) {
            if (this.isFavorite(product.id)) {
                this.items = this.items.filter((item) => item.id !== product.id);
            } else {
                this.items = [...this.items, product];
            }
            this.persist();
        },

        remove(id) {
            this.items = this.items.filter((item) => item.id !== id);
            this.persist();
        },
    });
});

window.Alpine = Alpine;
Alpine.start();
