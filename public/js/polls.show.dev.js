"use strict";

window.addEventListener("load", function () {
  var $el = $(".qrcode");
  $el.qrcode({
    ecLevel: 'H',
    minVersion: 6,
    text: $el.data("url"),
    quiet: 4,
    mode: 1,
    // label strip
    label: $el.data("label"),
    fontname: $el.css("font-family"),
    fontcolor: $el.css("color")
  });
});
$(function () {
  $(document).on("click", ".share", function () {
    $(this).select();
  });
});