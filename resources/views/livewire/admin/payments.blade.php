<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Payments') }}</h1>
            <p class="text-base-content/60">{{ __('Manage student payments and fees') }}</p>
        </div>
        <div class="flex gap-2">
            @if(count($selectedPayments) > 0)
                <button wire:click="deleteSelected" wire:confirm="{{ __('Delete :count selected payment(s)?', ['count' => count($selectedPayments)]) }}" class="btn btn-error btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    {{ __('Delete') }} ({{ count($selectedPayments) }})
                </button>
            @endif
            <button wire:click="openInitModal" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('Initialize') }}
            </button>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="flex w-full mb-6 bg-base-100 shadow rounded-box overflow-hidden">
        <div class="flex-1 p-4 border-r border-base-200 text-center">
            <div class="text-xs text-base-content/60">{{ __('Total Due') }}</div>
            <div class="text-xl font-bold text-primary">{{ number_format($stats['total_due'], 0) }}</div>
            <div class="text-xs text-base-content/60">MRU</div>
        </div>
        <div class="flex-1 p-4 border-r border-base-200 text-center">
            <div class="text-xs text-base-content/60">{{ __('Total Paid') }}</div>
            <div class="text-xl font-bold text-success">{{ number_format($stats['total_paid'], 0) }}</div>
            <div class="text-xs text-base-content/60">MRU</div>
        </div>
        <div class="flex-1 p-4 border-r border-base-200 text-center">
            <div class="text-xs text-base-content/60">{{ __('Balance') }}</div>
            <div class="text-xl font-bold text-error">{{ number_format($stats['balance'], 0) }}</div>
            <div class="text-xs text-base-content/60">MRU</div>
        </div>
        <div class="flex-1 p-4 text-center">
            <div class="text-xs text-base-content/60">{{ __('Rate') }}</div>
            <div class="text-xl font-bold {{ $stats['collection_rate'] >= 50 ? 'text-success' : 'text-warning' }}">{{ $stats['collection_rate'] }}%</div>
            <div class="text-xs text-base-content/60">{{ $stats['paid_count'] }}/{{ $stats['paid_count'] + $stats['pending_count'] }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search...') }}" class="input input-bordered input-sm w-44" />
        <select wire:model.live="filterClass" class="select select-bordered select-sm">
            <option value="">{{ __('All classes') }}</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterType" class="select select-bordered select-sm">
            <option value="">{{ __('All types') }}</option>
            <option value="registration">{{ __('Registration') }}</option>
            <option value="tuition">{{ __('Tuition') }}</option>
        </select>
        <select wire:model.live="filterStatus" class="select select-bordered select-sm">
            <option value="">{{ __('All statuses') }}</option>
            <option value="pending">{{ __('Pending') }}</option>
            <option value="partial">{{ __('Partial') }}</option>
            <option value="paid">{{ __('Paid') }}</option>
        </select>
    </div>

    {{-- Payments Table --}}
    <div class="card bg-base-100 shadow">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th class="w-8">
                            <input type="checkbox" wire:model.live="selectAll" class="checkbox checkbox-sm" />
                        </th>
                        <th>{{ __('Student') }}</th>
                        <th>{{ __('Class') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th class="text-right">{{ __('Amount') }}</th>
                        <th class="text-right">{{ __('Paid') }}</th>
                        <th class="text-right">{{ __('Balance') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="w-32">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="hover">
                            <td>
                                @if($payment->status === 'pending')
                                    <input type="checkbox" wire:model.live="selectedPayments" value="{{ $payment->id }}" class="checkbox checkbox-sm" />
                                @endif
                            </td>
                            <td>
                                <div class="font-medium text-sm">{{ $payment->student->full_name }}</div>
                                <div class="text-xs text-base-content/60">{{ $payment->student->matricule }}</div>
                            </td>
                            <td class="text-sm">{{ $payment->student->class?->name ?? '-' }}</td>
                            <td>
                                <div class="text-sm">{{ $payment->getTypeLabel() }}</div>
                                @if($payment->month)
                                    <div class="text-xs text-base-content/60">{{ $payment->getMonthLabel() }}</div>
                                @endif
                            </td>
                            <td class="text-right font-mono text-sm">{{ number_format($payment->amount, 0) }}</td>
                            <td class="text-right font-mono text-sm text-success">{{ number_format($payment->amount_paid, 0) }}</td>
                            <td class="text-right font-mono text-sm {{ $payment->balance > 0 ? 'text-error' : 'text-success' }}">
                                {{ number_format($payment->balance, 0) }}
                            </td>
                            <td>
                                <span class="badge {{ $payment->getStatusBadgeClass() }} badge-xs">
                                    {{ __($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    @if($payment->status !== 'paid')
                                        <button wire:click="openPaymentModal({{ $payment->id }})" class="btn btn-primary btn-xs">
                                            {{ __('Pay') }}
                                        </button>
                                    @endif
                                    @if($payment->amount_paid > 0)
                                        <button wire:click="downloadReceipt({{ $payment->id }})" class="btn btn-ghost btn-xs" title="{{ __('Receipt') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-base-content/60">
                                {{ __('No payments found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Initialize Modal --}}
    @if($showInitModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">{{ __('Initialize Payments') }}</h3>
            <form wire:submit="initializePayments">
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Class') }}</span></label>
                    <select wire:model="initFilterClass" class="select select-bordered w-full">
                        <option value="">{{ __('All classes') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('From month') }}</span></label>
                        <select wire:model="initStartMonth" class="select select-bordered w-full">
                            @foreach($monthOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('To month') }}</span></label>
                        <select wire:model="initEndMonth" class="select select-bordered w-full">
                            @foreach($monthOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-control mb-4">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" wire:model="initIncludeRegistration" class="checkbox checkbox-primary" />
                        <span class="label-text">{{ __('Include registration fee') }}</span>
                    </label>
                </div>

                <div class="alert alert-info text-sm mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ __('Existing payments will not be duplicated.') }}</span>
                </div>

                <div class="modal-action">
                    <button type="button" wire:click="$set('showInitModal', false)" class="btn">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Initialize') }}</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="$set('showInitModal', false)"></div>
    </div>
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">{{ __('Record Payment') }}</h3>
            <form wire:submit="recordPayment">
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Amount') }} (MRU)</span></label>
                    <input type="number" wire:model="paymentAmount" class="input input-bordered w-full" step="1" min="1" required />
                </div>
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Payment Method') }}</span></label>
                    <select wire:model="paymentMethod" class="select select-bordered w-full">
                        <option value="cash">{{ __('Cash') }}</option>
                        <option value="transfer">{{ __('Bank Transfer') }}</option>
                        <option value="mobile_money">{{ __('Mobile Money') }}</option>
                    </select>
                </div>
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Notes') }}</span></label>
                    <textarea wire:model="paymentNotes" class="textarea textarea-bordered w-full" rows="2"></textarea>
                </div>
                <div class="modal-action">
                    <button type="button" wire:click="$set('showPaymentModal', false)" class="btn">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Confirm') }}</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="$set('showPaymentModal', false)"></div>
    </div>
    @endif
</div>
