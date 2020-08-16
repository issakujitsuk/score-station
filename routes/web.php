<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource("polls", "PollController")->except(["index", "destroy", "edit", "show", "update"]);
Route::get("polls/{poll:token}", "PollController@show")->name("polls.show");
Route::put("polls/{poll:token}", "PollController@update")->name("polls.update");
Route::get("votes/{poll:token}", "VoteController@create")->name("votes.create");
Route::post("votes/store/{poll:token}", "VoteController@store")->name("votes.store");
Route::get("votes/accepted/{poll:token}", "VoteController@accepted")->name("votes.accepted");
Route::get("votes/wait/{poll:token}", "VoteController@wait")->name("votes.wait");
Route::get("votes/expired/{poll:token}", "VoteController@expired")->name("votes.expired");
