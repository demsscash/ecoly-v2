<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('students') }}" class="btn btn-ghost btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold">{{ __('Student Profile') }}</h1>
                <p class="text-base-content/60">{{ $student->matricule }}</p>
            </div>
        </div>
        <button wire:click="downloadAttestation" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            {{ __('Certificate of Enrollment') }}
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Photo & Basic Info --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body items-center text-center">
                    <div class="avatar mb-4">
                        <div class="w-32 h-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" />
                        </div>
                    </div>
                    <h2 class="card-title">{{ $student->full_name }}</h2>
                    @if ($student->first_name_ar)
                        <p class="text-base-content/60" dir="rtl">{{ $student->full_name_ar }}</p>
                    @endif
                    <div class="badge {{ $student->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                        {{ __($student->status) }}
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="w-full text-left space-y-2">
                        @if ($student->nni)
                            <div class="flex justify-between">
                                <span class="text-base-content/60">{{ __('NNI') }}</span>
                                <span class="font-mono">{{ $student->nni }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Class') }}</span>
                            <span class="font-medium">{{ $student->class?->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('School Year') }}</span>
                            <span>{{ $student->schoolYear?->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Personal Info --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">{{ __('Personal Information') }}</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Birth Date') }}</span>
                            <span>{{ $student->birth_date->format('d/m/Y') }}</span>
                        </div>
                        @if ($student->birth_place)
                            <div class="flex justify-between">
                                <span class="text-base-content/60">{{ __('Birth Place') }}</span>
                                <span>{{ $student->birth_place }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Gender') }}</span>
                            <span>{{ $student->gender === 'male' ? __('Male') : __('Female') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Nationality') }}</span>
                            <span>{{ $student->nationality }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Guardian Info --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">{{ __('Guardian Information') }}</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Name') }}</span>
                            <span>{{ $student->guardian_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Phone') }}</span>
                            <a href="tel:{{ $student->guardian_phone }}" class="link link-primary">{{ $student->guardian_phone }}</a>
                        </div>
                        @if ($student->guardian_email)
                            <div class="flex justify-between">
                                <span class="text-base-content/60">{{ __('Email') }}</span>
                                <a href="mailto:{{ $student->guardian_email }}" class="link link-primary text-xs">{{ $student->guardian_email }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Annual Summary --}}
            <div class="card bg-gradient-to-r from-primary/10 to-secondary/10 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">{{ __('Annual Summary') }}</h3>
                    <div class="flex flex-row w-full overflow-hidden">
                        @foreach($trimesterAverages as $trimId => $data)
                            <div class="flex-1 text-center p-3 {{ !$loop->last ? 'border-r border-base-300' : '' }}">
                                <div class="text-xs text-base-content/60 mb-1">{{ $data['name'] }}</div>
                                <div class="text-xl font-bold {{ $data['average'] !== null ? ($data['average'] >= 10 ? 'text-success' : 'text-error') : 'text-base-content/30' }}">
                                    {{ $data['average'] ?? '-' }}
                                </div>
                                @if($data['rank']['rank'])
                                    <div class="text-xs text-base-content/60">{{ $data['rank']['rank'] }}/{{ $data['rank']['total'] }}</div>
                                @endif
                            </div>
                        @endforeach
                        <div class="flex-1 text-center p-3 bg-primary/10 rounded-lg">
                            <div class="text-xs text-base-content/60 mb-1">{{ __('Annual') }}</div>
                            <div class="text-xl font-bold {{ $annualAverage !== null ? ($annualAverage >= 10 ? 'text-success' : 'text-error') : 'text-base-content/30' }}">
                                {{ $annualAverage ?? '-' }}
                            </div>
                            @if($annualRank['rank'])
                                <div class="text-xs text-base-content/60">{{ $annualRank['rank'] }}/{{ $annualRank['total'] }}</div>
                            @endif
                            @if($annualMention)
                                <div class="badge badge-sm mt-1 {{ $annualAverage >= 10 ? 'badge-success' : 'badge-warning' }}">{{ $annualMention }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Trimester Selector --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body py-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <h3 class="card-title text-base">{{ __('Grades') }}</h3>
                        <select wire:model.live="selectedTrimesterId" class="select select-bordered select-sm w-48">
                            @foreach ($trimesters as $trimester)
                                <option value="{{ $trimester->id }}">{{ $trimester->name_fr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Trimester Stats --}}
            @if($trimesterAverage !== null)
                <div class="flex flex-row w-full bg-base-100 shadow rounded-box overflow-hidden">
                    <div class="stat flex-1 border-r border-base-200">
                        <div class="stat-title">{{ __('Average') }}</div>
                        <div class="stat-value {{ $trimesterAverage >= 10 ? 'text-success' : 'text-error' }}">
                            {{ number_format($trimesterAverage, 2) }}
                        </div>
                        <div class="stat-desc">/ {{ $student->class?->grade_base ?? 20 }}</div>
                    </div>
                    <div class="stat flex-1 border-r border-base-200">
                        <div class="stat-title">{{ __('Rank') }}</div>
                        <div class="stat-value text-primary">
                            @if($rankInfo['rank'])
                                {{ $rankInfo['rank'] }}<span class="text-lg text-base-content/60">/{{ $rankInfo['total'] }}</span>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="stat flex-1">
                        <div class="stat-title">{{ __('Mention') }}</div>
                        <div class="stat-value text-lg {{ $trimesterAverage >= 10 ? 'text-success' : 'text-warning' }}">
                            {{ $mention ?? '-' }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Grades Table --}}
            <div class="card bg-base-100 shadow">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Subject') }}</th>
                                <th class="text-center">{{ __('Coef') }}</th>
                                <th class="text-center">{{ __('Control') }}</th>
                                <th class="text-center">{{ __('Exam') }}</th>
                                <th class="text-center">{{ __('Average') }}</th>
                                <th>{{ __('Appreciation') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjects as $subject)
                                @php
                                    $grade = $grades->get($subject->id);
                                    $coef = $subject->pivot->coefficient ?? $subject->coefficient;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="font-medium">{{ $subject->name_fr }}</div>
                                        <div class="text-xs text-base-content/60">{{ $subject->code }}</div>
                                    </td>
                                    <td class="text-center">{{ $coef }}</td>
                                    <td class="text-center font-mono">
                                        {{ $grade?->control_grade !== null ? number_format($grade->control_grade, 2) : '-' }}
                                    </td>
                                    <td class="text-center font-mono">
                                        {{ $grade?->exam_grade !== null ? number_format($grade->exam_grade, 2) : '-' }}
                                    </td>
                                    <td class="text-center font-mono font-bold">
                                        @if ($grade?->average !== null)
                                            <span class="{{ $grade->average >= 10 ? 'text-success' : 'text-error' }}">
                                                {{ number_format($grade->average, 2) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-sm">{{ $grade?->appreciation ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-base-content/60">
                                        {{ __('No subjects assigned to this class.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($trimesterAverage !== null)
                            <tfoot>
                                <tr class="bg-base-200">
                                    <td colspan="4" class="text-right font-bold">{{ __('Trimester Average') }}</td>
                                    <td class="text-center font-mono font-bold text-lg">
                                        <span class="{{ $trimesterAverage >= 10 ? 'text-success' : 'text-error' }}">
                                            {{ number_format($trimesterAverage, 2) }}
                                        </span>
                                    </td>
                                    <td class="font-bold">{{ $mention }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
