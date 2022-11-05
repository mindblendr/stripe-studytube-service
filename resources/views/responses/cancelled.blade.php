@extends('response')

@section('top-symbol')
<i class="top-symbol accent">✘</i>
@endsection

@section('title')
<title>{{ env('APP_NAME') }} - Cancelled</title>
@endsection

@section('content')
<h1 class="accent">Registration Failed!</h1>
<p>Registration has been failed. Please try again</p>
<br><br>
<p><small><i>Something went wrong. Please <a href="https://academyofbrain.com">contact us</a>.</i></small></p>
@endsection

<style>
    .accent {
        color: #c10000
    }
</style>