<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@section('head')
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title') - {{ config("app.name") }}</title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" defer></script>
<link rel="stylesheet" href="https://unpkg.com/modern-css-reset/dist/reset.min.css">
<link rel="stylesheet" href="{{ asset("css/app.min.css") }}">
@show

<header id="header">
    <p class="title">{{ config("app.name") }}</p>
</header>
<main>
    @yield('content')
</main>
<footer>
    <a href="#header">▲ページトップへ</a> | powered by <span class="title">{{ config("app.name") }}</span>.
</footer>
