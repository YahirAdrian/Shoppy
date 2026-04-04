<x-layouts.admin title="Inventario — Shoppy Adminer">

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="mb-4 flex items-center justify-between rounded-lg bg-green-100 px-4 py-3 text-sm text-green-800">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show"
             class="mb-4 flex items-center justify-between rounded-lg bg-red-100 px-4 py-3 text-sm text-red-800">
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="text-red-600 hover:text-red-800">&times;</button>
        </div>
    @endif

    {{-- Page header --}}
    <h1 class="text-2xl font-bold text-stone-800 uppercase">Inventario</h1>
    <p class="mt-1 text-stone-500">Gestión de productos y categorías</p>

    {{-- Main container with Alpine state --}}
    <div x-data="{
        tab: new URLSearchParams(window.location.search).get('tab') || 'products',
        layout: localStorage.getItem('inventory_layout') || 'grid',
        setLayout(value) {
            this.layout = value;
            localStorage.setItem('inventory_layout', value);
        },

        {{-- Product form modal --}}
        showProductModal: false,
        productEditMode: false,
        productForm: { id: null, name: '', sku: '', barcode: '', category_id: '', description: '', cost_price: '', selling_price: '', stock: '', low_stock_alert: '', unit: 'pcs', is_active: true, image: '', remove_image: false, image_preview: null },
        openCreateProduct() {
            this.productEditMode = false;
            this.productForm = { id: null, name: '', sku: '', barcode: '', category_id: '', description: '', cost_price: '', selling_price: '', stock: '', low_stock_alert: '', unit: 'pcs', is_active: true, image: '', remove_image: false };
            this.showProductModal = true;
        },
        openEditProduct(product) {
            this.productEditMode = true;
            this.productForm = { ...product, remove_image: false, image_preview: null };
            this.showProductModal = true;
        },

        {{-- Category form modal --}}
        showCategoryModal: false,
        categoryEditMode: false,
        categoryForm: { id: null, name: '', description: '', is_active: true },
        openCreateCategory() {
            this.categoryEditMode = false;
            this.categoryForm = { id: null, name: '', description: '', is_active: true };
            this.showCategoryModal = true;
        },
        openEditCategory(category) {
            this.categoryEditMode = true;
            this.categoryForm = { ...category };
            this.showCategoryModal = true;
        },

        {{-- Stock adjustment modal --}}
        showStockModal: false,
        stockProduct: { id: null, name: '', stock: 0 },
        stockForm: { quantity: '', note: '' },
        openStockAdjustment(product) {
            this.stockProduct = product;
            this.stockForm = { quantity: '', note: '' };
            this.showStockModal = true;
        },

        {{-- Delete confirmation --}}
        showDeleteModal: false,
        deleteTarget: { type: '', id: null, name: '' },
        openDelete(type, id, name) {
            this.deleteTarget = { type, id, name };
            this.showDeleteModal = true;
        }
    }" class="mt-6">

        {{-- Tab bar --}}
        <div class="flex items-center gap-2 border-b border-stone-200 pb-0">
            <button @click="tab = 'products'"
                    :class="tab === 'products' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent text-stone-500 hover:text-stone-700'"
                    class="border-b-2 px-4 py-2 text-sm transition">
                Productos
            </button>
            <button @click="tab = 'categories'"
                    :class="tab === 'categories' ? 'border-primary-600 text-primary-600 font-semibold' : 'border-transparent text-stone-500 hover:text-stone-700'"
                    class="border-b-2 px-4 py-2 text-sm transition">
                Categorías
            </button>
            </button>
        </div>

        {{-- Products tab --}}
        <div x-show="tab === 'products'" x-cloak>
            {{-- Toolbar: layout toggle + add product --}}
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center gap-1 rounded-lg bg-stone-200 p-1">
                    <button @click="setLayout('grid')"
                            :class="layout === 'grid' ? 'bg-white shadow text-primary-600' : 'text-stone-500 hover:text-stone-700'"
                            class="rounded-md p-1.5 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    </button>
                    <button @click="setLayout('table')"
                            :class="layout === 'table' ? 'bg-white shadow text-primary-600' : 'text-stone-500 hover:text-stone-700'"
                            class="rounded-md p-1.5 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                    </button>
                </div>
                <button @click="openCreateProduct()" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-primary-700 transition">
                    + Agregar producto
                </button>
            </div>

            {{-- Grid view --}}
            <div x-show="layout === 'grid'" x-cloak>
                @include('admin.inventory.products-grid')
            </div>

            {{-- Table view --}}
            <div x-show="layout === 'table'" x-cloak>
                @include('admin.inventory.products-table')
            </div>
        </div>

        {{-- Categories tab --}}
        <div x-show="tab === 'categories'" x-cloak>
            <div class="mt-4 flex items-center justify-end">
                <button @click="openCreateCategory()" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-primary-700 transition">
                    + Agregar categoría
                </button>
            </div>
            @include('admin.inventory.categories')
        </div>

        {{-- Modals --}}
        @include('admin.inventory.modals.product-form-modal')
        @include('admin.inventory.modals.category-form-modal')
        @include('admin.inventory.modals.stock-adjustment-modal')
        @include('admin.inventory.modals.delete-confirmation-modal')
        

    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <script>
            document.addEventListener('alpine:init', () => {
                // Re-open the product modal if there were validation errors
                @if(old('sku') || old('selling_price'))
                    Alpine.nextTick(() => {
                        const el = document.querySelector('[x-data]');
                        if (el && el.__x) el.__x.$data.showProductModal = true;
                    });
                @endif
            });
        </script>
    @endif

</x-layouts.admin>
