<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Subjects') }}</h1>
            <p class="text-base-content/60">{{ __('Manage school subjects') }}</p>
        </div>
        <button wire:click="create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            {{ __('Add Subject') }}
        </button>
    </div>

    {{-- Search --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search subjects...') }}" class="input input-bordered w-full sm:w-80" />
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Name (French)') }}</th>
                        <th>{{ __('Name (Arabic)') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td><span class="badge badge-ghost">{{ $subject->code }}</span></td>
                            <td>{{ $subject->name_fr }}</td>
                            <td dir="rtl">{{ $subject->name_ar ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $subject->is_active ? 'badge-success' : 'badge-warning' }} badge-sm">
                                    {{ $subject->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <button wire:click="edit({{ $subject->id }})" class="btn btn-ghost btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </button>
                                    <button wire:click="delete({{ $subject->id }})" wire:confirm="{{ __('Are you sure you want to delete this subject?') }}" class="btn btn-ghost btn-sm text-error">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-base-content/60">
                                {{ __('No subjects found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subjects->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">{{ $editingId ? __('Edit Subject') : __('New Subject') }}</h3>
            <form wire:submit="save">
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Code') }}</span></label>
                    <input type="text" wire:model="code" class="input input-bordered w-full" required />
                    @error('code') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Name (French)') }}</span></label>
                    <input type="text" wire:model="name_fr" class="input input-bordered w-full" required />
                    @error('name_fr') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Name (Arabic)') }}</span></label>
                    <input type="text" wire:model="name_ar" class="input input-bordered w-full" dir="rtl" />
                    @error('name_ar') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="form-control mb-4">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="checkbox" wire:model="is_active" class="checkbox checkbox-primary" />
                        <span class="label-text">{{ __('Active') }}</span>
                    </label>
                </div>
                <div class="modal-action">
                    <button type="button" wire:click="$set('showModal', false)" class="btn">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="$set('showModal', false)"></div>
    </div>
    @endif
</div>
