@extends('layout')

@section('title', 'アンケートが完成しました')

@section('head')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/lrsjng.jquery-qrcode/0.18.0/jquery-qrcode.min.js" defer></script>
<script src="{{ asset("js/polls.show.prod.js")}}" defer></script>
@endsection

@section('content')

@section('caption')
<h1>アンケートが完成しました</h1>
@show

<section>
    <h2>{{ $poll->title }}</h2>
    <label>
        <span>投票ページ</span>
        <input type="text" value="{{ $url }}" class="share one-column" readonly>
    </label>
    <div class="qrcode" data-url="{{ $url }}" data-label="{{ config("app.name") }}"></div>
    <p>
        このURLをシェアして、投票してもらいましょう。
    </p>
</section>

@endsection
