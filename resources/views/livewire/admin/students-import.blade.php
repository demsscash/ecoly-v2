<div>
    @if (!$showResults)
        <div class="space-y-6">
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="font-bold">{{ __('Import Instructions') }}</h3>
                    <p class="text-sm">{{ __('Download the template, fill it with student data, then upload it here.') }}</p>
                </div>
                <button wire:click="downloadTemplate" class="btn btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    {{ __('Download Template') }}
                </button>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">{{ __('Upload File') }}</h3>
                    <div class="form-control">
                        <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" class="file-input file-input-bordered w-full" />
                        <p class="text-xs text-base-content/60 mt-2">{{ __('Formats: Excel (.xlsx, .xls), CSV. Max: 5MB') }}</p>
                    </div>

                    @if ($file)
                        <div class="mt-4 alert">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                            <span>{{ $file->getClientOriginalName() }}</span>
                        </div>
                    @endif

                    <div class="card-actions justify-end mt-4">
                        <button wire:click="import" wire:loading.attr="disabled" class="btn btn-primary" @if(!$file) disabled @endif>
                            <span wire:loading wire:target="import" class="loading loading-spinner loading-sm"></span>
                            {{ __('Import') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="collapse collapse-arrow bg-base-100 shadow">
                <input type="checkbox" />
                <div class="collapse-title font-medium">{{ __('Column Reference') }}</div>
                <div class="collapse-content">
                    <table class="table table-xs">
                        <thead><tr><th>{{ __('Column') }}</th><th>{{ __('Required') }}</th><th>{{ __('Description') }}</th></tr></thead>
                        <tbody>
                            <tr><td class="font-mono">nom</td><td>✅</td><td>Nom de famille</td></tr>
                            <tr><td class="font-mono">prenom</td><td>✅</td><td>Prénom</td></tr>
                            <tr><td class="font-mono">date_naissance</td><td>✅</td><td>JJ/MM/AAAA</td></tr>
                            <tr><td class="font-mono">lieu_naissance</td><td>✅</td><td>Lieu de naissance</td></tr>
                            <tr><td class="font-mono">genre</td><td></td><td>M ou F</td></tr>
                            <tr><td class="font-mono">nni</td><td></td><td>10 chiffres</td></tr>
                            <tr><td class="font-mono">classe</td><td></td><td>Nom de la classe</td></tr>
                            <tr><td class="font-mono">tuteur</td><td>✅</td><td>Nom du tuteur</td></tr>
                            <tr><td class="font-mono">telephone</td><td>✅</td><td>Téléphone</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="space-y-6">
            <div class="stats shadow w-full">
                <div class="stat">
                    <div class="stat-figure text-success"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>
                    <div class="stat-title">{{ __('Imported') }}</div>
                    <div class="stat-value text-success">{{ $imported }}</div>
                </div>
                <div class="stat">
                    <div class="stat-figure text-warning"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg></div>
                    <div class="stat-title">{{ __('Skipped') }}</div>
                    <div class="stat-value text-warning">{{ $skipped }}</div>
                </div>
            </div>

            @if (count($importErrors) > 0)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title text-base text-error">{{ __('Errors') }} ({{ count($importErrors) }})</h3>
                        <div class="max-h-60 overflow-y-auto">
                            <ul class="text-sm space-y-1">
                                @foreach ($importErrors as $err)
                                    <li class="text-error">{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex gap-2">
                <button wire:click="resetImport" class="btn btn-outline">{{ __('Import More') }}</button>
                <a href="{{ route('students') }}" class="btn btn-primary">{{ __('View Students') }}</a>
            </div>
        </div>
    @endif
</div>
