@extends('polls.show')

@section('title', '回答を受け付けました')

@section('content')
@parent

<div class="back-to-vote">
    <a href="{{ $url }}" class="button">アンケートに戻る</a>
</div>

@endsection

@section('caption')
<h1>回答を受け付けました</h1>
@endsection
