!function(e){var t={};function o(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return e[n].call(i.exports,i,i.exports,o),i.l=!0,i.exports}o.m=e,o.c=t,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)o.d(n,i,function(t){return e[t]}.bind(null,i));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="",o(o.s=5)}({5:function(e,t){jQuery(document).ready((function(e){e("body").hasClass("job-application-details-keep-open")||e(".application_details").hide(),e(document.body).on("click",".job_application .application_button",(function(){var t=e(this).parents(".job_application").find(".application_details").first(),o=e(this);t.slideToggle(400,(function(){if(e(this).is(":visible")){t.trigger("visible");var n=Math.max(Math.min(t.outerHeight(),200),.33*t.outerHeight()),i=t.offset().top+n,r=5;e("#wpadminbar").length>0&&"fixed"===e("#wpadminbar").css("position")&&(r+=e("#wpadminbar").outerHeight()),e("header").length>0&&"fixed"===e("header").css("position")&&(r+=e("header").outerHeight());var a=e(window).scrollTop()+window.innerHeight,l=t.offset().top+t.outerHeight()-a,u=window.innerHeight-r;l>0&&t.outerHeight()<.9*u?e("html, body").animate({scrollTop:e(window).scrollTop()+l+5},400):a<i&&e("html, body").animate({scrollTop:o.offset().top-r},600)}}))}))}))}});