@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Admin Profile</h4>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="profile-image mb-3">
                                <img src="{{ $admin->avatar ?? 'https://via.placeholder.com/150x150?text=Admin' }}" 
                                     alt="Admin Avatar" 
                                     class="rounded-circle img-fluid" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="profile-info">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Name:</label>
                                    <p class="mb-0">{{ $admin->name }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="mb-0">{{ $admin->email }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Role:</label>
                                    <p class="mb-0">
                                        <span class="badge bg-primary">Administrator</span>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Account Created:</label>
                                    <p class="mb-0">{{ $admin->created_at->format('F j, Y') }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Last Updated:</label>
                                    <p class="mb-0">{{ $admin->updated_at->format('F j, Y g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection