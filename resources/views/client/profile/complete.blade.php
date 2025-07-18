<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to KONEK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-circle me-2"></i>
                            Welcome to KONEK!
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            You're almost ready to start posting jobs! Just tell us a bit about yourself to get started.
                        </p>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Good news!</strong> You can skip this step and start posting jobs right away. 
                            You can always complete your profile later.
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.complete.store') }}">
                            @csrf
                            
                            <!-- Basic Information -->
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Basic Information
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                                   id="display_name" name="display_name" 
                                                   value="{{ old('display_name', Auth::user()->name) }}" required>
                                            @error('display_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                                   id="location" name="location" value="{{ old('location') }}" 
                                                   placeholder="City, Country">
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="bio" class="form-label">Bio</label>
                                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                      id="bio" name="bio" rows="2" 
                                                      placeholder="Tell us about yourself...">{{ old('bio', Auth::user()->bio) }}</textarea>
                                            @error('bio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Information (Optional) -->
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-building me-2"></i>Company Information 
                                    <small class="text-muted">(Optional)</small>
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                                   id="company_name" name="company_name" value="{{ old('company_name') }}"
                                                   placeholder="Leave blank if posting as individual">
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="industry" class="form-label">Industry</label>
                                            <input type="text" class="form-control @error('industry') is-invalid @enderror" 
                                                   id="industry" name="industry" value="{{ old('industry') }}"
                                                   placeholder="e.g., Technology, Healthcare, Finance">
                                            @error('industry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_size" class="form-label">Company Size</label>
                                            <select class="form-select @error('company_size') is-invalid @enderror" 
                                                    id="company_size" name="company_size">
                                                <option value="">Select company size</option>
                                                <option value="startup" {{ old('company_size') == 'startup' ? 'selected' : '' }}>Startup (1-10 employees)</option>
                                                <option value="small" {{ old('company_size') == 'small' ? 'selected' : '' }}>Small (11-50 employees)</option>
                                                <option value="medium" {{ old('company_size') == 'medium' ? 'selected' : '' }}>Medium (51-200 employees)</option>
                                                <option value="large" {{ old('company_size') == 'large' ? 'selected' : '' }}>Large (201-1000 employees)</option>
                                                <option value="enterprise" {{ old('company_size') == 'enterprise' ? 'selected' : '' }}>Enterprise (1000+ employees)</option>
                                            </select>
                                            @error('company_size')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_website" class="form-label">Company Website</label>
                                            <input type="url" class="form-control @error('company_website') is-invalid @enderror" 
                                                   id="company_website" name="company_website" value="{{ old('company_website') }}" 
                                                   placeholder="https://yourcompany.com">
                                            @error('company_website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="company_description" class="form-label">Company Description</label>
                                    <textarea class="form-control @error('company_description') is-invalid @enderror" 
                                              id="company_description" name="company_description" rows="3" 
                                              placeholder="Tell us about your company...">{{ old('company_description') }}</textarea>
                                    @error('company_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Skip for Now
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Save & Continue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>