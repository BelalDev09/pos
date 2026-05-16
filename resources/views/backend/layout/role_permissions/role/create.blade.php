@extends('backend.app')

@section('title', 'Create Role')

@section('content')

{{-- HEADER --}}
<div class="row align-items-center mb-3">
    <div class="col">
        <h5 class="mb-0">Create Role</h5>
    </div>

    <div class="col-auto">
        <a href="{{ route('admin.roles.list') }}" class="btn btn-danger btn-sm">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>
</div>

{{-- CARD --}}
<div class="card shadow-sm">
    <div class="card-body">

        <form action="{{ route('admin.roles.store') }}" method="post">
            @csrf

            {{-- ROLE NAME --}}
            <div class="mb-3">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter role name">

                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- PERMISSIONS --}}
            <div class="mb-2">
                <label class="form-label">All Permissions</label>
            </div>

            {{-- PERMISSIONS --}}
            <div class="mb-2">
                <label class="form-label">All Permissions</label>
            </div>

            @php
            $groupedPermissions = [];

            foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $group = $parts[0];

            $groupedPermissions[$group][] = $permission;
            }
            @endphp


            <div class="row g-3">

                @foreach ($groupedPermissions as $group => $perms)
                <div class="col-md-4 col-sm-6">

                    {{-- SMALL BOX --}}
                    <div class="border rounded shadow-sm p-2 h-100">

                        {{-- HEADER --}}
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-2">

                            <strong class="text-capitalize fs-6">
                                {{ $group }}
                            </strong>

                            <div class="form-check m-0">
                                <input type="checkbox" class="form-check-input"
                                    onclick="toggleGroup(this, '{{ $group }}')">
                            </div>

                        </div>

                        {{-- PERMISSIONS LIST --}}
                        <div class="d-flex flex-column gap-1">

                            @foreach ($perms as $permission)
                            <div class="form-check">

                                <input class="form-check-input group-{{ $group }}" type="checkbox"
                                    name="permissions[]" value="{{ $permission->name }}"
                                    id="perm_{{ $permission->id }}">

                                <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>

                            </div>
                            @endforeach

                        </div>

                    </div>

                </div>
                @endforeach

            </div>

            {{-- SUBMIT --}}
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="ri-check-line me-1"></i> Submit
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
@push('scripts')
<script>
    function toggleGroup(mainCheckbox, group) {
        let checkboxes = document.querySelectorAll('.group-' + group);

        checkboxes.forEach(cb => {
            cb.checked = mainCheckbox.checked;
        });
    }
</script>
@endpush