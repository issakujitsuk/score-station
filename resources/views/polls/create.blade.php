@extends('layout')

@section('title', 'アンケートをつくる')

@section('head')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.loadtemplate/1.5.10/jquery.loadTemplate.min.js" defer integrity="sha512-T1zx+UG2gXu9mr29wvzaKkNUmAOAie40T32ZPIvcRPJgO5br53+Ocqj8gzguUuix7FK+Z3ncRCJMaZcumnPZzg==" crossorigin="anonymous"></script>
<script src="{{ asset("js/polls.create.dev.js")}}" defer></script>
@endsection

@section('content')
<section class="poll-create">
    <h1>アンケートをつくる</h1>

    <form method="POST" action="{{ route("polls.store") }}" id="form">
        <p class="item">
            <label>
                <span class="required">タイトル</span>
                <input type="text" name="title" class="for-subject one-column" placeholder="赤い食べ物について" required>
            </label>
        </p>
        <p class="errors target-title"></p>
        <p class="item">
            <label>
                <span class="required">質問リスト</span>
                <textarea name="questions" class="for-body list" placeholder="甘いと感じるものは？
酸っぱいと感じるものは？" required></textarea>
            </label>
        </p>
        <p class="errors target-questions"></p>
        <p class="item">
            <label>
                <span class="required">投票対象</span>
                <textarea name="options" class="for-body list" placeholder="トマト
いちご" required></textarea>
            </label>
        </p>
        <p class="errors target-options"></p>
        <p class="description">
            質問リスト、投票対象は各行が1項目となります。
        </p>
        <p class="item">
            <label>
                <span class="required">点数</span>
                <input type="number" name="point_min" class="for-body value" value="0" required>
                ～
                <input type="number" name="point_max" class="for-body value" value="1" required>
            </label>
        </p>
        <p class="errors target-point_min target-point_max"></p>
        <p class="description">
            0～2と設定した場合は、それぞれの候補に0点、1点、2点のどれかを投票することができます。
        </p>
        <p class="item">
            <label>
                <span class="required">締め切り</span>
                <input type="datetime-local" name="expiry" class="for-subject value" list="expiry-options" required>
            </label>
        </p>
        <p class="errors target-expiry"></p>
        <datalist id="expiry-options">
            @foreach ([1,3,7,14] as $days)
            <option value="{{ (new DateTime())->setTime(0, 0)->add(new DateInterval("P{$days}D"))->format('Y-m-d\TH:i')}}">
                @endforeach
        </datalist>
        <p class="item">
            <label>
                <span class="required">パスワード</span>
                <input type="password" name="password" pattern="^[a-zA-Z\d]{5,}$" class="one-column" placeholder="英数字5文字以上" required>
            </label>
        </p>
        <p class="errors target-password"></p>
        <p class="description">
            パスワードはこのアンケートを手動で締め切りするときに必要となります。<br>
        </p>
        <p class="submit-controller">
            <button type="submit">この内容でつくる</button>
        </p>
        <p class="errors error-submit"></p>
    </form>
</section>
<section class="preview">
    <h2>プレビュー</h2>
    <section>
        <div class="poll-subject"></div>
        <div class="poll-body"></div>
    </section>
</section>

@include('template-poll')

@endsection
