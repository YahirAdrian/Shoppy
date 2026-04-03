<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @forelse($categories as $category)
        <div class="relative rounded-xl border border-stone-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            {{-- Kebab menu --}}
            <div x-data="{ open: false }" class="absolute top-3 right-3">
                <button @click="open = !open" class="rounded-full p-1 text-stone-400 hover:text-stone-700 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute right-0 mt-1 w-36 rounded-lg bg-white py-1 shadow-lg ring-1 ring-stone-200 z-10">
                    <button @click="open = false; openEditCategory({
                        id: {{ $category->id }},
                        name: '{{ addslashes($category->name) }}',
                        description: `{{ addslashes($category->description) }}`,
                        is_active: {{ $category->is_active ? 'true' : 'false' }}
                    })" class="flex w-full items-center gap-2 px-3 py-2 text-sm text-stone-700 hover:bg-stone-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                        Editar
                    </button>
                    <button @click="open = false; openDelete('category', {{ $category->id }}, '{{ addslashes($category->name) }}')"
                            class="flex w-full items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        Eliminar
                    </button>
                </div>
            </div>

            {{-- Category info --}}
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100">
                    <svg class="h-5 w-5 text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path d="M6 6h.008v.008H6V6Z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="font-semibold text-stone-800">{{ $category->name }}</h4>
                    @if($category->description)
                        <p class="mt-1 text-sm text-stone-500 line-clamp-2">{{ $category->description }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <span class="rounded-full bg-accent-100 px-2.5 py-0.5 text-xs font-medium text-accent-700">
                    {{ $category->products_count }} {{ $category->products_count === 1 ? 'producto' : 'productos' }}
                </span>
                @if(!$category->is_active)
                    <span class="rounded-full bg-stone-200 px-2 py-0.5 text-xs text-stone-500">Inactiva</span>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full py-8 text-center text-stone-400">
            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path d="M6 6h.008v.008H6V6Z"/></svg>
            <p class="mt-2 text-sm">No hay categorías registradas.</p>
        </div>
    @endforelse
</div>
