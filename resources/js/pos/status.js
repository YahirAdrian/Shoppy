export default function posStatus({
    salesApiBase,
    adminAuthUrl,
    logoutUrl,
    currency,
    totalSold,
    sales: initialSales,
}) {
    return {
        salesApiBase,
        adminAuthUrl,
        logoutUrl,
        currency,
        totalSold,

        sales: initialSales,

        // Preview modal
        showPreviewModal: false,
        previewSale: null,
        loadingPreview: false,
        previewError: '',

        // Delete confirm
        showDeleteConfirm: false,
        saleToDelete: null,
        deleting: false,
        deleteError: '',

        // Admin auth
        isAdminUnlocked: false,
        adminToken: null,
        adminTimer: null,
        showAdminModal: false,
        adminEmail: '',
        adminPassword: '',
        adminError: '',
        adminLoading: false,

        // Money withdrawal
        withdrawalInput: '',
        totalWithdrawn: 0,
        withdrawals: [],
        withdrawalError: '',

        init() {
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) this.lockAdmin();
            });
            window.addEventListener('beforeunload', () => this.lockAdmin());
        },

        // ── Preview modal ──────────────────────────────────────────────

        async openPreview(sale) {
            this.previewError = '';
            this.previewSale = null;
            this.showPreviewModal = true;
            this.loadingPreview = true;

            try {
                const res = await fetch(`${this.salesApiBase}/${sale.id}`, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error();
                const data = await res.json();
                this.previewSale = data.sale;
            } catch {
                this.previewError = 'No se pudo cargar el detalle de la venta.';
            } finally {
                this.loadingPreview = false;
            }
        },

        closePreview() {
            this.showPreviewModal = false;
            this.previewSale = null;
        },

        // ── Delete confirm ─────────────────────────────────────────────

        confirmDelete(sale) {
            this.deleteError = '';
            this.saleToDelete = sale;
            this.showDeleteConfirm = true;
        },

        async executeDelete() {
            if (!this.saleToDelete || this.deleting) return;
            this.deleting = true;
            this.deleteError = '';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const res = await fetch(`${this.salesApiBase}/${this.saleToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Admin-Token': this.adminToken || '',
                    },
                });

                if (res.ok) {
                    this.sales = this.sales.filter(s => s.id !== this.saleToDelete.id);
                    const deleted = this.saleToDelete;
                    this.totalSold = Math.max(0, this.totalSold - deleted.total);
                    this.showDeleteConfirm = false;
                    this.saleToDelete = null;
                } else {
                    const data = await res.json();
                    this.deleteError = data.message || 'Error al eliminar la venta.';
                }
            } catch {
                this.deleteError = 'Error de red. Intente de nuevo.';
            } finally {
                this.deleting = false;
            }
        },

        // ── Admin auth ─────────────────────────────────────────────────

        openAdminModal() {
            this.adminEmail = '';
            this.adminPassword = '';
            this.adminError = '';
            this.showAdminModal = true;
        },

        async submitAdminAuth() {
            if (this.adminLoading) return;
            this.adminLoading = true;
            this.adminError = '';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const res = await fetch(this.adminAuthUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        email: this.adminEmail,
                        password: this.adminPassword,
                    }),
                });

                const data = await res.json();

                if (res.ok) {
                    this.adminToken = data.token;
                    this.isAdminUnlocked = true;
                    this.showAdminModal = false;

                    // Auto-lock after 15 min
                    clearTimeout(this.adminTimer);
                    this.adminTimer = setTimeout(() => this.lockAdmin(), 15 * 60 * 1000);
                } else {
                    this.adminError = data.message || 'Credenciales incorrectas.';
                }
            } catch {
                this.adminError = 'Error de red. Intente de nuevo.';
            } finally {
                this.adminLoading = false;
            }
        },

        lockAdmin() {
            this.isAdminUnlocked = false;
            this.adminToken = null;
            clearTimeout(this.adminTimer);
            this.adminTimer = null;
        },

        // ── Money withdrawal ───────────────────────────────────────────

        addWithdrawal() {
            const amount = parseFloat(this.withdrawalInput);
            if (!amount || amount <= 0) {
                this.withdrawalError = 'Ingrese un monto válido.';
                return;
            }
            this.withdrawalError = '';
            this.withdrawals.push(amount);
            this.totalWithdrawn = this.withdrawals.reduce((s, a) => s + a, 0);
            this.withdrawalInput = '';
        },

        canEndSession() {
            return this.totalWithdrawn >= this.totalSold;
        },

        endSession() {
            document.getElementById('logout-form').submit();
        },

        // ── Helpers ────────────────────────────────────────────────────

        paymentLabel(method) {
            return { cash: 'Efectivo', card: 'Tarjeta', other: 'Otro' }[method] || method;
        },

        formatTime(iso) {
            return new Date(iso).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
        },

        money(n) {
            return this.currency + Number(n).toFixed(2);
        },
    };
}
