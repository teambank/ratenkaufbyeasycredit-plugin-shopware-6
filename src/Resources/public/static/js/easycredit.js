(() => {
  // esbuild/src/plugin-system/pluginmanager.class
  var PluginManager = class {
    register(pluginName, pluginClass, selector = document, options = {}) {
      window.addEventListener("DOMContentLoaded", (event) => {
        const el = document.querySelector(selector);
        if (el) {
          const plugin = new pluginClass(el);
          plugin.init();
        }
      });
    }
  };

  // esbuild/src/plugin-system/plugin.class
  var Plugin = class {
    constructor(el, options = {}, pluginName = false) {
      this.el = el;
    }
    init() {
      throw new Error(`The "init" method for the plugin is not defined.`);
    }
  };

  // src/Resources/app/storefront/src/checkout/checkout.js
  var EasyCreditRatenkaufCheckout = class extends Plugin {
    init() {
      function createHiddenField(name, value) {
        var el = document.createElement("input");
        el.setAttribute("type", "hidden");
        el.setAttribute("name", `easycredit[${name}]`);
        el.setAttribute("value", value);
        return el;
      }
      document.querySelector("easycredit-checkout")?.addEventListener("submit", (e) => {
        var form = document.getElementById("changePaymentForm");
        form.append(createHiddenField("submit", "1"));
        form.append(createHiddenField("number-of-installments", e.detail.numberOfInstallments));
        form.append(createHiddenField("agreement-checked", e.detail.privacyCheckboxChecked));
        form.submit();
        return false;
      });
    }
  };

  // src/Resources/app/storefront/src/checkout/express.js
  var EasyCreditRatenkaufExpressCheckout = class extends Plugin {
    init() {
      this.el.addEventListener("submit", () => {
        var form;
        if (form = this.replicateBuyForm()) {
          form.submit();
          return;
        }
        if (document.querySelector(".is-ctl-checkout.is-act-cartpage")) {
          window.location.href = "/easycredit/express";
          return;
        }
        alert("Der easycredit-Ratenkauf konnte nicht gestartet werden.");
      });
    }
    replicateBuyForm() {
      let buyForm = document.getElementById("productDetailPageBuyProductForm");
      if (!buyForm) {
        return false;
      }
      var form = document.createElement("form");
      form.setAttribute("action", buyForm.getAttribute("action"));
      form.setAttribute("method", "post");
      form.style.display = "none";
      var formData = new FormData(buyForm);
      formData.set("redirectTo", "frontend.easycredit.express");
      formData.set("easycredit-express", "1");
      for (var key of formData.keys()) {
        let field = document.createElement("input");
        field.setAttribute("name", key);
        field.setAttribute("value", formData.get(key));
        form.append(field);
      }
      document.querySelector("body").append(form);
      return form;
    }
  };

  // src/Resources/app/storefront/src/widget/widget.js
  var EasyCreditRatenkaufWidget = class extends Plugin {
    init() {
      this.initWidget();
    }
    initWidget() {
      const selector = this.getMeta("widget-selector");
      if (selector === null || this.getMeta("api-key") === null) {
        return;
      }
      this.el = document.querySelector(selector);
      let amount = this.getMeta("amount");
      if (null === amount || isNaN(amount)) {
        const priceContainer = this.el.parentNode;
        amount = priceContainer.querySelector("[itemprop=price]") ? priceContainer.querySelector("[itemprop=price]").content : null;
      }
      if (null === amount || isNaN(amount)) {
        return;
      }
      let widget = document.createElement("easycredit-widget");
      widget.setAttribute("webshop-id", this.getMeta("api-key"));
      widget.setAttribute("amount", this.getMeta("amount"));
      this.el.appendChild(widget);
    }
    getMeta(key) {
      const meta = document.querySelector("meta[name=easycredit-" + key + "]");
      if (meta === null) {
        return null;
      }
      return meta.content;
    }
  };

  // src/Resources/app/storefront/src/marketing/marketing.js
  var EasyCreditRatenkaufMarketing = class extends Plugin {
    init() {
      this.initMarketing();
    }
    initMarketing() {
      this.body = document.querySelector("body");
      this.bar = document.querySelector("easycredit-box-top");
      if (this.bar) {
        this.body.classList.add("easycredit-box-top");
      }
      this.card = document.querySelector(".easycredit-box-listing");
      if (this.card) {
        var siblings = (n) => [...n.parentElement.children].filter((c) => c != n);
        var siblingsCard = siblings(this.card);
        var position = this.card.querySelector("easycredit-box-listing").getAttribute("position");
        var previousPosition = typeof position === void 0 ? null : Number(position - 1);
        var appendAfterPosition = typeof position === void 0 ? null : Number(position - 2);
        if (!position || previousPosition <= 0) {
        } else if (appendAfterPosition in siblingsCard) {
          siblingsCard[appendAfterPosition].after(this.card);
        } else {
          this.card.parentElement.append(this.card);
        }
      }
    }
  };

  // src/Resources/app/storefront/src/_main.js
  var PluginManager2 = new PluginManager();
  PluginManager2.register("EasyCreditRatenkaufCheckout", EasyCreditRatenkaufCheckout, ".is-ctl-checkout.is-act-confirmpage");
  PluginManager2.register("EasyCreditRatenkaufCheckoutExpress", EasyCreditRatenkaufExpressCheckout, "easycredit-express-button");
  PluginManager2.register("EasyCreditRatenkaufWidget", EasyCreditRatenkaufWidget, "body");
  PluginManager2.register("EasyCreditRatenkaufMarketing", EasyCreditRatenkaufMarketing, "body");
})();
