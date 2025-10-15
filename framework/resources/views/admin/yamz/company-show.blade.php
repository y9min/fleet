@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building"></i> {{ $company->name }} — Details
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.yamz.companies') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="border p-3 rounded">
                                    <div class="text-muted">Vehicles</div>
                                    <div class="h4 mb-0">{{ $vehiclesCount }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border p-3 rounded">
                                    <div class="text-muted">Super Admins</div>
                                    <div class="h4 mb-0">{{ $supers->count() }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border p-3 rounded">
                                    <div class="text-muted">Office Admins</div>
                                    <div class="h4 mb-0">{{ $offices->count() }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border p-3 rounded">
                                    <div class="text-muted">Drivers</div>
                                    <div class="h4 mb-0">{{ $drivers->count() }}</div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Super Admins</h5>
                        <ul class="list-group mb-3">
                            @forelse($supers as $u)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $u->name }} <span class="text-muted">{{ $u->email }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">No super admins</li>
                            @endforelse
                        </ul>

                        <h5>Office Admins</h5>
                        <ul class="list-group mb-3">
                            @forelse($offices as $u)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $u->name }} <span class="text-muted">{{ $u->email }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">No office admins</li>
                            @endforelse
                        </ul>

                        <h5>Drivers</h5>
                        <ul class="list-group mb-3">
                            @forelse($drivers as $u)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $u->name }} <span class="text-muted">{{ $u->email }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">No drivers</li>
                            @endforelse
                        </ul>

                        <h5>Recent Vehicles</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Plate</th>
                                        <th>In Service</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles as $v)
                                        <tr>
                                            <td>{{ $v->id }}</td>
                                            <td>{{ $v->make_name }}</td>
                                            <td>{{ $v->model_name }}</td>
                                            <td>{{ $v->license_plate }}</td>
                                            <td>{{ $v->in_service ? 'Yes' : 'No' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5">No vehicles found</td></tr>
                                    @endforelse
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
