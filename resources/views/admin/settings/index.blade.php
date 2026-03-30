@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 text-gray-800">
                <i class="fas fa-cog text-primary"></i> System Settings
            </h1>
            <p class="text-muted">Manage system configuration and preferences</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-sliders-h me-2"></i>System Configuration
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        System settings configuration will be implemented here.
                    </div>
                    <p class="text-muted">
                        This section will contain settings for:
                    </p>
                    <ul class="text-muted">
                        <li>General system configuration</li>
                        <li>Email settings and templates</li>
                        <li>Payment gateway configuration</li>
                        <li>User permissions and roles</li>
                        <li>System notifications</li>
                        <li>Backup and maintenance settings</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
