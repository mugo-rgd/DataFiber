@extends('layouts.app')

@section('title', 'Campaign Management - Marketing Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bullhorn me-2"></i>Campaign Management
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
            <i class="fas fa-plus me-2"></i>Create Campaign
        </button>
    </div>

    <!-- Campaign Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Campaigns
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Reach
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">45.2K</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Engagement Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12.4%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                ROI
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3.2x</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Marketing Campaigns</h6>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm active">All</button>
                        <button class="btn btn-outline-primary btn-sm">Active</button>
                        <button class="btn btn-outline-primary btn-sm">Completed</button>
                        <button class="btn btn-outline-primary btn-sm">Draft</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Campaign Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Budget</th>
                                    <th>Leads</th>
                                    <th>Conversion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Q4 Enterprise Push</td>
                                    <td>Email Marketing</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>Oct 1, 2024</td>
                                    <td>Dec 31, 2024</td>
                                    <td>$15,000</td>
                                    <td>245</td>
                                    <td>18%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Analytics">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Social Media Awareness</td>
                                    <td>Social Media</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>Nov 1, 2024</td>
                                    <td>Nov 30, 2024</td>
                                    <td>$8,000</td>
                                    <td>189</td>
                                    <td>12%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Analytics">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Referral Program Launch</td>
                                    <td>Referral</td>
                                    <td><span class="badge bg-warning">Draft</span></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>$5,000</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Summer Promotion 2024</td>
                                    <td>Digital Ads</td>
                                    <td><span class="badge bg-secondary">Completed</span></td>
                                    <td>Jun 1, 2024</td>
                                    <td>Aug 31, 2024</td>
                                    <td>$12,000</td>
                                    <td>312</td>
                                    <td>22%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Analytics">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Campaign Modal -->
<div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Campaign</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Campaign creation feature is currently under development.
                </div>
                <p class="text-muted">This functionality will allow you to create and manage marketing campaigns with detailed targeting, budgeting, and performance tracking.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" disabled>Create Campaign</button>
            </div>
        </div>
    </div>
</div>
@endsection
