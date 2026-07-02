<script src="https://jquery.com"></script>
<script>
$(document).ready(function() {
    // Intercepts the form action to prevent serverless page reloads
    $('#serverless-login-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = $('#login-submit-btn');
        var emailInput = $('#email');
        var errorSpan = $('#email-error-msg');
        var errorText = $('#email-error-text');

        // Reset display states
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
                    // Instantly force-load the dashboard view layout bypassing Lambdas
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
