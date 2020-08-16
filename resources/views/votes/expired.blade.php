@extends('layout')

@section('title', '結果発表')

@section('content')
<h1>{{ $poll->title }}</h1>
<p class="description">
    投票期間中に{{ $answer_count }}名が参加しました。
</p>

<section class="summary">
    <h2>総合得点</h2>
    <section>
        <ul class="options">
            @foreach($options as $option)
            <li>
                {{ $option->label }} {{ $summary["total"][$option->id] }}
            </li>
            @endforeach
        </ul>
    </section>
</section>

<section class="summary">

    <h2>個別の得点</h2>
    <section>
        <ol class="questions">
            @foreach($questions as $question)
            <li class="item">
                <h3>{{ $question->label }}</h3>
                <ul class="options">
                    @foreach($options as $option)
                    <li>
                        {{ $option->label }} {{ $summary[$question->id][$option->id] }}
                    </li>
                    @endforeach
                </ul>
            </li>
            @endforeach
        </ol>
    </section>
</section>

<aside class="expired-aside">
    <p>ほかにも調べますか？
        <a href="{{ route("polls.create") }}" class="button">アンケートをつくる</a>
    </p>
</aside>

@endsection
