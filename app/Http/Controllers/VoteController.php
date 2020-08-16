<?php

namespace App\Http\Controllers;

use App\Answer;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\DB;

class VoteController extends Controller {

    public function create(Request $request, \App\Poll $poll) {
        if ($poll->isClosed()) {
            return redirect()->route("votes.expired", ["poll" => $poll->token], 301);
        }
        $questions = \App\Question::where("poll_id", $poll->id)->get();
        $options = \App\Option::where("poll_id", $poll->id)->get();

        // 回答者数
        $answer_count = Answer::getAnswerCount($questions);
        $user_names = Answer::getUserNames($questions);

        // 自分が投票済みかチェック
        $user_name = $request->cookie("user_name");
        $answers = Answer::getMyAnswers($user_name, $questions);
        $voted = !empty($answers);
        return view("votes.create", [
            "poll" => $poll,
            //"questions" => $questions,
            //"options" => $options,
            "answer_count" => $answer_count,
            "user_names" => $user_names,
            "user_name" => $user_name,
            "answers" => $answers,
            "voted" => $voted,
            "json" => [
                "poll" => $poll->getOpenAttributes(),
                "questions" => \App\Question::getOpenAttributes($questions),
                "options" => \App\Option::getOpenAttributes($options),
                "point_default" => $poll->point_min <= 0 && 0 <= $poll->point_max ? 0 : $poll->poin_min,
                "answers" => $answers,
                "voted" => $voted,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, \App\Poll $poll) {
        if ($poll->isClosed()) {
            return redirect()->route("votes.expired", ["poll" => $poll->token], 301);
        }

        $request->validate([
            "user_name" => "required|regex:/\S+/",
            "answers" => "required|array",
        ]);

        // Save User
        $user = \App\User::createOrGet($request->user_name);
        \Illuminate\Support\Facades\Cookie::queue("user_name", $user->name);
        if (Answer::isUserVoted($user, $poll->id)) {
            throw new \Exception("You have already voted.");
        }

        // Save Answers
        $answer_values = Answer::getAnswerValues($request, $poll, $user->id);
        DB::transaction(function () use ($answer_values) {
            foreach ($answer_values as $data) {
                Answer::create($data);
            }
        });

        $to = route("votes.accepted", ["poll" => $poll->token]);
        if ($request->ajax()) {
            return response()->json([
                        "success" => true,
                        "url" => $to,
            ]);
        }
        return response()->redirect($to, 303);
    }

    public function accepted(\App\Poll $poll) {
        return view("votes.accepted", [
            "poll" => $poll,
            "url" => route("votes.create", ["poll" => $poll->token]),
        ]);
    }

    public function expired(\App\Poll $poll) {
        if (!$poll->isClosed()) {
            throw new \Exception("Voting is open.");
        }
        $questions = \App\Question::where("poll_id", $poll->id)->get();
        $options = \App\Option::where("poll_id", $poll->id)->get();

        // 集計結果
        $summary = Answer::summary($questions);

        // 投票人数
        $answer_count = Answer::getAnswerCount($questions);

        return view("votes.expired", [
            "poll" => $poll,
            "questions" => $questions,
            "options" => $options,
            "summary" => $summary,
            "answer_count" => $answer_count,
        ]);
    }

}
