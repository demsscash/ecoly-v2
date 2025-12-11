<div x-data="{ showModal: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Class Subjects') }}</h1>
            <p class="text-base-content/60">{{ __('Assign subjects to classes with specific coefficients') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="text-sm font-medium">{{ __('School Year') }}:</label>
                    <select 
                        wire:model.live="selectedYearId"
                        class="select select-bordered select-sm w-48 ml-2"
                    >
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}">
                                {{ $year->name }}
                                @if ($year->is_active) ({{ __('Active') }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="text-sm font-medium">{{ __('Class') }}:</label>
                    <select 
                        wire:model.live="selectedClassId"
                        class="select select-bordered select-sm w-48 ml-2"
                    >
                        <option value="">{{ __('Select a class') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($selectedClassId)
        {{-- Add Subject Button --}}
        <div class="flex justify-end mb-4">
            <button 
                @click="showModal = true; $wire.openAddModal()"
                class="btn btn-primary btn-sm"
                @if($availableSubjects->isEmpty()) disabled @endif
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('Add Subject') }}
            </button>
        </div>

        {{-- Subjects Table --}}
        <div class="card bg-base-100 shadow">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Default Coefficient') }}</th>
                            <th>{{ __('Class Coefficient') }}</th>
                            <th>{{ __('Teacher') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classSubjects as $subject)
                            <tr>
                                <td class="font-mono font-bold">{{ $subject->code }}</td>
                                <td>
                                    <div>{{ $subject->name_fr }}</div>
                                    <div class="text-sm text-base-content/60" dir="rtl">{{ $subject->name_ar }}</div>
                                </td>
                                <td class="text-base-content/60">{{ $subject->coefficient }}</td>
                                <td>
                                    <input 
                                        type="number"
                                        value="{{ $subject->pivot->coefficient ?? '' }}"
                                        placeholder="{{ $subject->coefficient }}"
                                        min="0.5"
                                        max="10"
                                        step="0.5"
                                        class="input input-bordered input-sm w-20"
                                        wire:change="updateCoefficient({{ $subject->id }}, $event.target.value)"
                                    />
                                </td>
                                <td>
                                    <select 
                                        class="select select-bordered select-sm w-40"
                                        wire:change="updateTeacher({{ $subject->id }}, $event.target.value)"
                                    >
                                        <option value="">{{ __('No teacher') }}</option>
                                        @foreach ($teachers as $teacher)
                                            <option 
                                                value="{{ $teacher->id }}"
                                                @selected($subject->pivot->teacher_id == $teacher->id)
                                            >
                                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button 
                                        wire:click="removeSubject({{ $subject->id }})"
                                        wire:confirm="{{ __('Remove this subject from class?') }}"
                                        class="btn btn-ghost btn-xs text-error"
                                        title="{{ __('Remove') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-base-content/60">
                                    {{ __('No subjects assigned to this class.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-12 text-base-content/60">
                {{ __('Please select a class to manage its subjects.') }}
            </div>
        </div>
    @endif

    {{-- Add Subject Modal --}}
    <div 
        x-show="showModal" 
        x-cloak
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; overflow-y: auto;"
    >
        {{-- Backdrop --}}
        <div 
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5);" 
            @click="showModal = false"
        ></div>
        
        {{-- Modal Content --}}
        <div style="display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 1rem;">
            <div 
                style="position: relative; background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 100%; max-width: 28rem; padding: 1.5rem;"
                class="bg-base-100"
                @click.stop
            >
                {{-- Close button --}}
                <button 
                    @click="showModal = false" 
                    style="position: absolute; right: 0.5rem; top: 0.5rem;"
                    class="btn btn-sm btn-circle btn-ghost"
                >✕</button>
                
                <h3 class="font-bold text-lg mb-4">{{ __('Add Subject to Class') }}</h3>
                
                <form wire:submit="addSubject" class="space-y-4">
                    {{-- Subject --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Subject') }} <span class="text-error">*</span>
                        </label>
                        <select 
                            wire:model="subjectId"
                            class="select select-bordered w-full"
                        >
                            <option value="">{{ __('Select a subject') }}</option>
                            @foreach ($availableSubjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->code }} - {{ $subject->name_fr }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Coefficient Override --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Coefficient') }} ({{ __('optional override') }})
                        </label>
                        <input 
                            type="number" 
                            wire:model="coefficient"
                            min="0.5"
                            max="10"
                            step="0.5"
                            placeholder="{{ __('Leave empty for default') }}"
                            class="input input-bordered w-full" 
                        />
                    </div>

                    {{-- Teacher --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Teacher') }} ({{ __('optional') }})
                        </label>
                        <select 
                            wire:model="teacherId"
                            class="select select-bordered w-full"
                        >
                            <option value="">{{ __('No teacher') }}</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="showModal = false" class="btn">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" @click="showModal = false">
                            <span wire:loading.remove>{{ __('Add') }}</span>
                            <span wire:loading class="loading loading-spinner loading-sm"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
