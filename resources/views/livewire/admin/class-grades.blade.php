<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Class Grades') }}</h1>
            <p class="text-base-content/60">{{ __('View grades and rankings by class') }}</p>
        </div>
        <div class="flex gap-2">
            @if($selectedClassId && $selectedTrimesterId && auth()->user()->isAdmin())
                <button wire:click="validateAllGrades" wire:confirm="{{ __('Validate all grades for this class?') }}" class="btn btn-success btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    {{ __('Validate All') }}
                </button>
            @endif
            @if($selectedClassId && $selectedTrimesterId)
                <button wire:click="exportExcel" class="btn btn-outline btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    {{ __('Export Excel') }}
                </button>
            @endif
        </div>
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
        {{-- Statistics --}}
        <div class="flex flex-row w-full mb-6 bg-base-100 shadow rounded-box overflow-hidden">
            <div class="stat flex-1 border-r border-base-200">
                <div class="stat-title">{{ __('Students') }}</div>
                <div class="stat-value text-primary">{{ $statistics['count'] }}</div>
                <div class="stat-desc">{{ $statistics['graded'] }} {{ __('graded') }}</div>
            </div>
            <div class="stat flex-1 border-r border-base-200">
                <div class="stat-title">{{ __('Class Average') }}</div>
                <div class="stat-value {{ $statistics['average'] >= 10 ? 'text-success' : 'text-error' }}">
                    {{ $statistics['average'] ?? '-' }}
                </div>
                <div class="stat-desc">/ {{ $selectedClass?->grade_base ?? 20 }}</div>
            </div>
            <div class="stat flex-1 border-r border-base-200">
                <div class="stat-title">{{ __('Min / Max') }}</div>
                <div class="stat-value text-lg">
                    <span class="text-error">{{ $statistics['min'] ?? '-' }}</span>
                    <span class="text-base-content/50">/</span>
                    <span class="text-success">{{ $statistics['max'] ?? '-' }}</span>
                </div>
            </div>
            <div class="stat flex-1">
                <div class="stat-title">{{ __('Pass Rate') }}</div>
                <div class="stat-value text-success">{{ $statistics['pass_rate'] }}%</div>
                <div class="stat-desc">{{ $statistics['passed'] }} {{ __('passed') }} / {{ $statistics['failed'] }} {{ __('failed') }}</div>
            </div>
        </div>

        {{-- Grades Table --}}
        <div class="card bg-base-100 shadow">
            <div class="overflow-x-auto">
                <table class="table table-xs">
                    <thead>
                        <tr class="bg-base-200">
                            <th class="sticky left-0 bg-base-200 z-10">{{ __('Rank') }}</th>
                            <th class="sticky left-12 bg-base-200 z-10">{{ __('Student') }}</th>
                            @foreach($subjects as $subject)
                                <th class="text-center">
                                    <div class="tooltip" data-tip="{{ $subject->name_fr }}">
                                        {{ $subject->code }}
                                    </div>
                                    <div class="text-xs font-normal text-base-content/60">
                                        ({{ $subjectStats[$subject->id]['average'] ?? '-' }})
                                    </div>
                                </th>
                            @endforeach
                            <th class="text-center bg-primary/10">{{ __('Average') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankings as $item)
                            <tr class="hover">
                                <td class="sticky left-0 bg-base-100 z-10 font-bold text-center">
                                    @if($item['rank'] === 1)
                                        <span class="text-warning">🥇</span>
                                    @elseif($item['rank'] === 2)
                                        <span class="text-base-content/60">🥈</span>
                                    @elseif($item['rank'] === 3)
                                        <span class="text-amber-600">🥉</span>
                                    @else
                                        {{ $item['rank'] }}
                                    @endif
                                </td>
                                <td class="sticky left-12 bg-base-100 z-10">
                                    <a href="{{ route('students.show', $item['student']) }}" class="hover:underline">
                                        {{ $item['student']->full_name }}
                                    </a>
                                </td>
                                @foreach($subjects as $subject)
                                    <td class="text-center font-mono">
                                        @if(isset($item['grades'][$subject->id]['average']) && $item['grades'][$subject->id]['average'] !== null)
                                            <span class="cursor-pointer {{ $item['grades'][$subject->id]['average'] >= 10 ? 'text-success' : 'text-error' }} {{ $item['grades'][$subject->id]['is_validated'] ? 'underline decoration-success' : '' }}"
                                                  wire:click="showGradeHistory({{ $item['grades'][$subject->id]['grade_id'] }})"
                                                  title="{{ __('Click to see history') }}">
                                                {{ number_format($item['grades'][$subject->id]['average'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-base-content/30">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center font-mono font-bold bg-primary/5">
                                    @if($item['average'] !== null)
                                        <span class="{{ $item['average'] >= 10 ? 'text-success' : 'text-error' }}">
                                            {{ number_format($item['average'], 2) }}
                                        </span>
                                    @else
                                        <span class="text-base-content/30">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item['all_validated'] === true)
                                        <span class="badge badge-success badge-sm">{{ __('Validated') }}</span>
                                    @elseif($item['all_validated'] === false)
                                        @if(auth()->user()->isAdmin())
                                            <button wire:click="validateStudentGrades({{ $item['student']->id }})" class="btn btn-ghost btn-xs text-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                                {{ __('Validate') }}
                                            </button>
                                        @else
                                            <span class="badge badge-warning badge-sm">{{ __('Pending') }}</span>
                                        @endif
                                    @else
                                        <span class="text-base-content/30">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($subjects) + 4 }}" class="text-center py-8 text-base-content/60">
                                    {{ __('No students in this class.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($rankings) > 0)
                        <tfoot>
                            <tr class="bg-base-200 font-bold">
                                <td colspan="2" class="sticky left-0 bg-base-200 z-10">{{ __('Class Statistics') }}</td>
                                @foreach($subjects as $subject)
                                    <td class="text-center text-xs">
                                        <div class="text-error">{{ $subjectStats[$subject->id]['min'] ?? '-' }}</div>
                                        <div class="text-success">{{ $subjectStats[$subject->id]['max'] ?? '-' }}</div>
                                    </td>
                                @endforeach
                                <td class="text-center bg-primary/10">
                                    <div class="text-error">{{ $statistics['min'] ?? '-' }}</div>
                                    <div class="text-success">{{ $statistics['max'] ?? '-' }}</div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @else
        <div class="alert">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ __('Select a class and trimester to view grades.') }}</span>
        </div>
    @endif

    {{-- History Modal --}}
    @if($showHistory && $gradeHistory)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">{{ __('Grade History') }}</h3>
            <div class="mb-4">
                <p><strong>{{ __('Student') }}:</strong> {{ $gradeHistory->student->full_name }}</p>
                <p><strong>{{ __('Subject') }}:</strong> {{ $gradeHistory->subject->name_fr }}</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Action') }}</th>
                            <th>{{ __('Control') }}</th>
                            <th>{{ __('Exam') }}</th>
                            <th>{{ __('Average') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gradeHistory->histories as $history)
                            <tr>
                                <td class="text-xs">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $history->user->name }}</td>
                                <td>
                                    <span class="badge badge-sm {{ $history->action === 'create' ? 'badge-success' : 'badge-info' }}">
                                        {{ __($history->action) }}
                                    </span>
                                </td>
                                <td class="font-mono text-xs">
                                    @if($history->action === 'update')
                                        {{ $history->old_control_grade ?? '-' }} → {{ $history->new_control_grade ?? '-' }}
                                    @else
                                        {{ $history->new_control_grade ?? '-' }}
                                    @endif
                                </td>
                                <td class="font-mono text-xs">
                                    @if($history->action === 'update')
                                        {{ $history->old_exam_grade ?? '-' }} → {{ $history->new_exam_grade ?? '-' }}
                                    @else
                                        {{ $history->new_exam_grade ?? '-' }}
                                    @endif
                                </td>
                                <td class="font-mono text-xs">
                                    @if($history->action === 'update')
                                        {{ $history->old_average ?? '-' }} → {{ $history->new_average ?? '-' }}
                                    @else
                                        {{ $history->new_average ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-base-content/60">{{ __('No history available.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="modal-action">
                <button wire:click="$set('showHistory', false)" class="btn btn-sm">{{ __('Close') }}</button>
            </div>
        </div>
        <div class="modal-backdrop" wire:click="$set('showHistory', false)"></div>
    </div>
    @endif
</div>
