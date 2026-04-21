export default function posSale({ searchUrl, storeUrl }) {
    return {
        searchUrl,
        storeUrl,

        query: '',
        searchTimer: null,
        searching: false,
        searchResults: [],
        searchMessage: '',
        cart: [],

        showPaymentModal: false,
        showOptional: false,
        payment: { method: 'cash', tendered: '', customer_name: '', note: '' },

        showStockWarning: false,
        stockIssues: [],
        submitting: false,
        submitError: '',
        lastSale: null,

        init() {
            try {
                const stored = localStorage.getItem('pos_cart');
                if (stored) this.cart = JSON.parse(stored);
            } catch (e) {
                this.cart = [];
            }
        },

        persist() {
            localStorage.setItem('pos_cart', JSON.stringify(this.cart));
        },

        scheduleSearch() {
            clearTimeout(this.searchTimer);
            this.searchMessage = '';
            if (this.query.trim() === '') {
                this.searchResults = [];
                return;
            }
            this.searchTimer = setTimeout(() => this.searchNow(), 800);
        },

        async searchNow() {
            const q = this.query.trim();
            if (!q) return;
            clearTimeout(this.searchTimer);
            this.searching = true;
            this.searchMessage = '';
            try {
                const res = await fetch(`${this.searchUrl}?q=${encodeURIComponent(q)}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                const products = data.products || [];
                if (products.length === 0) {
                    this.searchResults = [];
                    this.searchMessage = 'No se encontraron productos con ese código o nombre.';
                } else if (products.length === 1) {
                    this.addToCart(products[0]);
                    this.searchResults = [];
                    this.query = '';
                } else {
                    this.searchResults = products;
                }
            } catch (e) {
                this.searchMessage = 'Error al buscar productos.';
            } finally {
                this.searching = false;
            }
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                existing.quantity = parseFloat(existing.quantity) + 1;
            } else {
                this.cart.push({
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
            this.persist();
            this.searchResults = [];
            this.query = '';
        },

        removeItem(index) {
            this.cart.splice(index, 1);
            this.persist();
        },

        resetSale() {
            this.cart = [];
            this.query = '';
            this.searchResults = [];
            this.searchMessage = '';
            this.payment = { method: 'cash', tendered: '', customer_name: '', note: '' };
            localStorage.removeItem('pos_cart');
        },

        lineSubtotal(item) {
            const qty = parseFloat(item.quantity) || 0;
            const price = parseFloat(item.unit_price) || 0;
            const disc = parseFloat(item.discount) || 0;
            return Math.max(0, qty * price - disc);
        },

        subtotal() {
            return this.cart.reduce((sum, i) => sum + (parseFloat(i.quantity) || 0) * (parseFloat(i.unit_price) || 0), 0);
        },

        totalDiscount() {
            return this.cart.reduce((sum, i) => sum + (parseFloat(i.discount) || 0), 0);
        },

        total() {
            return Math.max(0, this.subtotal() - this.totalDiscount());
        },

        changeAmount() {
            const tendered = parseFloat(this.payment.tendered) || 0;
            return Math.max(0, tendered - this.total());
        },

        openPayment() {
            if (this.cart.length === 0) return;
            this.submitError = '';
            this.showPaymentModal = true;
        },

        canSubmit() {
            const tendered = parseFloat(this.payment.tendered) || 0;
            return !this.submitting
                && this.payment.method === 'cash'
                && tendered >= this.total()
                && this.total() > 0;
        },

        async submitSale(force = false) {
            if (this.submitting) return;
            this.submitting = true;
            this.submitError = '';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const payload = {
                items: this.cart.map(i => ({
                    product_id: i.id,
                    quantity: parseFloat(i.quantity),
                    discount: parseFloat(i.discount) || 0,
                })),
                payment_method: this.payment.method,
                amount_tendered: parseFloat(this.payment.tendered) || 0,
                customer_name: this.payment.customer_name || null,
                note: this.payment.note || null,
                force_low_stock: force,
            };

            try {
                const res = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(payload),
                });

                if (res.status === 201) {
                    const data = await res.json();
                    this.lastSale = data.sale;
                    this.showPaymentModal = false;
                    this.resetSale();
                    // Phase 4 will replace this with printReceipt()
                    alert(`Venta #${data.sale.id} registrada por ${data.sale.total.toFixed(2)}.`);
                    return;
                }

                if (res.status === 422) {
                    const data = await res.json();
                    if (Array.isArray(data.stock_issues) && data.stock_issues.length > 0) {
                        this.stockIssues = data.stock_issues;
                        this.showPaymentModal = false;
                        this.showStockWarning = true;
                        return;
                    }
                    this.submitError = data.message || 'Datos inválidos.';
                    return;
                }

                this.submitError = 'Error al registrar la venta. Intente de nuevo.';
            } catch (e) {
                this.submitError = 'Error de red. Intente de nuevo.';
            } finally {
                this.submitting = false;
            }
        },

        forceSubmit() {
            this.showStockWarning = false;
            this.submitSale(true);
        },
    };
}
