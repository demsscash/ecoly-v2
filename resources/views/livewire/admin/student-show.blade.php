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
        <div class="flex gap-2">
            <button wire:click="downloadAttestation" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                {{ __('Certificate') }}
            </button>
        </div>
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
            {{-- Tabs --}}
            <div class="flex gap-2 border-b border-base-300 pb-2">
                <button 
                    wire:click="setTab('grades')" 
                    class="btn btn-sm {{ $activeTab === 'grades' ? 'btn-primary' : 'btn-ghost' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
                    {{ __('Grades') }}
                </button>
                <button 
                    wire:click="setTab('finances')" 
                    class="btn btn-sm {{ $activeTab === 'finances' ? 'btn-primary' : 'btn-ghost' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                    {{ __('Finances') }}
                    @if($paymentsSummary['balance'] > 0)
                        <span class="badge badge-error badge-xs">!</span>
                    @endif
                </button>
            </div>

            {{-- Grades Tab --}}
            @if($activeTab === 'grades')
                {{-- Annual Summary --}}
                <div class="card bg-gradient-to-r from-primary/10 to-secondary/10 shadow">
                    <div class="card-body py-4">
                        <h3 class="card-title text-base mb-2">{{ __('Annual Summary') }}</h3>
                        <div class="flex flex-row w-full overflow-hidden rounded-lg bg-base-100">
                            @foreach($trimesterAverages as $trimId => $data)
                                <div class="flex-1 text-center p-3 {{ !$loop->last ? 'border-r border-base-300' : '' }}">
                                    <div class="text-xs text-base-content/60 mb-1">{{ $data['name'] }}</div>
                                    <div class="text-xl font-bold {{ $data['average'] !== null ? ($data['average'] >= 10 ? 'text-success' : 'text-error') : 'text-base-content/30' }}">
                                        {{ $data['average'] ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex-1 text-center p-3 bg-primary/10">
                                <div class="text-xs text-base-content/60 mb-1">{{ __('Annual') }}</div>
                                <div class="text-xl font-bold {{ $annualAverage !== null ? ($annualAverage >= 10 ? 'text-success' : 'text-error') : 'text-base-content/30' }}">
                                    {{ $annualAverage ?? '-' }}
                                </div>
                                @if($annualRank['rank'])
                                    <div class="text-xs text-base-content/60">{{ $annualRank['rank'] }}/{{ $annualRank['total'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trimester Selector --}}
                <div class="flex justify-between items-center">
                    <div class="form-control">
                        <select wire:model.live="selectedTrimesterId" class="select select-bordered select-sm w-48 text-base-content bg-base-100">
                            @if($trimesters->isEmpty())
                                <option value="" class="text-base-content">{{ __('No trimester') }}</option>
                            @else
                                @foreach($trimesters as $trimester)
                                    <option value="{{ $trimester->id }}" class="text-base-content bg-base-100">{{ $trimester->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    @if($mention)
                        <span class="badge badge-lg {{ $trimesterAverage >= 10 ? 'badge-success' : 'badge-warning' }}">{{ $mention }}</span>
                    @endif
                </div>

                {{-- Trimester Stats --}}
                @if($trimesterAverage !== null)
                    <div class="flex w-full bg-base-100 shadow rounded-box overflow-hidden">
                        <div class="flex-1 p-4 border-r border-base-200 text-center">
                            <div class="text-xs text-base-content/60">{{ __('Average') }}</div>
                            <div class="text-2xl font-bold {{ $trimesterAverage >= 10 ? 'text-success' : 'text-error' }}">
                                {{ number_format($trimesterAverage, 2) }}
                            </div>
                            <div class="text-xs text-base-content/60">/ {{ $student->class?->grade_base ?? 20 }}</div>
                        </div>
                        <div class="flex-1 p-4 border-r border-base-200 text-center">
                            <div class="text-xs text-base-content/60">{{ __('Rank') }}</div>
                            <div class="text-2xl font-bold text-primary">
                                @if($rankInfo['rank'])
                                    {{ $rankInfo['rank'] }}<span class="text-lg text-base-content/60">/{{ $rankInfo['total'] }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 p-4 text-center">
                            <div class="text-xs text-base-content/60">{{ __('Mention') }}</div>
                            <div class="text-lg font-bold {{ $trimesterAverage >= 10 ? 'text-success' : 'text-warning' }}">
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
                                        <td class="text-center font-mono font-bold {{ ($grade?->average ?? 0) >= 10 ? 'text-success' : 'text-error' }}">
                                            {{ $grade?->average !== null ? number_format($grade->average, 2) : '-' }}
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
                        </table>
                    </div>
                </div>
            @endif

            {{-- Finances Tab --}}
            @if($activeTab === 'finances')
                {{-- Financial Summary --}}
                <div class="flex w-full bg-base-100 shadow rounded-box overflow-hidden">
                    <div class="flex-1 p-4 border-r border-base-200 text-center">
                        <div class="text-xs text-base-content/60">{{ __('Total Due') }}</div>
                        <div class="text-xl font-bold text-primary">{{ number_format($paymentsSummary['total_due'], 0) }}</div>
                        <div class="text-xs text-base-content/60">MRU</div>
                    </div>
                    <div class="flex-1 p-4 border-r border-base-200 text-center">
                        <div class="text-xs text-base-content/60">{{ __('Total Paid') }}</div>
                        <div class="text-xl font-bold text-success">{{ number_format($paymentsSummary['total_paid'], 0) }}</div>
                        <div class="text-xs text-base-content/60">MRU</div>
                    </div>
                    <div class="flex-1 p-4 border-r border-base-200 text-center">
                        <div class="text-xs text-base-content/60">{{ __('Balance') }}</div>
                        <div class="text-xl font-bold {{ $paymentsSummary['balance'] > 0 ? 'text-error' : 'text-success' }}">{{ number_format($paymentsSummary['balance'], 0) }}</div>
                        <div class="text-xs text-base-content/60">MRU</div>
                    </div>
                    <div class="flex-1 p-4 text-center">
                        <div class="text-xs text-base-content/60">{{ __('Status') }}</div>
                        <div class="mt-1">
                            @if($paymentsSummary['status'] === 'paid')
                                <span class="badge badge-success">{{ __('Paid') }}</span>
                            @elseif($paymentsSummary['status'] === 'partial')
                                <span class="badge badge-warning">{{ __('Partial') }}</span>
                            @else
                                <span class="badge badge-error">{{ __('Pending') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Export Button --}}
                <div class="flex justify-end">
                    <button wire:click="downloadFinancialStatement" class="btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        {{ __('Export PDF') }}
                    </button>
                </div>

                {{-- Payments Table --}}
                <div class="card bg-base-100 shadow">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Type') }}</th>
                                    <th class="text-right">{{ __('Amount') }}</th>
                                    <th class="text-right">{{ __('Paid') }}</th>
                                    <th class="text-right">{{ __('Balance') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Reference') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td>
                                            <div class="font-medium">{{ $payment->getTypeLabel() }}</div>
                                            @if($payment->month)
                                                <div class="text-xs text-base-content/60">{{ $payment->getMonthLabel() }}</div>
                                            @endif
                                        </td>
                                        <td class="text-right font-mono">{{ number_format($payment->amount, 0) }}</td>
                                        <td class="text-right font-mono text-success">{{ number_format($payment->amount_paid, 0) }}</td>
                                        <td class="text-right font-mono {{ $payment->balance > 0 ? 'text-error' : 'text-success' }}">
                                            {{ number_format($payment->balance, 0) }}
                                        </td>
                                        <td>
                                            <span class="badge badge-sm {{ $payment->getStatusBadgeClass() }}">
                                                {{ __($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="text-xs text-base-content/60">
                                            {{ $payment->reference ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-base-content/60">
                                            {{ __('No payments found for this student.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
