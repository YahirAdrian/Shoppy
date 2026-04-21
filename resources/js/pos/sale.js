export default function posSale({ searchUrl, storeUrl, currency = '$', business = {} }) {
    return {
        searchUrl,
        storeUrl,
        currency,
        business,

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
        addedToast: false,

        init() {
            try {
                const stored = localStorage.getItem('pos_cart');
                if (stored) this.cart = JSON.parse(stored);
            } catch (e) {
                this.cart = [];
            }
            const params = new URLSearchParams(window.location.search);
            if (params.get('added') === '1') {
                this.addedToast = true;
                window.history.replaceState({}, '', window.location.pathname);
                setTimeout(() => { this.addedToast = false; }, 3000);
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
                    this.printReceipt(data.sale);
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

        printReceipt(sale) {
            const html = this.buildReceiptHtml(sale);
            const win = window.open('', '_blank', 'width=380,height=640');
            if (!win) {
                this.submitError = 'No se pudo abrir la ventana de impresión. Revise el bloqueador de ventanas emergentes.';
                return;
            }
            win.document.open();
            win.document.write(html);
            win.document.close();
        },

        buildReceiptHtml(sale) {
            const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
            })[c]);
            const money = (n) => `${this.currency}${Number(n).toFixed(2)}`;
            const date = new Date(sale.created_at);
            const dateStr = date.toLocaleString('es-MX', {
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit',
            });

            const methodLabels = { cash: 'Efectivo', card: 'Tarjeta', other: 'Otro' };
            const methodLabel = methodLabels[sale.payment_method] || sale.payment_method;

            const itemsRows = sale.items.map((i) => `
                <tr>
                    <td class="name">${esc(i.product_name)}</td>
                    <td class="qty">${Number(i.quantity)}</td>
                    <td class="num">${money(i.unit_price)}</td>
                    <td class="num">${money(i.subtotal)}</td>
                </tr>
            `).join('');

            const optionalBlock = `
                ${sale.customer_name ? `<p class="meta">Cliente: ${esc(sale.customer_name)}</p>` : ''}
                ${sale.note ? `<p class="meta">Nota: ${esc(sale.note)}</p>` : ''}
            `;

            const headerText = this.business.receipt_header
                ? `<p class="hdr-text">${esc(this.business.receipt_header)}</p>` : '';
            const footerText = this.business.receipt_footer
                ? `<p class="ftr-text">${esc(this.business.receipt_footer)}</p>` : '';
            const address = this.business.address
                ? `<p class="biz-contact">${esc(this.business.address)}</p>` : '';
            const phone = this.business.phone
                ? `<p class="biz-contact">Tel: ${esc(this.business.phone)}</p>` : '';

            return `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ticket #${sale.id}</title>
<style>
  @page { size: 80mm auto; margin: 4mm; }
  * { box-sizing: border-box; }
  body { font-family: 'Courier New', monospace; font-size: 12px; color: #000; margin: 0; padding: 8px; }
  .center { text-align: center; }
  .biz-name { font-size: 16px; font-weight: bold; margin: 0 0 4px; }
  .biz-contact { margin: 0; font-size: 11px; }
  .hdr-text, .ftr-text { margin: 6px 0; font-size: 11px; white-space: pre-line; }
  .divider { border: 0; border-top: 1px dashed #000; margin: 8px 0; }
  .meta { margin: 2px 0; font-size: 11px; }
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th, td { padding: 2px 0; vertical-align: top; }
  th { border-bottom: 1px solid #000; text-align: left; }
  .num { text-align: right; white-space: nowrap; }
  .qty { text-align: center; width: 30px; }
  .name { word-break: break-word; }
  .totals { width: 100%; font-size: 12px; }
  .totals td { padding: 2px 0; }
  .totals .label { text-align: left; }
  .totals .val { text-align: right; white-space: nowrap; }
  .total-row td { font-weight: bold; font-size: 14px; border-top: 1px solid #000; padding-top: 4px; }
  .thanks { text-align: center; margin-top: 10px; font-size: 11px; }
  @media print { body { padding: 0; } }
</style>
</head>
<body>
  <div class="center">
    <p class="biz-name">${esc(this.business.name || 'Shoppy')}</p>
    ${address}
    ${phone}
    ${headerText}
  </div>

  <hr class="divider">

  <p class="meta">Ticket: #${sale.id}</p>
  <p class="meta">Fecha: ${esc(dateStr)}</p>
  <p class="meta">Pago: ${esc(methodLabel)}</p>
  ${optionalBlock}

  <hr class="divider">

  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th class="qty">Cant</th>
        <th class="num">P.U.</th>
        <th class="num">Subt.</th>
      </tr>
    </thead>
    <tbody>${itemsRows}</tbody>
  </table>

  <hr class="divider">

  <table class="totals">
    <tr><td class="label">Subtotal</td><td class="val">${money(sale.subtotal)}</td></tr>
    ${Number(sale.discount_amount) > 0
              ? `<tr><td class="label">Descuento</td><td class="val">-${money(sale.discount_amount)}</td></tr>`
              : ''}
    <tr class="total-row"><td class="label">Total</td><td class="val">${money(sale.total)}</td></tr>
    <tr><td class="label">Recibido</td><td class="val">${money(sale.amount_tendered)}</td></tr>
    <tr><td class="label">Cambio</td><td class="val">${money(sale.change_given)}</td></tr>
  </table>

  <hr class="divider">

  <div class="center">
    ${footerText}
    <p class="thanks">¡Gracias por su compra!</p>
  </div>

  <script>
    window.addEventListener('load', function () {
      window.focus();
      window.print();
    });
    window.addEventListener('afterprint', function () {
      window.close();
    });
  <\/script>
</body>
</html>`;
        },
    };
}
