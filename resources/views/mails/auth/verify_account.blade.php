@component('mail::message')
Hello {{ $data['first_name'] }},

You have been added as a {{ $data['type'] }} to {{ $data["school_name"] }}. click on the <b>CREATE ACCOUNT </b> to create your account.
This invitation code is valid for 24 hours.

<br/>

<a href="{{ $data['url'] }}">CREATE ACCOUNT</a>

<br/>

or copy and paste the following link in your browser:

<br/>

{{ $data['url'] }}

<br/>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
