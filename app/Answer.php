<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {

    protected $fillable = ["user_id", "question_id", "option_id", "point"];

    public static function getMyAnswers($user_name, \ArrayAccess $questions) {
        $user = User::getByName($user_name);
        if (!isset($user)) {
            return [];
        }

        $records = Answer::whereIn("question_id", self::questionsToIds($questions))
                        ->where("user_id", $user->id)->get();
        $answers = [];
        foreach ($records as $answer) {
            $answers[$answer->question_id][$answer->option_id] = $answer->point;
        }
        return $answers;
    }

    /**
     * @param \App\User $user
     * @param type $poll_id
     * @return boolean $user が $poll_id に回答済みか
     * @throws \Exception
     */
    public static function isUserVoted(User $user, $poll_id) {
        if (!isset($user)) {
            return false;
        }
        $questions = \App\Question::where("poll_id", $poll_id)->get();

        $exists = Answer::whereIn("question_id", self::questionsToIds($questions))
                        ->where("user_id", $user->id)->exists();
        return !!$exists;
    }

    /**
     * Answer オブジェクト生成用の値をまとめる
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Poll $poll
     * @param type $user_id
     * @return type
     * @throws \Exception
     */
    public static function getAnswerValues(\Illuminate\Http\Request $request, Poll $poll, $user_id) {
        $poll_id = $poll->id;
        $questions = \App\Question::where("poll_id", $poll_id)->get();
        $options = \App\Option::where("poll_id", $poll_id)->get();
        $values = [];
        foreach ($questions as $question) {
            $question_id = $question->id;
            foreach ($options as $option) {
                $option_id = $option->id;
                if (!isset($request->answers[$question_id][$option_id])) {
                    throw new \Exception("The answer $question_id-$option_id is required.");
                }
                $point = $request->answers[$question_id][$option_id];
                if ($point < $poll->point_min || $poll->point_max < $point) {
                    throw new \Exception("The answer $question_id-$option_id is invalid.");
                }
                $values[] = [
                    "user_id" => $user_id,
                    "question_id" => $question_id,
                    "option_id" => $option_id,
                    "point" => $point,
                ];
            }
        }
        return $values;
    }

    public static function getAnswerCount(\ArrayAccess $questions) {
        return \Illuminate\Support\Facades\DB::table("answers")
                        ->whereIn("question_id", self::questionsToIds($questions))
                        ->distinct()
                        ->count("answers.user_id");
    }

    /**
     * @param \ArrayAccess $questions
     * @param int $limit 投票が新しい順で $limit 件。default: 5
     * @return array 投票したユーザーの名前の配列
     */
    public static function getUserNames(\ArrayAccess $questions, $limit = 5) {
        $users = \Illuminate\Support\Facades\DB::table("users")
                ->select('users.name')
                ->distinct()
                ->join("answers", "users.id", "=", "answers.user_id")
                ->whereIn("question_id", self::questionsToIds($questions))
                ->orderByDesc("answers.id")
                ->limit($limit)
                ->get();
        $user_names = [];
        foreach ($users as $user) {
            $user_names[] = $user->name;
        }
        return $user_names;
    }

    public static function summary(\ArrayAccess $quesions) {
        $summary = [];

        // 個々の質問
        $answers = \Illuminate\Support\Facades\DB::table("answers")
                ->select(["question_id", "option_id", \Illuminate\Support\Facades\DB::raw("sum(point) as point")])
                ->whereIn("question_id", self::questionsToIds($quesions))
                ->groupBy("question_id", "option_id")
                ->get();
        foreach ($answers as $item) {
            $summary[$item->question_id][$item->option_id] = +$item->point;

            // トータル
            if (!isset($summary["total"][$item->option_id])) {
                $summary["total"][$item->option_id] = 0;
            }
            $summary["total"][$item->option_id] += $item->point;
        }

        return $summary;
    }

    private static function questionsToIds(\ArrayAccess $questions) {
        $ids = [];
        foreach ($questions as $questions) {
            $ids[] = $questions->id;
        }
        return $ids;
    }

}
