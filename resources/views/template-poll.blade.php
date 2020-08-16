<template id="template-poll-subject">
    <h1 data-content-text="title"></h1>
    <p class="expiry">
        投票締め切り: <span data-content-text="expiry"></span>
    </p>
</template>

<template id="template-poll-body">
    <ol class="questions">
        {{-- template-poll-question --}}
    </ol>
</template>

<template id="template-poll-question">
    <li class="item">
        <h3 data-content-text="label"></h3>
        <ul class="options">
            {{-- template-poll-option --}}
        </ul>
    </li>
</template>

<template id="template-poll-option">
    <li>
        <label data-content-label="label" data-template-bind='[
            {"attribute": "for", "value": "optionId"}
        ]' data-content-text="label"></label>

        <input type="range" class="answers" data-class="optionTag" data-value="answer" data-template-bind='[
            {"attribute": "min", "value": "min"},
            {"attribute": "max", "value": "max"},
            {"attribute": "data-target", "value": "optionTag"},
            {"attribute": "disabled", "value": "voted"}
        ]' tabindex="-1">

        <input type="number" class="answers" data-id="optionId" data-class="optionTag" data-value="answer" data-template-bind='[
            {"attribute": "min", "value": "min"},
            {"attribute": "max", "value": "max"},
            {"attribute": "data-target", "value": "optionTag"},
            {"attribute": "name", "value": "optionId"},
            {"attribute": "readonly", "value": "voted"}
        ]' required>
    </li>
</template>
