"use strict";

$(function () {
  $("#form").on("input", ".for-subject", function () {
    $(".poll-subject").loadTemplate("#template-poll-subject", {
      title: $("input[name='title']").val(),
      expiry: formatDateTime($("input[name='expiry']").val())
    });
  }).on("input", ".for-body", function () {
    // 入力データまとめ
    var inputs = {};
    $(".for-body.list").each(function (idx, item) {
      var name = item.name;
      var list = [];
      var value = ("" + $(item).val()).trim();

      if (value !== "") {
        value.split(/[\r\n]+/g).forEach(function (item) {
          list[list.length] = item;
        });
      }

      inputs[name] = list;
    });
    $(".for-body.value").each(function (idx, item) {
      inputs[item.name] = $(item).val();
    }); // console.log(inputs);
    // questions

    var questions = [];

    for (var i = 0; i < inputs.questions.length; i++) {
      var question = inputs.questions[i];
      var questionId = i; // options

      var options = [];

      for (var _i = 0; _i < inputs.options.length; _i++) {
        var option = inputs.options[_i];
        var optionId = _i;

        var _$tmp = $(document.createDocumentFragment());

        _$tmp.loadTemplate("#template-poll-option", {
          "label": option,
          "optionId": "answers[" + questionId + "][" + optionId + "]",
          "optionTag": "answers-" + questionId + "-" + optionId,
          "min": inputs.point_min,
          "max": inputs.point_max,
          "answer": inputs.point_min
        });

        options[options.length] = _$tmp;
      } // question


      var $tmp = $(document.createDocumentFragment());
      $tmp.loadTemplate("#template-poll-question", {
        label: question
      });
      $tmp.find(".options").append(options);
      questions[questions.length] = $tmp;
    } // body


    var $body = $($("#template-poll-body")[0].content).clone();
    $body.find(".questions").append(questions);
    $(".poll-body").empty().append($body);
  }).on("submit", function (event) {
    event.preventDefault(); // エラー出力を削除

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
      // エラー出力を削除
      $(".errors").empty();
      var response = "" + $xhr.responseText;

      if (response !== "") {
        try {
          response = JSON.parse(response);

          if (response.message != null) {
            error = response.message;
          }

          console.log(response);
        } catch (ex) {// nop
        }
      }

      if (response.errors) {
        for (var k in response.errors) {
          $(".errors.target-" + k).append(response.errors[k].join("<br>") + "<br>");
        }
      }

      $(".error-submit").append("[" + textStatus + "]" + error);
    });
    return false;
  });
  $(".preview").on("change", ".answers", function (event) {
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
  $("#form .for-preview").trigger("input");
  /**
   * @param {string} str
   * @returns {string} yyyy/mm/dd HH:MM
   */

  function formatDateTime(str) {
    var date = new Date(str);
    return isNaN(date.getTime()) ? "" : Intl.DateTimeFormat("japanese", {
      dateStyle: "medium",
      timeStyle: "short"
    }).format(date);
  }
});