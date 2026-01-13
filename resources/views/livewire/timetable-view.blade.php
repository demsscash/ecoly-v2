<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Class Timetable') }}</h1>
        <p class="text-base-content/60">{{ __('View weekly schedule') }}</p>
    </div>

    {{-- Class Selector --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="form-control max-w-md">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('Select Class') }}</span>
                </label>
                <select wire:model.live="selectedClassId" class="select select-bordered">
                    <option value="">{{ __('Select a class') }}</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if($selectedClassId)
        {{-- Weekly Schedule Grid --}}
        <div class="card bg-base-100 shadow overflow-x-auto">
            <div class="card-body p-0">
                <table class="table table-zebra">
                    <thead class="bg-base-200">
                        <tr>
                            <th class="sticky left-0 bg-base-200 z-10 w-32">{{ __('Time') }}</th>
                            @foreach($days as $dayKey => $dayLabel)
                                <th class="text-center min-w-[150px]">{{ $dayLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slot)
                            <tr class="{{ $slot->is_break ? 'bg-base-200/50' : '' }}">
                                {{-- Time Column --}}
                                <td class="sticky left-0 bg-base-100 z-10 font-semibold">
                                    <div class="text-sm">{{ $slot->name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $slot->time_range }}</div>
                                </td>
                                
                                {{-- Days Columns --}}
                                @foreach($days as $dayKey => $dayLabel)
                                    <td class="p-2">
                                        @if(isset($schedule[$slot->id][$dayKey]))
                                            @php $entry = $schedule[$slot->id][$dayKey]; @endphp
                                            <div class="card bg-primary/10 border border-primary/20 p-3">
                                                <div class="font-semibold text-sm text-primary mb-1">
                                                    {{ $entry->subject->name_fr }}
                                                </div>
                                                @if($entry->teacher)
                                                    <div class="text-xs text-base-content/70 flex items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                                        </svg>
                                                        {{ $entry->teacher->first_name }} {{ $entry->teacher->last_name }}
                                                    </div>
                                                @endif
                                                @if($entry->room)
                                                    <div class="text-xs text-base-content/60 flex items-center gap-1 mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                        </svg>
                                                        {{ $entry->room }}
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($slot->is_break)
                                            <div class="text-center text-xs text-base-content/40 italic">
                                                {{ $slot->is_break ? __('Break') : '' }}
                                            </div>
                                        @else
                                            <div class="text-center text-base-content/20">-</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Legend --}}
        <div class="card bg-base-100 shadow mt-4">
            <div class="card-body">
                <div class="flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-primary/10 border border-primary/20 rounded"></div>
                        <span>{{ __('Course') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-base-200/50 rounded"></div>
                        <span>{{ __('Break') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 mx-auto text-base-content/30 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <h3 class="text-xl font-bold mb-2">{{ __('Select a class') }}</h3>
                <p class="text-base-content/60">{{ __('Choose a class from the dropdown above to view its timetable.') }}</p>
            </div>
        </div>
    @endif
</div>
