{{-- resources/views/financial-parameters/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Financial Parameters</h5>
                    {{-- Fix the route name --}}
                    <a href="{{ route('finance.financial-parameters.create') }}" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus"></i> Add New Parameter
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                    <th>Currency</th>
                                    <th>Effective From</th>
                                    <th>Effective To</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($parameters as $parameter)
                                <tr>
                                    <td>{{ $parameter->parameter_name }}</td>
                                    <td>{{ number_format($parameter->parameter_value, 6) }}</td>
                                    <td>{{ $parameter->currency_code ?? 'N/A' }}</td>
                                    <td>{{ $parameter->effective_from->format('M d, Y') }}</td>
                                    <td>{{ $parameter->effective_to ? $parameter->effective_to->format('M d, Y') : 'Current' }}</td>
                                    <td>{{ $parameter->description }}</td>
                                    <td>{{ $parameter->creator->name }}</td>
                                    <td>
                                        {{-- Fix edit route --}}
                                        <a href="{{ route('finance.financial-parameters.edit', $parameter) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Fix delete route --}}
                                        <form action="{{ route('finance.financial-parameters.destroy', $parameter) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $parameters->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
