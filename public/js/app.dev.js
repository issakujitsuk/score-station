"use strict";

$(document).on("change", ".answers", function (event) {
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