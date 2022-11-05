@extends('response')

@section('top-symbol')
<i class="top-symbol accent">✓</i>
@endsection

@section('title')
<title>{{ env('APP_NAME') }} - Success</title>
@endsection

@section('content')
<h1 class="accent">Thank You!</h1>
@if ($emailSent == 'emailSent')
<p>Kindly check your email for your account details!</p>
@else
<p>You have successfully registered!</p>
@endif
<br><br>
<p><small><i>Please visit <a href="https://academyofbrain.com">Academy of Brain</a>.</i></small></p>
@endsection

<style>
    .accent {
        color: #9ABC66
    }
</style>