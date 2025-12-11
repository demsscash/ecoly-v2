<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Students') }}</h1>
            <p class="text-base-content/60">{{ __('Manage student enrollment') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-outline btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    {{ __('Export') }}
                </label>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                    <li><button wire:click="exportExcel">Excel (.xlsx)</button></li>
                    <li><button wire:click="exportPdf">PDF</button></li>
                </ul>
            </div>
            <button onclick="import_modal.showModal()" class="btn btn-outline btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                {{ __('Import') }}
            </button>
            <button wire:click="create" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                {{ __('New Student') }}
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="input input-bordered input-sm flex items-center gap-2 w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search') }}..." class="grow bg-transparent border-none focus:outline-none" />
                    </label>
                </div>
                
                {{-- Class filter --}}
                <div>
                    <select wire:model.live="filterClass" class="select select-bordered select-sm w-full">
                        <option value="">{{ __('All classes') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Status filter --}}
                <div>
                    <select wire:model.live="filterStatus" class="select select-bordered select-sm w-full">
                        <option value="">{{ __('All') }} ({{ __('Status') }})</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                        <option value="transferred">{{ __('Transferred') }}</option>
                        <option value="graduated">{{ __('Graduated') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Photo') }}</th>
                        <th>{{ __('Matricule') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Class') }}</th>
                        <th>{{ __('Guardian') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="avatar">
                                    <div class="w-10 h-10 rounded-full">
                                        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" />
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono">{{ $student->matricule }}</td>
                            <td>
                                <a href="{{ route('students.show', $student) }}" class="font-medium hover:underline">{{ $student->full_name }}</a>
                                @if($student->nni)<br><span class="text-xs text-base-content/60 font-mono">{{ $student->nni }}</span>@endif
                            </td>
                            <td>{{ $student->class?->name ?? '-' }}</td>
                            <td>
                                <div>{{ $student->guardian_name }}</div>
                                <div class="text-xs text-base-content/60">{{ $student->guardian_phone }}</div>
                            </td>
                            <td>
                                <span class="badge badge-sm {{ $student->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                                    {{ __($student->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-ghost btn-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    </a>
                                    <button wire:click="edit({{ $student->id }})" class="btn btn-ghost btn-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                    </button>
                                    <button wire:click="delete({{ $student->id }})" wire:confirm="{{ __('Delete this student?') }}" class="btn btn-ghost btn-xs text-error">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-8 text-base-content/60">{{ __('No students found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="p-4 border-t border-base-200">{{ $students->links() }}</div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="modal modal-open">
        <div class="modal-box w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg mb-4">{{ $editingId ? __('Edit Student') : __('New Student') }}</h3>
            <form wire:submit="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Photo --}}
                    <div class="md:col-span-2 flex items-center gap-4">
                        <div class="avatar">
                            <div class="w-20 h-20 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" />
                                @elseif ($existingPhoto)
                                    <img src="{{ asset('storage/' . $existingPhoto) }}" />
                                @else
                                    <img src="{{ $gender === 'female' ? asset('images/default-female.svg') : asset('images/default-male.svg') }}" />
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" wire:model="photo" accept="image/*" class="file-input file-input-bordered file-input-sm w-full max-w-xs" />
                            @if($existingPhoto)
                                <button type="button" wire:click="removePhoto" class="btn btn-ghost btn-xs text-error mt-2">{{ __('Remove photo') }}</button>
                            @endif
                        </div>
                    </div>

                    <div class="divider md:col-span-2 my-2">{{ __('Student Information') }}</div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('First Name') }} *</span></label>
                        <input type="text" wire:model="first_name" class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Last Name') }} *</span></label>
                        <input type="text" wire:model="last_name" class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('First Name') }} (AR)</span></label>
                        <input type="text" wire:model="first_name_ar" dir="rtl" class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Last Name') }} (AR)</span></label>
                        <input type="text" wire:model="last_name_ar" dir="rtl" class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Birth Date') }} *</span></label>
                        <input type="date" wire:model="birth_date" class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Birth Place') }} *</span></label>
                        <input type="text" wire:model="birth_place" class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Gender') }} *</span></label>
                        <select wire:model.live="gender" class="select select-bordered select-sm w-full">
                            <option value="male">{{ __('Male') }}</option>
                            <option value="female">{{ __('Female') }}</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('NNI') }}</span></label>
                        <input type="text" wire:model="nni" maxlength="10" class="input input-bordered input-sm w-full font-mono" placeholder="0000000000" />
                        <label class="label py-0"><span class="label-text-alt text-base-content/60">{{ __('10 digits, optional') }}</span></label>
                    </div>

                    <div class="divider md:col-span-2 my-2">{{ __('Guardian Information') }}</div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Guardian Name') }} *</span></label>
                        <input type="text" wire:model="guardian_name" class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Guardian Name') }} (AR)</span></label>
                        <input type="text" wire:model="guardian_name_ar" dir="rtl" class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Phone') }} *</span></label>
                        <input type="text" wire:model="guardian_phone" class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Phone 2') }}</span></label>
                        <input type="text" wire:model="guardian_phone_2" class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Email') }}</span></label>
                        <input type="email" wire:model="guardian_email" class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Profession') }}</span></label>
                        <input type="text" wire:model="guardian_profession" class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label py-1"><span class="label-text">{{ __('Address') }}</span></label>
                        <input type="text" wire:model="address" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="divider md:col-span-2 my-2">{{ __('School Information') }}</div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Class') }}</span></label>
                        <select wire:model="class_id" class="select select-bordered select-sm w-full">
                            <option value="">{{ __('Not assigned') }}</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Enrollment Date') }} *</span></label>
                        <input type="date" wire:model="enrollment_date" class="input input-bordered input-sm w-full" required />
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Status') }}</span></label>
                        <select wire:model="status" class="select select-bordered select-sm w-full">
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                            <option value="transferred">{{ __('Transferred') }}</option>
                            <option value="graduated">{{ __('Graduated') }}</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ __('Previous School') }}</span></label>
                        <input type="text" wire:model="previous_school" class="input input-bordered input-sm w-full" />
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label py-1"><span class="label-text">{{ __('Notes') }}</span></label>
                        <textarea wire:model="notes" class="textarea textarea-bordered textarea-sm w-full" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-action">
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-sm">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="$set('showModal', false)"></div>
    </div>
    @endif

    {{-- Import Modal --}}
    <dialog id="import_modal" class="modal">
        <div class="modal-box w-11/12 max-w-3xl">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button></form>
            <h3 class="font-bold text-lg mb-4">{{ __('Import Students') }}</h3>
            <livewire:admin.students-import />
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
</div>
