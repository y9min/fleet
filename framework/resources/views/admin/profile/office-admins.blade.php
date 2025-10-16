@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie"></i> Office Admins Management
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Create New Office Admin Form -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-plus"></i> Create New Office Admin
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.profile.office-admins.create') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Full Name</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                       id="name" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="email">Email Address</label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" name="email" value="{{ old('email') }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" name="password" required>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input type="password" class="form-control" 
                                                       id="password_confirmation" name="password_confirmation" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-plus"></i> Create Office Admin
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Office Admins List -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list"></i> Existing Office Admins
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($officeAdmins->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Company</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($officeAdmins as $admin)
                                                    <tr>
                                                        <td>{{ $admin->name }}</td>
                                                        <td>{{ $admin->email }}</td>
                                                        <td>{{ $admin->company ? $admin->company->name : 'No Company' }}</td>
                                                        <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary" 
                                                                    data-toggle="modal" data-target="#editModal{{ $admin->id }}">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" 
                                                                    onclick="deleteAdmin({{ $admin->id }})">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Edit Modal -->
                                                    <div class="modal fade" id="editModal{{ $admin->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Office Admin</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <form method="POST" action="{{ route('admin.profile.office-admins.update', $admin->id) }}">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="edit_name{{ $admin->id }}">Full Name</label>
                                                                            <input type="text" class="form-control" 
                                                                                   id="edit_name{{ $admin->id }}" name="name" 
                                                                                   value="{{ $admin->name }}" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="edit_email{{ $admin->id }}">Email Address</label>
                                                                            <input type="email" class="form-control" 
                                                                                   id="edit_email{{ $admin->id }}" name="email" 
                                                                                   value="{{ $admin->email }}" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="edit_password{{ $admin->id }}">New Password (Optional)</label>
                                                                            <input type="password" class="form-control" 
                                                                                   id="edit_password{{ $admin->id }}" name="password">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="edit_password_confirmation{{ $admin->id }}">Confirm New Password</label>
                                                                            <input type="password" class="form-control" 
                                                                                   id="edit_password_confirmation{{ $admin->id }}" name="password_confirmation">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No office admins found.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.profile') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteAdmin(adminId) {
    if (confirm('Are you sure you want to delete this office admin?')) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ url("admin/profile/office-admins") }}/' + adminId;
        form.submit();
    }
}
</script>
@endsection
