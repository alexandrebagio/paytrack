@include('mail.TemplateHeader')
<div class="body">
    <p>Hello {{ $user->name }}, </p> 
    <p>We need you to click on the link below to confirm your identity.</p>
    <a href="{{ env('APP_URL') }}/api/user/confirmation/{{ $user->remember_token }}"> Click here </a>
</div>
@include('mail.TemplateFooter')
