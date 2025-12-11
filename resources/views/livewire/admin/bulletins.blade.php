<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Bulletins') }}</h1>
            <p class="text-base-content/60">{{ __('Generate student report cards') }}</p>
        </div>
        @if(count($selectedStudents) > 0)
            <button wire:click="generateSelectedBulletins" wire:loading.attr="disabled" class="btn btn-primary">
                <span wire:loading wire:target="generateSelectedBulletins" class="loading loading-spinner loading-sm"></span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                {{ __('Generate') }} ({{ count($selectedStudents) }})
            </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label py-1"><span class="label-text">{{ __('Class') }}</span></label>
                    <select wire:model.live="selectedClassId" class="select select-bordered w-full">
                        <option value="">{{ __('Select a class') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label py-1"><span class="label-text">{{ __('Trimester') }}</span></label>
                    <select wire:model.live="selectedTrimesterId" class="select select-bordered w-full">
                        @foreach($trimesters as $trimester)
                            <option value="{{ $trimester->id }}">{{ $trimester->name_fr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($selectedClassId && $selectedTrimesterId)
        {{-- Students List --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <label>
                                        <input type="checkbox" wire:model.live="selectAll" class="checkbox checkbox-sm" />
                                    </label>
                                </th>
                                <th>{{ __('Student') }}</th>
                                <th>{{ __('Matricule') }}</th>
                                <th class="text-center">{{ __('Grades') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php $hasGrades = $this->hasGrades($student->id); @endphp
                                <tr class="{{ !$hasGrades ? 'opacity-50' : '' }}">
                                    <td>
                                        <label>
                                            <input type="checkbox" 
                                                wire:model.live="selectedStudents" 
                                                value="{{ $student->id }}" 
                                                class="checkbox checkbox-sm"
                                                @if(!$hasGrades) disabled @endif />
                                        </label>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar">
                                                <div class="mask mask-squircle w-10 h-10">
                                                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $student->full_name }}</div>
                                                @if($student->first_name_ar)
                                                    <div class="text-xs text-base-content/60" dir="rtl">{{ $student->full_name_ar }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="font-mono text-sm">{{ $student->matricule }}</span></td>
                                    <td class="text-center">
                                        @if($hasGrades)
                                            <span class="badge badge-success badge-sm">{{ __('Available') }}</span>
                                        @else
                                            <span class="badge badge-warning badge-sm">{{ __('No grades') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hasGrades)
                                            <button wire:click="generateBulletin({{ $student->id }})" wire:loading.attr="disabled" wire:target="generateBulletin({{ $student->id }})" class="btn btn-ghost btn-sm">
                                                <span wire:loading wire:target="generateBulletin({{ $student->id }})" class="loading loading-spinner loading-xs"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                                PDF
                                            </button>
                                        @else
                                            <span class="text-xs text-base-content/40">{{ __('Enter grades first') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-base-content/60">
                                        {{ __('No students in this class.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ __('Select a class and trimester to view students.') }}</span>
        </div>
    @endif
</div>
