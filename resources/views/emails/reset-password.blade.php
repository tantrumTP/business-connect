@component('mail::message')
# {{ __('Password Reset') }}

{{ __('You have requested to reset your password. Click the button below to continue:') }}

@component('mail::button', ['url' => $resetUrl])
{{ __('Reset Password') }}
@endcomponent

{{ __('If you did not request this change, you can ignore this email.') }}

{{ __('Thank you,') }}<br>
{{ config('app.name') }}
@endcomponent
