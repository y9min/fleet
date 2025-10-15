@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="page-title">Create User</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.yamz.all-users')}}">All Users</a></li>
                    <li class="breadcrumb-item active">Create User</li>
                </ol>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Main Content -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">New User Information</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{route('admin.yamz.user.store')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_type">User Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="user_type" name="user_type" required>
                                        <option value="">Select User Type</option>
                                        <option value="B" {{ old('user_type') == 'B' ? 'selected' : '' }}>Boss Admin</option>
                                        <option value="S" {{ old('user_type') == 'S' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="O" {{ old('user_type') == 'O' ? 'selected' : '' }}>Office Admin</option>
                                        <option value="D" {{ old('user_type') == 'D' ? 'selected' : '' }}>Driver</option>
                                        <option value="C" {{ old('user_type') == 'C' ? 'selected' : '' }}>Customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_id">Company</label>
                                    <select class="form-control" id="company_id" name="company_id">
                                        <option value="">Select Company (Optional)</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Super Admins and Office Admins should be assigned to a company</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>User Information:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>Boss Admin:</strong> System-wide access to all companies and users (Yamz only)</li>
                                        <li><strong>Super Admin:</strong> Full access to assigned company's data</li>
                                        <li><strong>Office Admin:</strong> Limited access to assigned company's data</li>
                                        <li><strong>Driver:</strong> Vehicle driver account</li>
                                        <li><strong>Customer:</strong> Customer account for booking services</li>
                                        <li><strong>Default Password:</strong> All new users will be created with password "password" - they can change it later</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Create User</button>
                                    <a href="{{route('admin.yamz.all-users')}}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Content -->
</div>
@endsection
