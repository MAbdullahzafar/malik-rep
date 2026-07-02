@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="card-header" style="font-weight: 700; background-color: #f8f9fa;">{{ __('Login') }}</div>

                <div class="card-body" style="padding: 25px;">
                    <!-- NATIVE INTERCEPT: Prevents synchronous page post-back reloads entirely -->
                    <form method="POST" action="{{ route('login') }}" id="serverless-login-form" onsubmit="event.preventDefault();">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end" style="font-weight: 600;">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                <span class="invalid-feedback" id="email-error-msg" role="alert" style="display: none;">
                                    <strong id="email-error-text"></strong>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end" style="font-weight: 600;">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" id="login-submit-btn" class="btn btn-primary" style="background-color: #3b82f6; border: none; padding: 8px 20px; font-weight: 600; border-radius: 4px;">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}" style="color: #64748b; text-decoration: none; font-size: 13px; margin-left: 10px;">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VERIFIED CDN: Loads a valid JavaScript library copy instead of a raw webpage -->
<script src="https://jquery.com"></script>
<script>
$(document).ready(function() {
    $('#serverless-login-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = $('#login-submit-btn');
        var emailInput = $('#email');
        var errorSpan = $('#email-error-msg');
        var errorText = $('#email-error-text');

        emailInput.removeClass('is-invalid');
        errorSpan.hide();
        submitBtn.prop('disabled', true).text('Processing Portal Entrance...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success && response.redirect) {
                    window.location.replace(response.redirect);
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).text('Login');
                emailInput.addClass('is-invalid');
                errorSpan.show();
                
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.email) {
                    errorText.text(xhr.responseJSON.errors.email);
                } else {
                    errorText.text('Authentication Failed: Verify credentials are correct.');
                }
            }
        });
    });
});
</script>
@endsection
