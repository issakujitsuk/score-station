"use strict";$(function(){$("#form").on("input",".for-subject",function(){var e,t;$(".poll-subject").loadTemplate("#template-poll-subject",{title:$("input[name='title']").val(),expiry:(e=$("input[name='expiry']").val(),t=new Date(e),isNaN(t.getTime())?"":Intl.DateTimeFormat("japanese",{dateStyle:"medium",timeStyle:"short"}).format(t))})}).on("input",".for-body",function(){var r={};$(".for-body.list").each(function(e,t){var n=t.name,o=[],a=(""+$(t).val()).trim();""!==a&&a.split(/[\r\n]+/g).forEach(function(e){o[o.length]=e}),r[n]=o}),$(".for-body.value").each(function(e,t){r[t.name]=$(t).val()});for(var e=[],t=0;t<r.questions.length;t++){for(var n=r.questions[t],o=t,a=[],i=0;i<r.options.length;i++){var l=r.options[i],p=i,s=$(document.createDocumentFragment());s.loadTemplate("#template-poll-option",{label:l,optionId:"answers["+o+"]["+p+"]",optionTag:"answers-"+o+"-"+p,min:r.point_min,max:r.point_max,answer:r.point_min}),a[a.length]=s}var m=$(document.createDocumentFragment());m.loadTemplate("#template-poll-question",{label:n}),m.find(".options").append(a),e[e.length]=m}var u=$($("#template-poll-body")[0].content).clone();u.find(".questions").append(e),$(".poll-body").empty().append(u)}).on("submit",function(e){e.preventDefault(),$(".errors").empty();var t=$(this);return $.ajax({url:t.prop("action"),method:t.prop("method"),headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:t.serialize(),dataType:"json"}).done(function(e){console.log(e),document.location=e.url}).fail(function(e,t,n){$(".errors").empty();var o=""+e.responseText;if(""!==o)try{null!=(o=JSON.parse(o)).message&&(n=o.message),console.log(o)}catch(e){}if(o.errors)for(var a in o.errors)$(".errors.target-"+a).append(o.errors[a].join("<br>")+"<br>");$(".error-submit").append("["+t+"]"+n)}),!1}),$(".preview").on("change",".answers",function(e){var n=this,t=$(this),o=t.data("target"),a=t.val();$("."+o).each(function(e,t){n!==t&&$(t).val(a)})}),$("#form .for-preview").trigger("input")});