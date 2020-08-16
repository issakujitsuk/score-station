$(document).on("change", ".answers", function (event) {
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