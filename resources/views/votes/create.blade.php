@extends('layout')

@section('title', '投票する')

@section('head')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.loadtemplate/1.5.10/jquery.loadTemplate.min.js" defer integrity="sha512-T1zx+UG2gXu9mr29wvzaKkNUmAOAie40T32ZPIvcRPJgO5br53+Ocqj8gzguUuix7FK+Z3ncRCJMaZcumnPZzg==" crossorigin="anonymous"></script>
<script src="{{ asset("js/votes.create.prod.js")}}" defer></script>
@endsection

@section('content')

<section class="vote-create">
    <div class="poll-subject"></div>

    @if ($voted)
    <p class="alert">
        {{ $user_name }}さんは投票済みです。
    </p>
    @endif

    <form method="POST" action="{{ route("votes.store", ["poll" => $poll->token]) }}" id="form">
        <div class="poll-body"></div>
        <p class="item">
            <label>
                <span class="required">あなたのお名前</span>
                <input type="text" name="user_name" value="{{ $user_name }}" class="one-column" required {{ $voted ? "readonly" : ""}}>
            </label>
        </p>
        <p class="errors target-user_name"></p>
        <p class="submit-controller">
            <button type="submit">投票する</button>
        </p>
        <p class="errors error-submit"></p>
    </form>

</section>

<aside class="vote-aside">
    <section>
        <h2>投票済みのユーザー</h2>
        <section>
            @if(empty($user_names))
            <p>まだだれも参加していません。</p>
            @else
            <p>{{ $answer_count }}名が参加しました。</p>
            <ul>
                @foreach($user_names as $name)
                <li>{{ $name }}</li>
                @endforeach
                @if(count($user_names) < $answer_count) <li>...</li>
                    @endif
            </ul>
            @endif

        </section>
    </section>

    <section>
        <h2>管理メニュー</h2>
        <section>
            <form action="{{ route("polls.update", ["poll" => $poll->token]) }}" method="post">
                @csrf
                @method('put')
                <p class="item">
                    <label>
                        <span>パスワード</span>
                        <input type="password" name="password" class="one-column" required>
                    </label>
                </p>
                <p class="submit-controller">
                    <button type="submit">投票を締め切る</button>
                </p>
            </form>
        </section>
    </section>
</aside>


@include('template-poll')

<script type="application/json" id="poll-json">
    @json($json)

</script>
@endsection
