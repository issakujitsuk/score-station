"use strict";

$(function () {
  var json = JSON.parse($("#poll-json").text()); // subject

  $(".poll-subject").loadTemplate("#template-poll-subject", {
    title: json.poll.title,
    expiry: json.poll.expiry
  }); // questions

  var questions = [];

  for (var i = 0; i < json.questions.length; i++) {
    var question = json.questions[i]; // options

    var options = [];

    for (var _i = 0; _i < json.options.length; _i++) {
      var option = json.options[_i];

      var _$tmp = $(document.createDocumentFragment());

      _$tmp.loadTemplate("#template-poll-option", {
        "optionId": "answers[" + question.id + "][" + option.id + "]",
        "optionTag": "answers-" + question.id + "-" + option.id,
        "min": json.poll.point_min,
        "max": json.poll.point_max,
        "answer": (json.answers[question.id] || {})[option.id] || json.point_default,
        "label": option.label,
        "voted": json.voted
      });

      options[options.length] = _$tmp;
    } // question


    var $tmp = $(document.createDocumentFragment());
    $tmp.loadTemplate("#template-poll-question", {
      label: question.label
    });
    $tmp.find(".options").append(options);
    questions[questions.length] = $tmp;
  } // body


  var $body = $($("#template-poll-body")[0].content).clone();
  $body.find(".questions").append(questions);
  $(".poll-body").append($body); // set event handler

  $("#form").on("submit", function (event) {
    event.preventDefault();
    $(".errors").empty();
    var $form = $(this);
    $.ajax({
      url: $form.prop("action"),
      method: $form.prop("method"),
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: $form.serialize(),
      dataType: "json"
    }).done(function (response) {
      console.log(response);
      document.location = response.url;
    }).fail(function ($xhr, textStatus, error) {
      var response = "" + $xhr.responseText;

      if (response !== "") {
        try {
          response = JSON.parse(response);

          if (response.message != null) {
            error = response.message;
          }

          console.log(response);
        } catch (ex) {}
      }

      $(".errors").empty();

      if (response.errors) {
        for (var k in response.errors) {
          $(".errors.target-" + k).append(response.errors[k]);
        }
      }

      $(".error-submit").append("[" + textStatus + "]" + error);
    });
    return false;
  }).on("change", ".answers", function (event) {
    var el = this;
    var $this = $(this);
    var target = $this.data("target");
    var value = $this.val(); // console.log("change", target);

    $("." + target).each(function (idx, item) {
      if (el !== item) {
        // console.log("write", item);
        $(item).val(value);
      }
    });
  });
});