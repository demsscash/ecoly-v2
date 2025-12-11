<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Grading Configuration') }}</h1>
            <p class="text-base-content/60">{{ __('Configure grading weights and mention thresholds') }}</p>
        </div>
    </div>

    {{-- Year Filter --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="flex items-center gap-4">
                <label class="text-sm font-medium">{{ __('School Year') }}:</label>
                <select 
                    wire:model.live="selectedYearId"
                    class="select select-bordered select-sm w-48"
                >
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}">
                            {{ $year->name }}
                            @if ($year->is_active) ({{ __('Active') }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Pondération --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">{{ __('Grade Weighting') }}</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Control Weight') }} (%)
                            </label>
                            <input 
                                type="number"
                                wire:model.live="control_weight"
                                min="0"
                                max="100"
                                class="input input-bordered w-full"
                            />
                            @error('control_weight')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Exam Weight') }} (%)
                            </label>
                            <input 
                                type="number"
                                wire:model.live="exam_weight"
                                min="0"
                                max="100"
                                class="input input-bordered w-full"
                            />
                            @error('exam_weight')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Formula') }}: ({{ __('Control') }} × {{ $control_weight }}% + {{ __('Exam') }} × {{ $exam_weight }}%) = {{ __('Average') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Seuils Mentions --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">{{ __('Mention Thresholds') }} (/20)</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Excellent') }} (≥)
                            </label>
                            <input 
                                type="number"
                                wire:model="mention_excellent"
                                min="0"
                                max="20"
                                step="0.5"
                                class="input input-bordered w-full"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Very Good') }} (≥)
                            </label>
                            <input 
                                type="number"
                                wire:model="mention_very_good"
                                min="0"
                                max="20"
                                step="0.5"
                                class="input input-bordered w-full"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Good') }} (≥)
                            </label>
                            <input 
                                type="number"
                                wire:model="mention_good"
                                min="0"
                                max="20"
                                step="0.5"
                                class="input input-bordered w-full"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Fairly Good') }} (≥)
                            </label>
                            <input 
                                type="number"
                                wire:model="mention_fairly_good"
                                min="0"
                                max="20"
                                step="0.5"
                                class="input input-bordered w-full"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Seuil Passage --}}
            <div class="card bg-base-100 shadow lg:col-span-2">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">{{ __('Passing Threshold') }}</h2>
                    
                    <div class="max-w-xs">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Minimum average to pass') }} (/20)
                        </label>
                        <input 
                            type="number"
                            wire:model="passing_grade"
                            min="0"
                            max="20"
                            step="0.5"
                            class="input input-bordered w-full"
                        />
                        @error('passing_grade')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end mt-6">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Save Configuration') }}</span>
                <span wire:loading class="loading loading-spinner loading-sm"></span>
            </button>
        </div>
    </form>
</div>
