export default function posStartSession({ storeUrl, saleUrl }) {
    return {
        storeUrl,
        saleUrl,
        amount: '',
        loading: false,
        error: '',

        async submit() {
            const val = parseFloat(this.amount);
            if (isNaN(val) || val < 0) {
                this.error = 'Ingrese un monto válido (puede ser 0 si la caja está vacía).';
                return;
            }
            this.loading = true;
            this.error = '';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const res = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ opening_cash: val }),
                });

                if (res.ok) {
                    window.location.href = this.saleUrl;
                } else {
                    const data = await res.json();
                    this.error = data.message || 'Error al iniciar el turno.';
                    this.loading = false;
                }
            } catch {
                this.error = 'Error de red. Intente de nuevo.';
                this.loading = false;
            }
        },
    };
}
