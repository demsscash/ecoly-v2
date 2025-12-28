<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Series') }}</h1>
            <p class="text-base-content/60">{{ __('Manage lycee series (Littéraire, C, D)') }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New Serie') }}
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stat bg-base-100 rounded-box shadow">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
            </div>
            <div class="stat-title">{{ __('Total Series') }}</div>
            <div class="stat-value text-primary">{{ $series->total() }}</div>
            <div class="stat-desc">{{ __('Available series') }}</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow">
            <div class="stat-figure text-success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
            </div>
            <div class="stat-title">{{ __('Active Classes') }}</div>
            <div class="stat-value text-success">{{ $series->sum('classes_count') }}</div>
            <div class="stat-desc">{{ __('Using series') }}</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow">
            <div class="stat-figure text-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
            </div>
            <div class="stat-title">{{ __('Most Popular') }}</div>
            <div class="stat-value text-info text-lg">
                @php
                    $popular = $series->sortByDesc('classes_count')->first();
                @endphp
                {{ $popular ? $popular->name : '-' }}
            </div>
            <div class="stat-desc">
                {{ $popular ? $popular->classes_count . ' ' . __('classes') : __('N/A') }}
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">{{ __('Series List') }}</h2>
            
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th class="text-center">{{ __('Classes') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($series as $serie)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                                            <span class="text-white font-bold text-lg">{{ substr($serie->code, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold">{{ $serie->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary badge-lg">{{ $serie->code }}</span>
                                </td>
                                <td>
                                    <div class="max-w-md">
                                        {{ Str::limit($serie->description ?? '-', 60) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($serie->classes_count > 0)
                                        <span class="badge badge-success gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                            </svg>
                                            {{ $serie->classes_count }}
                                        </span>
                                    @else
                                        <span class="badge badge-ghost">0</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="openEditModal({{ $serie->id }})" 
                                            class="btn btn-ghost btn-sm tooltip" 
                                            data-tip="{{ __('Edit') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $serie->id }})" 
                                            wire:confirm="{{ __('Are you sure you want to delete this serie?') }}"
                                            class="btn btn-ghost btn-sm text-error tooltip"
                                            data-tip="{{ __('Delete') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-base-content/20">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                        </svg>
                                        <div class="text-center">
                                            <p class="text-lg font-semibold text-base-content/60">{{ __('No series found.') }}</p>
                                            <p class="text-sm text-base-content/40">{{ __('Get started by creating a new serie.') }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($series->hasPages())
                <div class="mt-4 pt-4 border-t border-base-200">
                    {{ $series->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-lg">
                <h3 class="font-bold text-xl mb-6">
                    {{ $editingSerieId ? __('Edit Serie') : __('New Serie') }}
                </h3>

                <form wire:submit="save">
                    {{-- Name --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">{{ __('Name') }} *</span>
                        </label>
                        <input type="text" wire:model="name" class="input input-bordered" 
                            placeholder="{{ __('Ex: Littéraire, Série C, Série D') }}" required />
                        @error('name')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Code --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">{{ __('Code') }} *</span>
                        </label>
                        <input type="text" wire:model="code" class="input input-bordered" 
                            placeholder="{{ __('Ex: LIT, C, D') }}" required maxlength="10" />
                        <label class="label">
                            <span class="label-text-alt">{{ __('Unique short code for the serie') }}</span>
                        </label>
                        @error('code')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">{{ __('Description') }}</span>
                        </label>
                        <textarea wire:model="description" class="textarea textarea-bordered h-24" 
                            placeholder="{{ __('Optional description') }}"></textarea>
                        @error('description')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="modal-action">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
