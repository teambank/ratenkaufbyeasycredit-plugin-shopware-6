!function(e){var t={};function n(r){if(t[r])return t[r].exports;var a=t[r]={i:r,l:!1,exports:{}};return e[r].call(a.exports,a,a.exports,n),a.l=!0,a.exports}n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)n.d(r,a,function(t){return e[t]}.bind(null,a));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/bundles/easycreditratenkauf/",n(n.s="q5WQ")}({"/b4G":function(e,t,n){var r=n("t1Kg");"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,n("SZ7m").default)("87786daa",r,!0,{})},GNhh:function(e,t){var n=Shopware.Component,r=Shopware.Data.Criteria;n.extend("easycredit-payment-status-select","sw-entity-single-select",{methods:{createdComponent:function(){this.criteria.addAssociation("stateMachine"),this.criteria.addAssociation("toStateMachineTransitions.fromStateMachineState"),this.criteria.addFilter(r.equals("stateMachine.technicalName","order_transaction.state")),this.criteria.addFilter(r.multi("OR",[r.equals("toStateMachineTransitions.fromStateMachineState.technicalName","open"),r.equals("technicalName","open")])),this.loadSelected()}}})},IU4U:function(e,t){Shopware.Application.addServiceProviderDecorator("ruleConditionDataProviderService",(function(e){return e.addCondition("cartCartAmountWithoutInterest",{component:"sw-condition-cart-amount",label:"easycredit.rule.cartAmountRule",scopes:["cart"],group:"cart"}),e.addCondition("cartPositionPriceWithoutInterest",{component:"sw-condition-cart-position-price",label:"easycredit.rule.cartPositionPrice",scopes:["cart"],group:"cart"}),e}))},IqQT:function(e,t){e.exports='{% block netzkollektiv_easycredit_actions_test %}\n    <sw-button-process\n            @click="onTest"\n            v-model="isTestSuccessful"\n            :isLoading="isTesting"\n            :disabled="testButtonDisabled">\n        {{ $tc(\'easycredit.settingForm.test\') }}\n    </sw-button-process>\n{% endblock %}\n'},"J+Kl":function(e,t,n){},"J2k+":function(e,t){e.exports='<easycredit-merchant-manager \n    v-if="isEasyCreditPayment && componentType == \'manager\'"\n    :tx-id="transactionId"\n    :date="transactionDate"\n/>\n<easycredit-merchant-status-widget \n    v-else-if="isEasyCreditPayment && componentType == \'status\'"\n    :tx-id="transactionId"\n    :date="transactionDate"\n/>\n'},KSEs:function(e,t,n){var r=n("NO2x");"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,n("SZ7m").default)("0e956dda",r,!0,{})},NO2x:function(e,t,n){},Q80d:function(e,t){e.exports='{% block easycredit_settings_icon %}\n    <span class="sw-settings-item__icon">\n        <svg width="46px" height="46px" viewBox="0 0 46 46" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin-right:-20px;">\n            <defs></defs>\n            <g id="ratenkauf-icon" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\n                <g>\n                    <path d="M46,23.0003853 C46,35.7027693 35.7025967,46 23,46 C10.2966326,46 0,35.7027693 0,23.0003853 C0,10.2972307 10.2966326,0 23,0 C35.7025967,0 46,10.2972307 46,23.0003853" id="blue" fill="#005DA9"></path>\n                    <polygon id="orange" fill="#EC6608" points="19.1197164 22.579685 12 16 12 37 19.1197164 37 19.3713154 37 34 37"></polygon>\n                    <path d="M25.7341311,8 L19.2650884,8 C15.2520812,8 12,11.2829473 12,15.3330708 L12,30 C12,25.9498765 15.2520812,22.6669292 19.2650884,22.6669292 L25.7341311,22.6653539 C29.7471384,22.6653539 33,19.3824066 33,15.3330708 C33,11.2829473 29.7471384,8 25.7341311,8" id="white" fill="#FFFFFF"></path>\n                </g>\n            </g>\n        </svg>\n    </span>\n{% endblock %}'},"R8+i":function(e,t,n){},Rsqd:function(e,t){e.exports='<div class="easycredit-click-and-collect-intro">\n    <a href="https://www.easycredit-ratenkauf.de/click-und-collect/" class="easycredit-click-and-collect-logo" target="_blank">\n        <svg version="1.0" xmlns="http://www.w3.org/2000/svg"\n        width="200" viewBox="0 0 700.000000 394.000000"\n        preserveAspectRatio="xMidYMid meet">\n        <g transform="translate(0.000000,394.000000) scale(0.100000,-0.100000)"\n        fill="#006ab0" stroke="none">\n            <path d="M0 1970 l0 -1970 3500 0 3500 0 0 1970 0 1970 -3500 0 -3500 0 0\n            -1970z m3895 1422 c91 -74 121 -104 123 -124 3 -23 -13 -39 -124 -129 -136\n            -110 -137 -110 -178 -55 l-21 27 35 27 c19 15 46 37 60 49 l25 22 -1437 1\n            -1437 0 -15 -22 c-14 -20 -16 -161 -16 -1315 0 -1280 0 -1293 20 -1313 20 -20\n            33 -20 2565 -20 2532 0 2545 0 2565 20 20 20 20 33 20 1064 l0 1043 -23 34\n            c-28 42 -83 64 -133 54 -30 -5 -61 -30 -158 -125 -108 -106 -120 -121 -108\n            -137 30 -42 63 -126 73 -182 21 -119 -20 -246 -112 -344 -56 -59 -49 -62 -109\n            44 -88 156 -255 322 -422 420 l-91 53 56 52 c82 76 152 107 258 112 93 5 161\n            -11 227 -51 l34 -20 121 121 c129 128 181 161 251 162 91 0 176 -52 215 -133\n            21 -44 21 -44 19 -1115 -3 -1064 -3 -1071 -24 -1098 -11 -15 -33 -37 -48 -48\n            l-27 -21 -2584 0 -2584 0 -27 21 c-15 11 -37 33 -48 48 -21 27 -21 32 -24\n            1349 -2 970 1 1329 9 1350 16 38 71 84 116 96 26 7 499 11 1461 11 l1424 0\n            -54 41 c-29 23 -57 46 -62 50 -11 10 40 79 57 79 7 0 66 -44 132 -98z m-1925\n            -888 l5 -369 40 -1 c42 0 47 -7 69 -96 8 -35 -13 -47 -97 -54 -71 -6 -116 12\n            -151 59 -20 27 -21 42 -24 409 -2 237 1 385 7 391 10 10 119 36 136 33 6 -2\n            12 -138 15 -372z m1238 126 l2 -244 120 119 119 118 45 -34 c25 -19 46 -38 46\n            -44 0 -5 -45 -55 -100 -110 l-100 -101 115 -138 c94 -112 114 -142 103 -151\n            -28 -26 -120 -69 -126 -59 -4 6 -54 71 -112 144 l-105 134 -5 -129 -5 -130\n            -77 -3 -78 -3 0 425 0 425 58 11 c31 6 62 13 67 15 27 9 30 -11 33 -245z\n            m-857 211 c51 -52 33 -128 -37 -157 -63 -26 -134 24 -134 94 0 50 46 92 100\n            92 33 0 48 -6 71 -29z m1810 -95 c44 -30 69 -79 69 -139 0 -65 -13 -91 -76\n            -158 l-56 -58 53 -67 54 -66 12 29 c6 15 15 47 19 71 7 47 11 48 81 31 l45\n            -12 -5 -46 c-3 -25 -18 -74 -33 -109 l-27 -62 42 -53 c22 -30 41 -58 41 -63 0\n            -13 -66 -64 -83 -64 -9 0 -29 17 -45 37 -28 36 -31 37 -49 22 -49 -44 -106\n            -63 -193 -63 -71 0 -94 4 -135 24 -75 38 -108 86 -113 164 -2 35 1 80 7 99 11\n            34 68 94 120 127 l24 16 -40 53 c-67 89 -76 154 -31 229 58 95 218 124 319 58z\n            m-2516 -147 c31 -12 59 -28 62 -36 6 -16 -23 -87 -43 -103 -9 -8 -22 -5 -46\n            10 -21 12 -50 20 -80 20 -39 0 -50 -5 -72 -30 -31 -38 -41 -84 -39 -181 2\n            -116 38 -169 116 -169 21 0 56 9 77 20 l40 20 14 -33 c28 -68 28 -74 1 -95\n            -27 -22 -104 -42 -159 -42 -59 0 -131 28 -170 66 -52 51 -77 113 -83 210 -12\n            184 59 313 197 355 48 14 129 9 185 -12z m1272 -7 c23 -12 44 -26 48 -32 7\n            -11 -44 -110 -56 -110 -5 0 -23 9 -41 20 -43 26 -107 26 -140 1 -13 -11 -30\n            -37 -36 -59 -15 -48 -15 -174 -2 -224 21 -75 99 -99 181 -58 l40 21 19 -43\n            c29 -65 26 -76 -27 -101 -68 -30 -192 -30 -248 0 -80 43 -118 102 -136 212 -9\n            54 -9 88 0 142 22 135 83 215 191 250 45 15 162 4 207 -19z m-557 -292 l0\n            -300 -75 0 -75 0 0 240 0 239 -57 3 -58 3 -3 44 c-5 72 -8 71 138 71 l130 0 0\n            -300z m2775 32 c149 -105 265 -235 344 -383 l44 -83 -105 -101 c-77 -75 -124\n            -111 -174 -136 -65 -32 -74 -34 -174 -34 -99 0 -110 2 -172 33 -173 85 -266\n            287 -213 468 25 86 46 117 163 236 l105 107 56 -29 c31 -15 88 -50 126 -78z\n            m-2565 -914 c0 -200 4 -369 9 -377 6 -9 20 -12 40 -9 17 3 35 0 40 -6 11 -14\n            31 -108 25 -117 -9 -15 -107 -31 -147 -25 -53 9 -82 28 -104 69 -16 29 -18 71\n            -21 406 -2 235 1 378 7 384 8 8 103 33 139 36 9 1 12 -78 12 -361z m340 -13\n            l5 -370 40 0 39 0 14 -59 c14 -55 14 -60 -3 -72 -26 -19 -118 -28 -158 -15\n            -35 12 -66 40 -86 78 -8 15 -11 140 -11 402 l0 381 23 5 c82 19 104 23 117 21\n            13 -1 16 -52 20 -371z m1510 190 l0 -95 81 0 82 0 -7 -41 c-12 -73 -19 -79\n            -91 -79 l-65 0 0 -154 c0 -85 5 -167 10 -182 11 -28 23 -29 112 -8 25 6 27 3\n            39 -51 15 -69 8 -78 -79 -96 -104 -21 -176 2 -216 71 -16 28 -20 59 -24 226\n            l-5 194 -33 0 c-57 0 -64 7 -64 66 l0 54 50 0 50 0 0 73 c0 39 4 78 9 86 9 14\n            64 27 124 30 l27 1 0 -95z m-2752 -104 c47 -24 50 -36 22 -91 -26 -51 -33 -54\n            -72 -30 -38 23 -114 26 -140 6 -35 -26 -52 -86 -51 -181 0 -97 14 -141 55\n            -169 31 -22 86 -20 133 4 44 22 46 22 69 -39 16 -42 16 -45 -1 -62 -10 -10\n            -47 -26 -82 -35 -157 -40 -286 32 -327 182 -22 84 -16 208 16 282 26 59 99\n            132 152 150 53 18 176 9 226 -17z m477 10 c55 -25 93 -66 124 -131 23 -49 26\n            -67 26 -170 0 -106 -2 -120 -28 -172 -34 -69 -84 -113 -151 -133 -139 -42\n            -266 20 -317 153 -25 66 -31 210 -11 279 20 69 73 135 132 165 65 35 162 38\n            225 9z m1276 -4 c79 -42 129 -149 129 -279 l0 -58 -176 0 -176 0 6 -37 c10\n            -54 36 -86 85 -103 37 -13 47 -13 104 3 34 10 71 20 83 23 17 5 24 -3 42 -42\n            11 -27 18 -52 16 -56 -11 -17 -81 -46 -139 -58 -117 -24 -233 9 -286 83 -64\n            88 -81 257 -39 372 26 69 96 144 150 162 63 20 152 16 201 -10z m637 -6 c23\n            -12 42 -29 42 -38 0 -16 -47 -103 -55 -103 -3 0 -19 9 -37 20 -50 30 -122 27\n            -153 -6 -40 -44 -55 -154 -34 -253 20 -93 97 -127 183 -80 21 11 41 17 44 14\n            13 -13 41 -99 35 -108 -3 -5 -24 -17 -47 -27 -142 -63 -292 -17 -353 108 -26\n            52 -28 66 -28 172 0 110 2 119 33 182 53 108 126 150 252 145 53 -3 88 -10\n            118 -26z"/>\n            <path d="M5424 2428 c-56 -53 -65 -67 -62 -92 2 -23 10 -33 31 -40 25 -9 32\n            -5 92 54 70 69 84 109 46 130 -32 17 -40 13 -107 -52z"/>\n            <path d="M3970 2650 c-11 -11 -20 -33 -20 -48 0 -33 55 -122 76 -122 7 0 25\n            17 39 37 29 44 34 112 9 137 -22 23 -80 20 -104 -4z"/>\n            <path d="M3953 2298 c-53 -59 -59 -124 -14 -169 24 -24 38 -29 78 -29 57 0\n            118 32 109 56 -9 24 -130 162 -142 163 -6 1 -20 -9 -31 -21z"/>\n            <path d="M1974 1365 c-48 -74 -43 -278 8 -332 29 -31 81 -31 113 1 31 31 38\n            72 33 194 -4 127 -20 155 -91 160 -40 3 -48 0 -63 -23z"/>\n            <path d="M3262 1400 c-24 -10 -52 -64 -52 -100 0 -19 6 -20 88 -20 l89 0 -7\n            46 c-4 25 -13 52 -21 59 -19 19 -69 27 -97 15z"/>\n            </g>\n        </svg>\n    </a>\n\n    <p>\n    Sie wollen Click&Collect für den ratenkauf by easyCredit nutzen? \n    Nichts einfacher als das! Um das Feature nutzen zu können, benötigen Sie eine ergänzende Zusatzvereinbarung zu Ihrem Factoringvertrag. \n    Sprechen Sie dazu einfach Ihren Key-Account-Manager an oder fragen Sie diesen über unser <a href="https://www.easycredit-ratenkauf.de/click-und-collect/" target="_blank">Click&Collect-Formular</a> an.\n    </p>\n    <p>\n    Wichtig für Sie ist, dass Sie bei Nutzung von Click&Collect sicherstellen müssen, dass das gekaufte Produkt ausschließlich an den Vertragspartner übergeben wird. Die Verantwortung für die korrekte Übergabe liegt bei Ihnen als Händler.\n    </p>\n    <p>Bitte stellen Sie im Folgenden die Versandart mit der Sie Click&Collect anbieten ein:</p>\n</div>'},SZ7m:function(e,t,n){"use strict";function r(e,t){for(var n=[],r={},a=0;a<t.length;a++){var i=t[a],o=i[0],s={id:e+":"+a,css:i[1],media:i[2],sourceMap:i[3]};r[o]?r[o].parts.push(s):n.push(r[o]={id:o,parts:[s]})}return n}n.r(t),n.d(t,"default",(function(){return h}));var a="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!a)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var i={},o=a&&(document.head||document.getElementsByTagName("head")[0]),s=null,c=0,d=!1,l=function(){},u=null,p="data-vue-ssr-id",f="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function h(e,t,n,a){d=n,u=a||{};var o=r(e,t);return m(o),function(t){for(var n=[],a=0;a<o.length;a++){var s=o[a];(c=i[s.id]).refs--,n.push(c)}t?m(o=r(e,t)):o=[];for(a=0;a<n.length;a++){var c;if(0===(c=n[a]).refs){for(var d=0;d<c.parts.length;d++)c.parts[d]();delete i[c.id]}}}}function m(e){for(var t=0;t<e.length;t++){var n=e[t],r=i[n.id];if(r){r.refs++;for(var a=0;a<r.parts.length;a++)r.parts[a](n.parts[a]);for(;a<n.parts.length;a++)r.parts.push(y(n.parts[a]));r.parts.length>n.parts.length&&(r.parts.length=n.parts.length)}else{var o=[];for(a=0;a<n.parts.length;a++)o.push(y(n.parts[a]));i[n.id]={id:n.id,refs:1,parts:o}}}}function g(){var e=document.createElement("style");return e.type="text/css",o.appendChild(e),e}function y(e){var t,n,r=document.querySelector("style["+p+'~="'+e.id+'"]');if(r){if(d)return l;r.parentNode.removeChild(r)}if(f){var a=c++;r=s||(s=g()),t=w.bind(null,r,a,!1),n=w.bind(null,r,a,!0)}else r=g(),t=b.bind(null,r),n=function(){r.parentNode.removeChild(r)};return t(e),function(r){if(r){if(r.css===e.css&&r.media===e.media&&r.sourceMap===e.sourceMap)return;t(e=r)}else n()}}var v,C=(v=[],function(e,t){return v[e]=t,v.filter(Boolean).join("\n")});function w(e,t,n,r){var a=n?"":r.css;if(e.styleSheet)e.styleSheet.cssText=C(t,a);else{var i=document.createTextNode(a),o=e.childNodes;o[t]&&e.removeChild(o[t]),o.length?e.insertBefore(i,o[t]):e.appendChild(i)}}function b(e,t){var n=t.css,r=t.media,a=t.sourceMap;if(r&&e.setAttribute("media",r),u.ssrId&&e.setAttribute(p,t.id),a&&(n+="\n/*# sourceURL="+a.sources[0]+" */",n+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(a))))+" */"),e.styleSheet)e.styleSheet.cssText=n;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(n))}}},Wbuv:function(e,t){e.exports="{% block sw_order_detail_content_tabs_general %}\n    {% parent %}\n\n    <sw-tabs-item v-if=\"isEasyCreditPayment\"\n                  :route=\"{ name: 'netzkollektiv.easycredit.payment.detail', params: { id: $route.params.id } }\"\n                  :title=\"$tc('easycredit.header')\">\n        {{ $tc('easycredit.header') }}\n    </sw-tabs-item>\n{% endblock %}"},auoe:function(e,t){e.exports='{% block netzkollektiv_easycredit_payment_detail %}\n    <div class="easycredit-payment-detail">\n        <div v-if="!isLoading">\n            <sw-card :title="$tc(\'easycredit-payment.paymentDetails.cardTitle\')">\n                <template slot="grid">\n                    <sw-card-section>\n                        <sw-container columns="1fr"\n                                      gap="0px 30px">\n                            <easycredit-tx-widget :order="order" componentType="manager" />\n                        </sw-container>\n                    </sw-card-section>\n                </template>\n            </sw-card>\n        </div>\n        <sw-loader v-if="isLoading">\n        </sw-loader>\n    </div>\n{% endblock %}'},bz3U:function(e,t){var n=Shopware.Component,r=Shopware.Data.Criteria;n.extend("easycredit-order-status-select","sw-entity-single-select",{methods:{createdComponent:function(){this.criteria.addAssociation("stateMachine"),this.criteria.addAssociation("toStateMachineTransitions.fromStateMachineState"),this.criteria.addFilter(r.equals("stateMachine.technicalName","order.state")),this.criteria.addFilter(r.multi("OR",[r.equals("toStateMachineTransitions.fromStateMachineState.technicalName","open"),r.equals("technicalName","open")])),this.loadSelected()}}})},cmwc:function(e,t){e.exports='{% block sw_order_detail_base_order_overview_billing_address %}\n    <div class="easycredit-no-edit" v-if="isEasyCreditPayment && isEditing">\n    {% parent %}\n        <dd><strong>Die Rechnungs- und Versandadresse kann bei ratenkauf by easyCredit nicht nachträglich verändert werden.</strong></dd>\n    </div>\n    <template v-else>\n        {% parent %}\n    </template>\n{% endblock %}\n\n{% block sw_order_detail_base_order_overview_shipping_address %}\n    <div class="easycredit-no-edit" v-if="isEasyCreditPayment && isEditing">\n        {% parent %}\n    </div>\n    <template v-else>\n        {% parent %}\n    </template>\n{% endblock %}'},deRj:function(e,t,n){var r=n("J+Kl");"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,n("SZ7m").default)("0463ee47",r,!0,{})},q5WQ:function(e,t,n){"use strict";n.r(t);var r=n("Q80d"),a=n.n(r);Shopware.Component.register("easycredit-settings-icon",{template:a.a}),Shopware.Module.register("netzkollektiv-easycredit",{type:"plugin",name:"EasyCreditRatenkauf",title:"easycredit.general.mainMenuItemGeneral",description:"easycredit.general.descriptionTextModule",version:"1.0.0",targetVersion:"1.0.0",color:"#9AA8B5",icon:"default-action-settings",routes:{index:{component:"easycredit",path:"index",meta:{parentPath:"sw.settings.index"}}},settingsItem:{group:"plugins",to:{name:"sw.extension.config",params:{namespace:"EasyCreditRatenkauf"}},iconComponent:"easycredit-settings-icon",backgroundEnabled:!0,privilege:"system.system_config"}});var i=n("J2k+"),o=n.n(i),s=(n("KSEs"),Shopware),c=s.Component,d=s.Context;c.register("easycredit-tx-widget",{template:o.a,props:{order:Object,componentType:{type:String,default:"status"}},created:function(){this.initRequestConfig()},computed:{isEasyCreditPayment:function(){var e=this.order.transactions;return 1==e.length&&"handler_netzkollektiv_handler"==e[0].paymentMethod.formattedHandlerIdentifier},transactionId:function(){return this.order.transactions[0].customFields.easycredit_transaction_id},transactionDate:function(){return this.order.transactions[0].createdAt}},methods:{initRequestConfig:function(){window.ratenkaufbyeasycreditOrderManagementConfig={endpoints:{get:"api/v2/easycredit/transaction/{transactionId}",capture:"api/v2/easycredit/transaction/{transactionId}/capture",refund:"api/v2/easycredit/transaction/{transactionId}/refund"},request_config:{headers:{"Content-Type":"application/json",Authorization:"Bearer "+d.api.authToken.access}}}}}});var l=n("Rsqd"),u=n.n(l);n("vDxs");Shopware.Component.register("easycredit-click-and-collect-intro",{template:u.a});n("GNhh"),n("bz3U");var p=n("IqQT"),f=n.n(p),h=(n("/b4G"),Shopware.Mixin);Shopware.Component.register("easycredit-test-credentials-button",{template:f.a,mixins:[h.getByName("notification")],inject:["EasyCreditRatenkaufApiCredentialsService"],data:function(){return{isLoading:!1,isTesting:!1,isTestSuccessful:!1,testButtonDisabled:!1}},metaInfo:function(){return{title:this.$createTitle()}},methods:{getConfigComponent:function(){for(var e=this;e.$parent;){if(void 0!==e.currentSalesChannelId)return e;e=e.$parent}},getConfig:function(e){return this.getConfigComponent().actualConfigData[e]},getCurrentSalesChannelId:function(){return this.getConfigComponent().currentSalesChannelId},onTest:function(){var e=this;this.isTesting=!0;var t=this.getCurrentSalesChannelId(),n=this.getConfig(t)["EasyCreditRatenkauf.config.webshopId"]||this.getConfig(null)["EasyCreditRatenkauf.settings.webshopId"],r=this.getConfig(t)["EasyCreditRatenkauf.config.apiPassword"]||this.getConfig(null)["EasyCreditRatenkauf.settings.apiPassword"],a=this.getConfig(t)["EasyCreditRatenkauf.config.apiSignature"]||this.getConfig(null)["EasyCreditRatenkauf.settings.apiSignature"];this.EasyCreditRatenkaufApiCredentialsService.validateApiCredentials(n,r,a).then((function(t){t.credentialsValid&&(e.isTesting=!1,e.isTestSuccessful=!0)})).catch((function(t){if(t.response.data&&t.response.data.errors){var n="".concat(e.$tc("easycredit.settingForm.messageTestError"),"<br><ul>");t.response.data.errors.forEach((function(e){n="".concat(n,"<li><strong>").concat(e.detail,"</strong></li>")})),n+="</li>",e.createNotificationError({title:e.$tc("easycredit.settingForm.titleSaveError"),message:n}),e.isTesting=!1,e.isTestSuccessful=!1}}))}}});var m=n("Wbuv"),g=n.n(m),y=Shopware,v=y.Component,C=y.Context,w=Shopware.Data.Criteria;v.override("sw-order-detail",{template:g.a,data:function(){return{isEasyCreditPayment:!1}},computed:{showTabs:function(){return!0}},watch:{orderId:{deep:!0,handler:function(){var e=this;if(this.orderId){var t=this.repositoryFactory.create("order"),n=new w(1,1);n.addAssociation("transactions"),n.getAssociation("transactions").addSorting(w.sort("createdAt")),t.get(this.orderId,C.api,n).then((function(t){var n=t.transactions.length,r=n-1;if(n<=0||!t.transactions[r].paymentMethodId)e.setIsEasyCreditPayment(null);else{var a=t.transactions[r].paymentMethodId;null!=a&&(e.setIsEasyCreditPayment(a),e.order=t)}}))}else this.setIsEasyCreditPayment(null)},immediate:!0}},methods:{setIsEasyCreditPayment:function(e){var t=this;e&&this.repositoryFactory.create("payment_method").get(e,C.api).then((function(e){t.isEasyCreditPayment="handler_netzkollektiv_handler"===e.formattedHandlerIdentifier}))}}});var b=n("rg0l"),S=n.n(b),k=Shopware,_=k.Component;k.Context,Shopware.Data.Criteria;_.override("sw-order-detail-base",{template:S.a,computed:{isEasyCreditPayment:function(){var e=this.order.transactions;return 1==e.length&&"handler_netzkollektiv_handler"==e[0].paymentMethod.formattedHandlerIdentifier}}});var x=n("cmwc"),M=n.n(x),E=(n("deRj"),Shopware),z=E.Component;E.Context,Shopware.Data.Criteria;z.override("sw-order-user-card",{template:M.a,computed:{isEasyCreditPayment:function(){var e=this.currentOrder.transactions;return 1==e.length&&"handler_netzkollektiv_handler"==e[0].paymentMethod.formattedHandlerIdentifier}}});var I=n("auoe"),P=n.n(I),T=Shopware,R=T.Component,A=(T.Filter,T.Mixin),O=T.Context,D=Shopware.Data.Criteria;function F(e){return(F="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function j(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function N(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function B(e,t){return(B=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function q(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=L(e);if(t){var a=L(this).constructor;n=Reflect.construct(r,arguments,a)}else n=r.apply(this,arguments);return U(this,n)}}function U(e,t){return!t||"object"!==F(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function L(e){return(L=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}R.register("easycredit-payment-detail",{template:P.a,mixins:[A.getByName("notification")],inject:["repositoryFactory"],watch:{$route:function(){this.resetDataAttributes(),this.createdComponent()}},data:function(){return{isLoading:!0,order:null}},created:function(){this.createdComponent()},methods:{createdComponent:function(){var e=this,t=this.$route.params.id,n=this.repositoryFactory.create("order"),r=new D(1,1);r.addAssociation("transactions.stateMachineState"),r.addAssociation("transactions.paymentMethod"),r.getAssociation("transactions").addSorting(D.sort("createdAt")),n.get(t,O.api,r).then((function(t){e.order=t,e.isLoading=!1}))}}}),Shopware.Module.register("easycredit-payment",{type:"plugin",name:"EasyCreditRatenkauf",description:"easycredit-payment.general.descriptionTextModule",version:"1.0.0",targetVersion:"1.0.0",color:"#2b52ff",routeMiddleware:function(e,t){"sw.order.detail"===t.name&&t.children.push({component:"easycredit-payment-detail",name:"netzkollektiv.easycredit.payment.detail",isChildren:!0,path:"/sw/order/easycredit/detail/:id"}),e(t)}});var $=Shopware.Classes.ApiService,W=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&B(e,t)}(i,e);var t,n,r,a=q(i);function i(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"easycredit";return j(this,i),a.call(this,e,t,n)}return t=i,(n=[{key:"validateApiCredentials",value:function(e,t,n){var r=this.getBasicHeaders();return this.httpClient.get("_action/".concat(this.getApiBasePath(),"/validate-api-credentials"),{params:{webshopId:e,apiPassword:t,apiSignature:n},headers:r}).then((function(e){return $.handleResponse(e)}))}}])&&N(t.prototype,n),r&&N(t,r),i}($),G=Shopware.Application;G.addServiceProvider("EasyCreditRatenkaufApiCredentialsService",(function(e){var t=G.getContainer("init");return new W(t.httpClient,e.loginService)}));n("IU4U")},rg0l:function(e,t){e.exports='{% block sw_order_detail_base_status_change_order %}\n    {% parent %}\n\n    <easycredit-tx-widget v-if="isEasyCreditPayment" :order="order" />\n{% endblock %}'},t1Kg:function(e,t,n){},vDxs:function(e,t,n){var r=n("R8+i");"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,n("SZ7m").default)("b7dd38ca",r,!0,{})}});