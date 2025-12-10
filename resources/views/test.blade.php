<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" 
    data-theme="{{ session('theme', 'light') }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecoly - Test</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Kufi+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen font-sans antialiased bg-base-200 p-8">
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('theme-changed', (data) => {
                document.documentElement.setAttribute('data-theme', data.theme);
            });
        });
    </script>

    <div class="max-w-4xl mx-auto space-y-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center">
                    <span class="text-white font-bold text-2xl">E</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Ecoly</h1>
                    <p class="text-sm text-base-content/60">{{ __('Dashboard') }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <livewire:language-switcher />
                <livewire:theme-toggle />
            </div>
        </div>
        
        {{-- Test DaisyUI Components --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">{{ __('Configuration') }}</h2>
                <p>Test des composants DaisyUI</p>
                
                {{-- Buttons --}}
                <div class="flex flex-wrap gap-2 mt-4">
                    <button class="btn btn-primary">{{ __('Save') }}</button>
                    <button class="btn btn-secondary">{{ __('Cancel') }}</button>
                    <button class="btn btn-error">{{ __('Delete') }}</button>
                    <button class="btn btn-warning">{{ __('Edit') }}</button>
                </div>
                
                {{-- Form Elements --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('First Name') }}</span></label>
                        <input type="text" placeholder="{{ __('First Name') }}" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Last Name') }}</span></label>
                        <input type="text" placeholder="{{ __('Last Name') }}" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Email') }}</span></label>
                        <input type="email" placeholder="{{ __('Email') }}" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Role') }}</span></label>
                        <select class="select select-bordered">
                            <option>{{ __('Admin') }}</option>
                            <option>{{ __('Secretary') }}</option>
                            <option>{{ __('Teacher') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="stat bg-primary text-primary-content rounded-box">
                <div class="stat-title text-primary-content/80">{{ __('Students') }}</div>
                <div class="stat-value">400</div>
            </div>
            <div class="stat bg-secondary text-secondary-content rounded-box">
                <div class="stat-title text-secondary-content/80">{{ __('Classes') }}</div>
                <div class="stat-value">12</div>
            </div>
            <div class="stat bg-accent text-accent-content rounded-box">
                <div class="stat-title text-accent-content/80">{{ __('Finances') }}</div>
                <div class="stat-value">1.2M</div>
            </div>
        </div>
        
        {{-- Table Example --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">{{ __('Students') }}</h2>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('First Name') }}</th>
                                <th>{{ __('Classes') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>محمد ولد أحمد</td>
                                <td>السنة الرابعة</td>
                                <td><span class="badge badge-success">{{ __('Active') }}</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>فاطمة بنت محمد</td>
                                <td>السنة الخامسة</td>
                                <td><span class="badge badge-success">{{ __('Active') }}</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>أحمد ولد سالم</td>
                                <td>السنة السادسة</td>
                                <td><span class="badge badge-success">{{ __('Active') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        {{-- Alerts --}}
        <div class="space-y-2">
            <div class="alert alert-success">
                <span>{{ __('Success') }} - Opération réussie</span>
            </div>
            <div class="alert alert-warning">
                <span>{{ __('Warning') }} - Attention requise</span>
            </div>
            <div class="alert alert-error">
                <span>{{ __('Error') }} - Une erreur est survenue</span>
            </div>
        </div>
        
    </div>
    
    @livewireScripts
</body>
</html>
