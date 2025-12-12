<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Financial Reports') }}</h1>
            <p class="text-base-content/60">{{ __('View and export financial reports') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('School Year') }}</span>
                    </label>
                    <select wire:model.live="selectedYearId" class="select select-bordered select-sm">
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Class') }}</span>
                    </label>
                    <select wire:model.live="selectedClassId" class="select select-bordered select-sm">
                        <option value="">{{ __('All Classes') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($reportType === 'monthly')
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('Month') }}</span>
                        </label>
                        <select wire:model.live="selectedMonth" class="select select-bordered select-sm">
                            <option value="">{{ __('All Months') }}</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Report Type Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-base-300 pb-2">
        <button 
            wire:click="$set('reportType', 'unpaid')" 
            class="btn btn-sm {{ $reportType === 'unpaid' ? 'btn-primary' : 'btn-ghost' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
            {{ __('Unpaid') }}
        </button>
        <button 
            wire:click="$set('reportType', 'monthly')" 
            class="btn btn-sm {{ $reportType === 'monthly' ? 'btn-primary' : 'btn-ghost' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
            {{ __('Monthly') }}
        </button>
        <button 
            wire:click="$set('reportType', 'by_class')" 
            class="btn btn-sm {{ $reportType === 'by_class' ? 'btn-primary' : 'btn-ghost' }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
            {{ __('By Class') }}
        </button>
    </div>

    {{-- Unpaid Report --}}
    @if($reportType === 'unpaid')
        <div class="flex justify-end mb-4">
            <button wire:click="downloadUnpaidPdf" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                {{ __('Export PDF') }}
            </button>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Student') }}</th>
                            <th>{{ __('Class') }}</th>
                            <th class="text-right">{{ __('Total Due') }}</th>
                            <th class="text-right">{{ __('Paid') }}</th>
                            <th class="text-right">{{ __('Balance') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report as $item)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $item['student']->full_name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $item['student']->matricule }}</div>
                                </td>
                                <td>{{ $item['student']->class?->name ?? '-' }}</td>
                                <td class="text-right font-mono">{{ number_format($item['total_due'], 0) }}</td>
                                <td class="text-right font-mono text-success">{{ number_format($item['total_paid'], 0) }}</td>
                                <td class="text-right font-mono text-error font-bold">{{ number_format($item['balance'], 0) }}</td>
                                <td>
                                    <span class="badge badge-sm {{ $item['status'] === 'partial' ? 'badge-warning' : 'badge-error' }}">
                                        {{ __($item['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-base-content/60">
                                    {{ __('No unpaid balances found.') }}
                                </td>
                            </tr>
                        @endforelse
                        @if(count($report) > 0)
                            <tr class="font-bold bg-base-200">
                                <td colspan="2">{{ __('Total') }}</td>
                                <td class="text-right">{{ number_format(collect($report)->sum('total_due'), 0) }}</td>
                                <td class="text-right text-success">{{ number_format(collect($report)->sum('total_paid'), 0) }}</td>
                                <td class="text-right text-error">{{ number_format(collect($report)->sum('balance'), 0) }}</td>
                                <td></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Monthly Report --}}
    @if($reportType === 'monthly')
        <div class="space-y-4">
            @forelse($monthlyData as $month => $data)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="card-title">{{ $month !== 'unknown' ? \Carbon\Carbon::parse($month . '-01')->format('F Y') : 'Sans date' }}</h3>
                            <div class="stats shadow">
                                <div class="stat py-2 px-4">
                                    <div class="stat-title text-xs">{{ __('Payments') }}</div>
                                    <div class="stat-value text-lg">{{ $data['count'] }}</div>
                                </div>
                                <div class="stat py-2 px-4">
                                    <div class="stat-title text-xs">{{ __('Collected') }}</div>
                                    <div class="stat-value text-lg text-success">{{ number_format($data['total_collected'], 0) }}</div>
                                    <div class="stat-desc">MRU</div>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Student') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th class="text-right">{{ __('Amount') }}</th>
                                        <th>{{ __('Method') }}</th>
                                        <th>{{ __('Reference') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['payments'] as $payment)
                                        <tr>
                                            <td>{{ $payment->paid_date?->format('d/m/Y') ?? '-' }}</td>
                                            <td>
                                                <div class="text-sm">{{ $payment->student->full_name }}</div>
                                                <div class="text-xs text-base-content/60">{{ $payment->student->class?->name }}</div>
                                            </td>
                                            <td>{{ $payment->getTypeLabel() }}</td>
                                            <td class="text-right font-mono text-success">{{ number_format($payment->amount_paid, 0) }}</td>
                                            <td>{{ $payment->getMethodLabel() }}</td>
                                            <td class="text-xs">{{ $payment->reference }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card bg-base-100 shadow">
                    <div class="card-body text-center py-8 text-base-content/60">
                        {{ __('No payments found for the selected period.') }}
                    </div>
                </div>
            @endforelse
        </div>
    @endif

    {{-- By Class Report --}}
    @if($reportType === 'by_class')
        <div class="flex justify-end mb-4">
            <button wire:click="downloadClassReportPdf" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                {{ __('Export PDF') }}
            </button>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Class') }}</th>
                            <th class="text-center">{{ __('Students') }}</th>
                            <th class="text-right">{{ __('Total Due') }}</th>
                            <th class="text-right">{{ __('Collected') }}</th>
                            <th class="text-right">{{ __('Balance') }}</th>
                            <th class="text-center">{{ __('Paid') }}</th>
                            <th class="text-center">{{ __('Partial') }}</th>
                            <th class="text-center">{{ __('Pending') }}</th>
                            <th class="text-center">{{ __('Rate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classReport as $item)
                            <tr>
                                <td class="font-medium">{{ $item['class']->name }}</td>
                                <td class="text-center">{{ $item['students_count'] }}</td>
                                <td class="text-right font-mono">{{ number_format($item['total_due'], 0) }}</td>
                                <td class="text-right font-mono text-success">{{ number_format($item['total_paid'], 0) }}</td>
                                <td class="text-right font-mono {{ $item['balance'] > 0 ? 'text-error' : 'text-success' }}">
                                    {{ number_format($item['balance'], 0) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">{{ $item['paid_count'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning">{{ $item['partial_count'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-error">{{ $item['pending_count'] }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="radial-progress text-sm {{ $item['collection_rate'] >= 80 ? 'text-success' : ($item['collection_rate'] >= 50 ? 'text-warning' : 'text-error') }}" 
                                         style="--value:{{ $item['collection_rate'] }}; --size:3rem;">
                                        {{ number_format($item['collection_rate'], 0) }}%
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-base-content/60">
                                    {{ __('No data available.') }}
                                </td>
                            </tr>
                        @endforelse
                        @if(count($classReport) > 0)
                            <tr class="font-bold bg-base-200">
                                <td>{{ __('Total') }}</td>
                                <td class="text-center">{{ collect($classReport)->sum('students_count') }}</td>
                                <td class="text-right">{{ number_format(collect($classReport)->sum('total_due'), 0) }}</td>
                                <td class="text-right text-success">{{ number_format(collect($classReport)->sum('total_paid'), 0) }}</td>
                                <td class="text-right text-error">{{ number_format(collect($classReport)->sum('balance'), 0) }}</td>
                                <td class="text-center">{{ collect($classReport)->sum('paid_count') }}</td>
                                <td class="text-center">{{ collect($classReport)->sum('partial_count') }}</td>
                                <td class="text-center">{{ collect($classReport)->sum('pending_count') }}</td>
                                <td class="text-center">
                                    @php
                                        $totalDue = collect($classReport)->sum('total_due');
                                        $totalPaid = collect($classReport)->sum('total_paid');
                                        $overallRate = $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0;
                                    @endphp
                                    <div class="radial-progress text-sm {{ $overallRate >= 80 ? 'text-success' : ($overallRate >= 50 ? 'text-warning' : 'text-error') }}" 
                                         style="--value:{{ $overallRate }}; --size:3rem;">
                                        {{ number_format($overallRate, 0) }}%
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
