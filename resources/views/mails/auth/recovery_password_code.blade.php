@component('mail::message')
Hello {{ $data['first_name'] }},

We received a request to reset your account password.
Enter the following password reset code:

<br/>
<h2>{{ $data['code'] }}</h2>
<br/>

<strong>Didn't request this change?</strong>
<br/>
If you didn't request a new password, <a href="#"><u>let us know.</u></a>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
