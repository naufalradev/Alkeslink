{{--
  Component: item-card
  Props: $item (App\Models\Item)
--}}
@props(['item'])

<article class="bg-white rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1
                transition-all duration-200 overflow-hidden flex flex-col">

    {{-- Image --}}
    <a href="{{ route('catalog.show', $item) }}" class="block aspect-[4/3] overflow-hidden bg-slate-100">
        @if($item->primaryImage)
            <img src="{{ $item->primaryImage->image_url }}"
                 alt="{{ $item->title }}"
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-300">
                <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
        @endif
    </a>

    {{-- Body --}}
    <div class="p-4 flex flex-col flex-1 gap-2">
        <div class="flex items-start justify-between gap-2">
            <a href="{{ route('catalog.show', $item) }}"
               class="font-semibold text-slate-800 text-sm leading-snug hover:text-teal-600 transition line-clamp-2">
                {{ $item->title }}
            </a>
            @include('components.status-badge', ['status' => $item->status, 'label' => $item->statusLabel()])
        </div>

        <p class="text-xs text-slate-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $item->city }}, {{ $item->province }}
        </p>

        <div class="flex items-center gap-2 mt-1">
            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                {{ $item->condition === 'baik' ? 'bg-green-100 text-green-700' : ($item->condition === 'cukup' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                {{ $item->conditionLabel() }}
            </span>
            <span class="text-xs text-slate-400">{{ $item->category->name }}</span>
        </div>

        @if($item->estimated_value > 0)
            <p class="text-xs text-slate-500 mt-auto">
                Nilai estimasi: <strong class="text-teal-600">Rp {{ number_format($item->estimated_value, 0, ',', '.') }}</strong>
            </p>
        @endif

        @if($item->status === 'active')
            <a href="{{ route('catalog.show', $item) }}"
               class="mt-2 block w-full text-center py-2 rounded-xl bg-teal-600 hover:bg-teal-700
                      text-white text-xs font-semibold transition">
                Ajukan Permohonan
            </a>
        @endif
    </div>
</article>
