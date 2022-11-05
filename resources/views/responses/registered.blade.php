@extends('response')

@section('top-symbol')
<i class="top-symbol accent">✓</i>
@endsection

@section('title')
<title>{{ env('APP_NAME') }} - Success</title>
@endsection

@section('content')
<h1 class="accent">Welcome back!</h1>
<p>It seems like you are already registered.
    @if ($emailSent == 'emailSent')
    Kindly check your email for your account details!
    @endif
</p>
<br><br>
<p><small><i>Please visit <a href="https://brain.studytube.nl/">Academy of Brain</a> to sign in.</i></small></p>
@endsection

<style>
    .accent {
        color: #9ABC66
    }
</style>