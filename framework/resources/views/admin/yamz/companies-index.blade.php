@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building"></i> Companies Overview (Yamz Only)
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
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <a href="{{ route('admin.yamz.all-users') }}" class="btn btn-primary">
                                <i class="fas fa-users"></i> View All Users
                            </a>
                            <a href="{{ route('admin.yamz.company.create') }}" class="btn btn-success ml-2">
                                <i class="fas fa-plus"></i> Create Company
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Accounts</th>
                                        <th>Vehicles</th>
                                        <th>Bookings</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $company)
                                        <tr>
                                            <td>{{ $company->name }}</td>
                                            <td>{{ $company->email }}</td>
                                            <td>{{ $company->accounts_count }}</td>
                                            <td>{{ $company->vehicles_count }}</td>
                                            <td>{{ $company->bookings_count }}</td>
                                            <td>
                                                @if($company->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-info" href="{{ route('admin.yamz.companies.show', $company->id) }}">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a class="btn btn-sm btn-warning" href="{{ route('admin.yamz.companies.edit', $company->id) }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form style="display:inline;" action="{{ route('admin.yamz.companies.delete', $company->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($uncountedUsers > 0)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i> {{ $uncountedUsers }} user(s) have no company assigned.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
