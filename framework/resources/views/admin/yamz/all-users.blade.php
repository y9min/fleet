@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users"></i> All Users (Yamz Only)
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.yamz.companies') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-building"></i> Companies
                            </a>
                            <a href="{{ route('admin.yamz.user.create') }}" class="btn btn-success btn-sm ml-2">
                                <i class="fas fa-plus"></i> Create User
                            </a>
                        </div>
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
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        
                        <!-- User Type Filter -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('admin.yamz.all-users') }}" class="form-inline">
                                    <div class="form-group">
                                        <label for="user_type" class="mr-2">Filter by User Type:</label>
                                        <select class="form-control" id="user_type" name="user_type" onchange="this.form.submit()">
                                            <option value="">All User Types</option>
                                            <option value="B" {{ $selectedUserType == 'B' ? 'selected' : '' }}>Boss Admin</option>
                                            <option value="S" {{ $selectedUserType == 'S' ? 'selected' : '' }}>Super Admin</option>
                                            <option value="O" {{ $selectedUserType == 'O' ? 'selected' : '' }}>Office Admin</option>
                                            <option value="D" {{ $selectedUserType == 'D' ? 'selected' : '' }}>Driver</option>
                                            <option value="C" {{ $selectedUserType == 'C' ? 'selected' : '' }}>Customer</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-8">
                                @if($selectedUserType)
                                    <div class="alert alert-info">
                                        <i class="fas fa-filter"></i> Showing users with type: <strong>{{ ucfirst(strtolower(str_replace(['B', 'S', 'O', 'D', 'C'], ['Boss Admin', 'Super Admin', 'Office Admin', 'Driver', 'Customer'], $selectedUserType))) }}</strong>
                                        <a href="{{ route('admin.yamz.all-users') }}" class="btn btn-sm btn-outline-secondary ml-2">
                                            <i class="fas fa-times"></i> Clear Filter
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allUsers as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>{{ $u->user_type_label }}</td>
                                            <td>{{ $u->company ? $u->company->name : 'No Company' }}</td>
                                            <td>
                                                @if($u->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $u->created_at ? $u->created_at->format('M d, Y') : '' }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-warning" href="{{ route('admin.yamz.user.edit', $u->id) }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @if($u->email !== Auth::user()->email && $u->email !== 'master@admin.com')
                                                    <form style="display:inline;" action="{{ route('admin.yamz.user.delete', $u->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">Protected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
