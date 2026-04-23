@extends('admin.layouts.app')

@section('title', __('Customer Ratings and Reviews'))

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Customer Reviews') }}</h2>
    <p class="text-sm font-medium text-gray-500">{{ __('Monitor service quality through user ratings for agents and orders') }}</p>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl font-bold flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-xl"></i>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($ratings as $rating)
    <div class="card-custom flex flex-col h-full relative overflow-hidden !rounded-3xl">
        
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600">
                    {{ mb_substr($rating->user->name ?? __('c'), 0, 1) }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800 dark:text-white">{{ $rating->user->name ?? __('Customer') }}</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $rating->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                @for($i=1; $i<=5; $i++)
                    <i class="fa-solid fa-star text-[10px] {{ $i <= $rating->stars ? 'text-amber-400' : 'text-gray-200' }}"></i>
                @endfor
            </div>
        </div>

        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 leading-relaxed italic bg-gray-50/50 dark:bg-white/5 p-4 rounded-2xl border border-gray-50 dark:border-white/5">
                "{{ $rating->comment ?: __('No text comment') }}"
            </p>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-50 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-gray-400">{{ __('For Order') }}:</span>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 rounded" dir="ltr">#{{ $rating->order_id }}</span>
            </div>
            
            <form action="{{ route('admin.ratings.destroy', $rating->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this rating?') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-400 hover:text-red-600 transition text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-trash-can"></i> {{ __('Delete') }}
                </button>
            </form>
        </div>

    </div>
    @empty
    <div class="col-span-full py-20 text-center bg-white dark:bg-[#161b22] border border-dashed border-gray-200 dark:border-white/10 rounded-3xl">
        <i class="fa-solid fa-star-half-stroke text-4xl text-gray-200 mb-4"></i>
        <h3 class="text-lg font-bold text-gray-500">{{ __('No ratings yet') }}</h3>
        <p class="text-sm text-gray-400 mt-1">{{ __('Once customers leave ratings, they will appear here') }}</p>
    </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $ratings->links() }}
</div>

@endsection
