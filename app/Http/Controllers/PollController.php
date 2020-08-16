<?php

namespace App\Http\Controllers;

use App\Poll;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\DB;

class PollController extends Controller {

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('polls.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            "title" => 'required|string',
            "password" => 'required|regex:/^[a-zA-Z\d]{5,}$/',
            "title" => 'required|string',
            "questions" => 'required|regex:/\S+/',
            "options" => 'required|regex:/\S+/',
            "point_min" => 'required|integer',
            "point_max" => 'required|integer',
            "expiry" => 'required|date|after:now',
        ]);
        $point_min = $request->point_min;
        $point_max = $request->point_max;
        if ($point_min == $point_max) {
            throw new Exception("point_min must be less than point_max.");
        } else if ($point_max < $point_min) {
            list($point_min, $point_max) = [$point_max, $point_min];
        }

        $poll_token = null;
        DB::beginTransaction();
        try {
            // Poll
            /** @var App\Poll */
            $poll = Poll::create([
                        "title" => $request->title,
                        "password" => password_hash($request->password, PASSWORD_DEFAULT),
                        "point_min" => $point_min,
                        "point_max" => $point_max,
                        "expiry" => (new \DateTime($request->expiry))->format("Y-m-d H:i"),
            ]);
            $poll_id = $poll->id;
            $common = ["poll_id" => $poll_id];

            // Questions
            \App\Question::createMulti($request->questions, $common);

            // Option
            \App\Option::createMulti($request->options, $common);

            // Token
            $poll->storeToken();
            $poll_token = $poll->token;

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $ex) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error($ex->getMessage());
            throw $ex;
        }

        $to = route("polls.show", ["poll" => $poll_token]);
        if ($request->ajax()) {
            return response()->json([
                        "success" => true,
                        "url" => $to,
            ]);
        }
        return redirect($to, 303);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function show(Poll $poll) {
        if ($poll->isClosed()) {
            return redirect(route("votes.expired", ["poll" => $poll->token]));
        }
        
        $url = route("votes.create", ["poll" => $poll->token]);
        return view("polls.show", [
            "poll" => $poll,
            "url" => $url,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poll $poll) {
        $request->validate([
            "password" => "required|string",
        ]);
        if ($poll->isClosed()) {
            throw new Exception("The poll has already expired.");
        }

        // check password
        if (!$poll->checkPassword($request->password)) {
            throw new \Exception("The password is wrong.");
        }

        // update poll->expiry
        $poll->expiry = (new \DateTime("now"))->format("Y-m-d H:i");
        $poll->update();

        return redirect(route("votes.expired", ["poll" => $poll->token]));
    }

}
