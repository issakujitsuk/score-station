$(function () {

    $("#form").on("input", ".for-subject", function () {
        $(".poll-subject").loadTemplate("#template-poll-subject", {
            title: $("input[name='title']").val(),
            expiry: formatDateTime($("input[name='expiry']").val()),
        });

    }).on("input", ".for-body", function () {
        // 入力データまとめ
        const inputs = {};
        $(".for-body.list").each((idx, item) => {
            const name = item.name;
            const list = [];
            const value = ("" + $(item).val()).trim();
            if (value !== "") {
                value.split(/[\r\n]+/g).forEach(function (item) {
                    list[list.length] = item;
                });
            }
            inputs[name] = list;
        });
        $(".for-body.value").each((idx, item) => {
            inputs[item.name] = $(item).val();
        });
        // console.log(inputs);

        // questions
        const questions = [];
        for (let i = 0; i < inputs.questions.length; i++) {
            const question = inputs.questions[i];
            const questionId = i;
            // options
            const options = [];
            for (let i = 0; i < inputs.options.length; i++) {
                const option = inputs.options[i];
                const optionId = i;
                const $tmp = $(document.createDocumentFragment());
                $tmp.loadTemplate("#template-poll-option", {
                    "label": option,
                    "optionId": "answers[" + questionId + "][" + optionId + "]",
                    "optionTag": "answers-" + questionId + "-" + optionId,
                    "min": inputs.point_min,
                    "max": inputs.point_max,
                    "answer": inputs.point_min,
                });
                options[options.length] = $tmp;
            }

            // question
            const $tmp = $(document.createDocumentFragment());
            $tmp.loadTemplate("#template-poll-question", {
                label: question,
            });
            $tmp.find(".options").append(options);
            questions[questions.length] = $tmp;
        }

        // body
        const $body = $($("#template-poll-body")[0].content).clone();
        $body.find(".questions").append(questions);
        $(".poll-body").empty().append($body);

    }).on("submit", function (event) {
        event.preventDefault();
        // エラー出力を削除
        $(".errors").empty();
        const $form = $(this);
        $.ajax({
            url: $form.prop("action"),
            method: $form.prop("method"),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $form.serialize(),
            dataType: "json",

        }).done((response) => {
            console.log(response);
            document.location = response.url;
        }).fail(($xhr, textStatus, error) => {
            // エラー出力を削除
            $(".errors").empty();

            let response = "" + $xhr.responseText;
            if (response !== "") {
                try {
                    response = JSON.parse(response);
                    if (response.message != null) {
                        error = response.message;
                    }
                    console.log(response);
                } catch (ex) {
                    // nop
                }
            }
            if (response.errors) {
                for (const k in response.errors) {
                    $(".errors.target-" + k).append(response.errors[k].join("<br>") + "<br>");
                }
            }
            $(".error-submit").append("[" + textStatus + "]" + error);
        });
        return false;
    });
    $(".preview").on("change", ".answers", function (event) {
        const el = this;
        const $this = $(this);
        const target = $this.data("target");
        const value = $this.val();
        // console.log("change", target);
        $("." + target).each((idx, item) => {
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
        const date = new Date(str);
        return isNaN(date.getTime()) ? "" : Intl.DateTimeFormat("japanese", { dateStyle: "medium", timeStyle: "short" }).format(date);
    }
});
