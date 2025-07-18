@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-shield-alt fa-2x text-white"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">Security Check</h3>
                            <p class="text-muted mb-0">
                                Enter the 6-digit code sent to your email
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success border-0 rounded-3 mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('two-factor.verify') }}">
                            @csrf

                            <div class="mb-4">
                                <div class="code-input-container">
                                    <input 
                                        id="code" 
                                        type="text" 
                                        class="form-control form-control-lg text-center border-2 @error('code') is-invalid @enderror" 
                                        name="code" 
                                        value="{{ old('code') }}" 
                                        required 
                                        autocomplete="one-time-code" 
                                        autofocus 
                                        maxlength="6"
                                        placeholder="● ● ● ● ● ●"
                                        style="font-size: 2rem; letter-spacing: 1rem; height: 70px; border-radius: 15px;"
                                    >
                                </div>

                                @error('code')
                                    <div class="invalid-feedback d-block text-center mt-2">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg py-3 rounded-3 fw-bold">
                                    <i class="fas fa-check me-2"></i>Verify Code
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="text-muted mb-3">Didn't receive the code?</p>
                            
                            <form method="POST" action="{{ route('two-factor.resend') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary rounded-3 px-4">
                                    <i class="fas fa-redo me-2"></i>Resend Code
                                </button>
                            </form>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-clock text-warning me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Time Sensitive</h6>
                                <p class="text-muted mb-0 small">
                                    This code expires in 5 minutes for your security.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.code-input-container {
    position: relative;
}

#code {
    background: linear-gradient(145deg, #f8f9fa, #ffffff);
    transition: all 0.3s ease;
}

#code:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(145deg, #0d6efd, #0b5ed7);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

/* Loading state for button */
.btn-primary:disabled {
    background: #6c757d;
    transform: none;
}

/* Animation for success */
@keyframes success-pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.alert-success {
    animation: success-pulse 0.5s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Auto-format the input to show only numbers
    codeInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        e.target.value = value;
        
        // Update placeholder style based on input
        if (value.length > 0) {
            e.target.style.color = '#0d6efd';
            e.target.style.fontWeight = 'bold';
        } else {
            e.target.style.color = '#6c757d';
            e.target.style.fontWeight = 'normal';
        }
    });

    // Auto-submit when 6 digits are entered
    codeInput.addEventListener('keyup', function(e) {
        if (e.target.value.length === 6) {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
            
            // Small delay to ensure user sees the complete code
            setTimeout(() => {
                document.querySelector('form').submit();
            }, 800);
        }
    });

    // Prevent paste of non-numeric content
    codeInput.addEventListener('paste', function(e) {
        e.preventDefault();
        let paste = (e.clipboardData || window.clipboardData).getData('text');
        let numericPaste = paste.replace(/[^0-9]/g, '').substring(0, 6);
        e.target.value = numericPaste;
        
        // Trigger input event to update styling
        e.target.dispatchEvent(new Event('input'));
        
        // Auto-submit if 6 digits pasted
        if (numericPaste.length === 6) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
            
            setTimeout(() => {
                document.querySelector('form').submit();
            }, 800);
        }
    });

    // Reset button state if form submission fails
    window.addEventListener('pageshow', function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});
</script>
@endsection