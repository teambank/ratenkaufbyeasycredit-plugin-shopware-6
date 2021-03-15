(window.webpackJsonp=window.webpackJsonp||[]).push([["easy-credit-ratenkauf"],{"3NPC":function(e,t,n){"use strict";n.r(t);var r=n("8XvI"),o=n("FGIj"),i=n("PC24"),a=n.n(i);function u(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var n=[],r=!0,o=!1,i=void 0;try{for(var a,u=e[Symbol.iterator]();!(r=(a=u.next()).done)&&(n.push(a.value),!t||n.length!==t);r=!0);}catch(e){o=!0,i=e}finally{try{r||null==u.return||u.return()}finally{if(o)throw i}}return n}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance")}()}function c(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function s(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function l(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var f=function(){function e(t,n){var r=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),l(this,"prepareWidgetData",(function(e){if(!e||e.wsMessages.messages.length>0)return Promise.reject();var t={number_of_installments:e.anzahlRaten,amount:r._formatAmount(e.betragRate),currency_symbol:r.opts.currencySymbol,suffix:r.opts.suffix,link_text:r.opts.linkText};return t.installmentTemplate=r._template(r.opts.installmentTemplate,t),Promise.resolve(t)})),l(this,"renderWidget",(function(e){var t=r._template(r.opts.widgetTemplate,e),n=document.createElement("div");n.innerHTML=t,r.element.parentNode.insertBefore(n,r.element.nextSibling),r.addStyles(),n.querySelector("a").addEventListener("click",r.showModal)})),l(this,"addStyles",(function(){var e=document.createElement("style");e.innerHTML="\n            .easycredit-widget {\n                display: block;\n                color: #000;\n                font-size: 13px;\n                padding: 10px;\n                display: inline-block;\n                background-color:#fff;\n                background-image: url(https://static.easycredit.de/content/image/logo/ratenkauf_42_55.png);\n                background-size: 55px 42px;\n                background-repeat: no-repeat;\n                padding-left: 60px;\n                background-position-y: center;\n                min-width: 200px;\n            }\n            .easycredit-widget .easycredit-rate, \n            .easycredit-widget .easycredit-suffix {\n                font-weight: 700;\n            }\n            .easycredit-widget .easycredit-link {\n                cursor: pointer;\n            }\n            ";var t=document.querySelector("script");t.parentNode.insertBefore(e,t)})),l(this,"showModal",(function(){var e=r._getModalContent(r._getIframeUri(r.opts));r.opts.modal(e)})),this.element=t;var o=this.getDefaults();this.opts=function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?c(n,!0).forEach((function(t){l(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):c(n).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}({},o,{},n),this.validate();var i=this._getApiUri(this.opts);this.getMinimumInstallment(i).then(this.prepareWidgetData).then(this.renderWidget).catch((function(e){return console.debug(e)}))}var t,n,r;return t=e,(n=[{key:"validate",value:function(){var e=this.opts;if(isNaN(e.amount)||e.amount<200||e.amount>1e4){if(e.debug)throw new Error(e.amount+" is not within allowed range")}else if(null==e.webshopId||""==e.webshopId)throw new Error("webshopId must be set for easycredit widget")}},{key:"getMinimumInstallment",value:function(e){var t=this,n={headers:new Headers({"content-type":"application/json; charset=utf-8"})};return a()(e,n).then((function(e){return console.log(t),e.json()}))}},{key:"getDefaults",value:function(){return{hostname:"//ratenkauf.easycredit.de",endpoint:"/ratenkauf-ws/rest/v1/modellrechnung/guenstigsterRatenplan",iframeSrc:"/widget/app/#/ratenwunsch",modal:null,webshopId:null,amount:null,debug:!1,currencySymbol:"&euro;",installmentTemplate:"%amount% %currency_symbol% / Monat",widgetTemplate:['<div class="easycredit-widget">','<span class="easycredit-suffix">%suffix% </span>','<span class="easycredit-rate">%installmentTemplate%</span>',"<br />",'<a class="easycredit-link">%link_text%</a>',"</div>"].join("\n"),suffix:"Finanzieren ab",linkText:"mehr Infos zum Ratenkauf"}}},{key:"_getApiUri",value:function(e){return[e.hostname+e.endpoint,this.param({webshopId:e.webshopId,finanzierungsbetrag:e.amount})].join("?")}},{key:"param",value:function(e){for(var t=new URLSearchParams,n=0,r=Object.entries(e);n<r.length;n++){var o=u(r[n],2),i=o[0],a=o[1];t.set(i,String(a))}return t.toString()}},{key:"_getIframeUri",value:function(e){return[e.hostname+e.iframeSrc,this.param({shopKennung:e.webshopId,bestellwert:e.amount})].join("?")}},{key:"_formatAmount",value:function(e){return Number(Math.round(e+"e2")+"e-2").toFixed(2).replace(".",",")}},{key:"_template",value:function(e,t){return e.replace(/%(\w*)%/g,(function(e,n){return t.hasOwnProperty(n)?t[n]:""}))}},{key:"_getModalContent",value:function(e){return'<iframe class="easycredit-modal" src="'+e+'"></iframe>'}}])&&s(t.prototype,n),r&&s(t,r),e}(),p=n("2Jwc");function d(e){return(d="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function y(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function m(e,t){return!t||"object"!==d(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function h(e){return(h=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function b(e,t){return(b=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}var g=function(e){function t(){return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),m(this,h(t).apply(this,arguments))}var n,r,o;return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&b(e,t)}(t,e),n=t,(r=[{key:"init",value:function(){this.initWidget()}},{key:"initWidget",value:function(){var e=this.getMeta("widget-selector");if(null!==e&&null!==this.getMeta("api-key")){this.el=document.querySelector(e);var t=this.getMeta("amount");if(null===t||isNaN(t)){var n=this.el.parentNode;t=n.querySelector("[itemprop=price]")?n.querySelector("[itemprop=price]").content:null}null===t||isNaN(t)||new f(this.el,{webshopId:this.getMeta("api-key"),amount:t,modal:this.createModal})}}},{key:"getMeta",value:function(e){var t=document.querySelector("meta[name=easycredit-"+e+"]");return null===t?null:t.content}},{key:"createModal",value:function(e){var t=new p.a(e);t.open(),t.getModal().querySelector(".modal-dialog").classList.add("modal-lg")}}])&&y(n.prototype,r),o&&y(n,o),t}(o.a),w=window.PluginManager;w.register("EasyCreditRatenkaufCheckout",r.a,".is-ctl-checkout.is-act-confirmpage"),w.register("EasyCreditRatenkaufWidget",g,"body")},"8XvI":function(e,t,n){"use strict";(function(e){function r(e){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function o(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function i(e,t){return!t||"object"!==r(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function a(e){return(a=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function u(e,t){return(u=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}n.d(t,"a",(function(){return c}));var c=function(t){function n(){return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,n),i(this,a(n).apply(this,arguments))}var r,c,s;return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&u(e,t)}(n,t),r=n,(c=[{key:"init",value:function(){var t=function(){e("#easycredit-payment-form").closest(".payment-method").find(e(this)).length>0?e("#easycredit-agreement").attr("required","required"):e("#easycredit-agreement").removeAttr("required")};e("input[type=radio][name=paymentMethodId]:checked").each(t),e("input[type=radio][name=paymentMethodId]").change(t),e("#easycredit-recalculate").click((function(){return e("#confirmPaymentForm").submit(),!1}))}}])&&o(r.prototype,c),s&&o(r,s),n}(n("FGIj").a)}).call(this,n("UoTJ"))},PC24:function(e,t,n){var r,o,i;o=[t,e],void 0===(i="function"==typeof(r=function(e,t){"use strict";var n={timeout:5e3,jsonpCallback:"callback",jsonpCallbackFunction:null};function r(e){try{delete window[e]}catch(t){window[e]=void 0}}function o(e){var t=document.getElementById(e);t&&document.getElementsByTagName("head")[0].removeChild(t)}t.exports=function(e){var t=arguments.length<=1||void 0===arguments[1]?{}:arguments[1],i=e,a=t.timeout||n.timeout,u=t.jsonpCallback||n.jsonpCallback,c=void 0;return new Promise((function(n,s){var l=t.jsonpCallbackFunction||"jsonp_"+Date.now()+"_"+Math.ceil(1e5*Math.random()),f=u+"_"+l;window[l]=function(e){n({ok:!0,json:function(){return Promise.resolve(e)}}),c&&clearTimeout(c),o(f),r(l)},i+=-1===i.indexOf("?")?"?":"&";var p=document.createElement("script");p.setAttribute("src",""+i+u+"="+l),t.charset&&p.setAttribute("charset",t.charset),p.id=f,document.getElementsByTagName("head")[0].appendChild(p),c=setTimeout((function(){s(new Error("JSONP request to "+e+" timed out")),r(l),o(f),window[l]=function(){r(l)}}),a),p.onerror=function(){s(new Error("JSONP request to "+e+" failed")),r(l),o(f),c&&clearTimeout(c)}}))}})?r.apply(t,o):r)||(e.exports=i)}},[["3NPC","runtime","vendor-node","vendor-shared"]]]);