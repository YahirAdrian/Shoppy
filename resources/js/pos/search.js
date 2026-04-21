export default function posSearch({ searchUrl, saleUrl, currency }) {
    return {
        searchUrl,
        saleUrl,
        currency,

        query: '',
        activeCategoryId: null,
        products: [],
        currentPage: 1,
        lastPage: 1,
        totalProducts: 0,
        loading: false,
        searchTimer: null,

        init() {
            this.loadProducts();
        },

        scheduleSearch() {
            clearTimeout(this.searchTimer);
            this.currentPage = 1;
            this.searchTimer = setTimeout(() => this.loadProducts(), 600);
        },

        selectCategory(id) {
            this.activeCategoryId = id;
            this.currentPage = 1;
            this.loadProducts();
        },

        async loadProducts() {
            this.loading = true;
            const params = new URLSearchParams({ page: this.currentPage });
            if (this.query.trim()) params.set('q', this.query.trim());
            if (this.activeCategoryId) params.set('category_id', this.activeCategoryId);

            try {
                const res = await fetch(`${this.searchUrl}?${params}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.products = data.products;
                this.currentPage = data.meta.current_page;
                this.lastPage = data.meta.last_page;
                this.totalProducts = data.meta.total;
            } catch (e) {
                this.products = [];
            } finally {
                this.loading = false;
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadProducts();
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadProducts();
            }
        },

        isOutOfStock(product) {
            return product.stock <= 0;
        },

        isLowStock(product) {
            return product.stock > 0 && product.stock <= product.low_stock_threshold;
        },

        addToSale(product) {
            let cart = [];
            try {
                const stored = localStorage.getItem('pos_cart');
                if (stored) cart = JSON.parse(stored);
            } catch (e) {
                cart = [];
            }

            const existing = cart.find(i => i.id === product.id);
            if (existing) {
                existing.quantity = parseFloat(existing.quantity) + 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    barcode: product.barcode,
                    category: product.category,
                    unit_price: parseFloat(product.selling_price),
                    stock: parseFloat(product.stock),
                    quantity: 1,
                    discount: 0,
                });
            }

            localStorage.setItem('pos_cart', JSON.stringify(cart));
            window.location.href = this.saleUrl + '?added=1';
        },
    };
}
