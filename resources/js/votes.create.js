$(function () {
    const json = JSON.parse($("#poll-json").text());
    // subject
    $(".poll-subject").loadTemplate(
        "#template-poll-subject", {
        title: json.poll.title,
        expiry: json.poll.expiry,
    });

    // questions
    const questions = [];
    for (let i = 0; i < json.questions.length; i++) {
        const question = json.questions[i];

        // options
        const options = [];
        for (let i = 0; i < json.options.length; i++) {
            const option = json.options[i];
            const $tmp = $(document.createDocumentFragment());
            $tmp.loadTemplate("#template-poll-option", {
                "optionId": "answers[" + question.id + "][" + option.id + "]",
                "optionTag": "answers-" + question.id + "-" + option.id,
                "min": json.poll.point_min,
                "max": json.poll.point_max,
                "answer": (json.answers[question.id] || {})[option.id] || json.point_default,
                "label": option.label,
                "voted": json.voted,
            });
            options[options.length] = $tmp;
        }

        // question
        const $tmp = $(document.createDocumentFragment());
        $tmp.loadTemplate("#template-poll-question", {
            label: question.label,
        });
        $tmp.find(".options").append(options);
        questions[questions.length] = $tmp;
    }

    // body
    const $body = $($("#template-poll-body")[0].content).clone();
    $body.find(".questions").append(questions);
    $(".poll-body").append($body);

    // set event handler
    $("#form").on("submit", function (event) {
        event.preventDefault();
        $(".errors").empty();
        const $form = $(this);
        $.ajax({
            url: $form.prop("action"),
            method: $form.prop("method"),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: $form.serialize(),
            dataType: "json",
        }).done((response) => {
            console.log(response);
            document.location = response.url;
        }).fail(($xhr, textStatus, error) => {
            let response = "" + $xhr.responseText;
            if (response !== "") {
                try {
                    response = JSON.parse(response);
                    if (response.message != null) {
                        error = response.message;
                    }
                    console.log(response);
                } catch (ex) {
                }
            }
            $(".errors").empty();
            if (response.errors) {
                for (const k in response.errors) {
                    $(".errors.target-" + k).append(response.errors[k]);
                }
            }

            $(".error-submit").append("[" + textStatus + "]" + error);

        });
        return false;
    }).on("change", ".answers", function (event) {
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
});
