@extends('layout')

@section('title', 'アンケートをお探しですか？')

@section('content')
<h1>アンケートをお探しですか？</h1>

<p>
    {{ config("app.name" )}}でアンケートを作成できます。<br>
</p>
<p class="submit-controller">
    <a href="{{ route('polls.create') }}" class="button">アンケートをつくる</a>
</p>
@endsection
