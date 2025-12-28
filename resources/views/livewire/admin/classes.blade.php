<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Classes') }}</h1>
            <p class="text-base-content/60">{{ __('Manage school classes') }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New Class') }}
        </button>
    </div>

    {{-- School Year Filter --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="flex items-center gap-4">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('School Year') }}</span>
                </label>
                <select wire:model.live="school_year_id" class="select select-bordered">
                    @foreach($schoolYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">{{ __('Classes List') }}</h2>

            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Serie') }}</th>
                            <th class="text-center">{{ __('Students') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td class="font-semibold">{{ $class->name }}</td>
                                <td>
                                    @if($class->level_type === 'fondamental')
                                        <span class="badge badge-info">{{ __('Fondamental') }}</span>
                                    @elseif($class->level_type === 'college')
                                        <span class="badge badge-success">{{ __('Collège') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ __('Lycée') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($class->serie)
                                        <span class="badge badge-primary">{{ $class->serie->name }}</span>
                                    @else
                                        <span class="text-base-content/40">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-ghost">{{ $class->students_count }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="openEditModal({{ $class->id }})" class="btn btn-ghost btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $class->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this class?') }}"
                                            class="btn btn-ghost btn-sm text-error">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                        </svg>
                                        <div class="text-center">
                                            <p class="text-lg font-semibold text-base-content/60">{{ __('No classes found.') }}</p>
                                            <p class="text-sm text-base-content/40">{{ __('Get started by creating a new class.') }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($classes->hasPages())
                <div class="mt-4 pt-4 border-t border-base-200">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-xl mb-6">
                    {{ $editingClassId ? __('Edit Class') : __('New Class') }}
                </h3>

                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Level Type --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Type') }} *</span>
                            </label>
                            <select wire:model.live="level_type" class="select select-bordered" required>
                                <option value="fondamental">{{ __('Fondamental') }}</option>
                                <option value="college">{{ __('Collège') }}</option>
                                <option value="lycee">{{ __('Lycée') }}</option>
                            </select>
                            @error('level_type')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Level --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Level') }} *</span>
                            </label>
                            <select wire:model.live="level" class="select select-bordered" required>
                                @if($level_type === 'fondamental')
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}">{{ $i }}{{ $i === 1 ? 'ère' : 'ème' }}</option>
                                    @endfor
                                @elseif($level_type === 'college')
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}">{{ $i }}{{ $i === 1 ? 'ère' : 'ème' }} Collège</option>
                                    @endfor
                                @else
                                    <option value="5">5ème</option>
                                    <option value="6">6ème</option>
                                    <option value="7">7ème</option>
                                @endif
                            </select>
                            @error('level')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Serie (only for lycee 5/6/7) --}}
                        @if($level_type === 'lycee' && in_array($level, [5, 6, 7]))
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ __('Serie') }} *</span>
                                </label>
                                <select wire:model="serie_id" class="select select-bordered" required>
                                    <option value="">{{ __('Select serie') }}</option>
                                    @foreach($series as $serie)
                                        <option value="{{ $serie->id }}">{{ $serie->name }}</option>
                                    @endforeach
                                </select>
                                @error('serie_id')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        {{-- Class Number --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    {{ __('Class Number') }} 
                                    @if($level_type === 'lycee' && in_array($level, [5, 6, 7])) * @endif
                                </span>
                            </label>
                            <input type="text" wire:model="class_number" class="input input-bordered"
                                placeholder="{{ __('Ex: 1, 2, 3') }}" 
                                {{ ($level_type === 'lycee' && in_array($level, [5, 6, 7])) ? 'required' : '' }} />
                            
                            @error('class_number')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- School Year --}}
                        <div class="form-control {{ ($level_type === 'lycee' && in_array($level, [5, 6, 7])) ? 'md:col-span-2' : '' }}">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('School Year') }} *</span>
                            </label>
                            <select wire:model="school_year_id" class="select select-bordered" required>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                            @error('school_year_id')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Info Alert --}}
                    <div class="alert alert-info mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm">{{ __('Class name will be generated automatically based on your selections.') }}</span>
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
