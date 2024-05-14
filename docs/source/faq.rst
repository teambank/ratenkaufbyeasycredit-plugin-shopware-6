.. role:: latex(raw)
   :format: latex

Häufige Fragen
============================

Im Checkout wird die Ratenauswahl und das Zahlartenlogo nicht angezeigt
--------------------------------------------------------------------------------

Die easyCredit-Ratenkauf Erweiterung ermöglicht die Ratenauswahl nach Auswahl der Zahlungsart und zeigt einen Button "Weiter zum Ratenkauf" an. Wird dieser Button nicht angezeigt, so überschreibt möglicherweise eine andere Erweiterung das Template `storefront/component/payment/payment-method.html.twig` in inkompatibler Weise. Die Template-Vererbung funktioniert in diesem Fall nicht korrekt und das für die easyCredit-Ratenkauf Erweiterung spezifische Template wird nicht geladen. Bitte prüfen Sie, ob alle Erweiterungen und Ihr Theme, die das Template `payment-method.html.twig` überschreiben oder erweitern, einen Aufruf auf das parent-Template durchführen.

Das folgende Beispiel zeigt die korrekte Implementierung:

.. code-block:: twig

    {% block component_payment_method_description %}
      {% if isPaymentMethodXY %}

        <div class="payment-method-description">
          [...]
        </div>

      {% else %}
        {{ parent() }}
      {% endif %}
    {% endblock %}  

Im Bestellvorgang erhält der Kunde nach Berechnung der Raten "Raten müssen neu berechnet werden".
--------------------------------------------------------------------------------------------------

Möglicherweise ist in der Installation eine Regel über den Rule Builder definiert, die die Zinsposition in den Bedingungen miteinbezieht. Die Erweiterung stellt hierfür zwei alternative Bedingungen zur Verfügung, welche die Zinsen aus der Summe bzw. Gesamtsumme herausrechnen. Somit lassen sich zinsunabhängige Regeln erstellen, welche mit easyCredit-Ratenkauf funktionieren. Die beiden Bedingungen sind wie folgt bezeichnet:

  * Summe, inkl. Zinsen (kompatibel mit easyCredit-Ratenkauf) 
  * Gesamtsumme, inkl. Zinsen (kompatibel mit easyCredit-Ratenkauf)

In Warenkorb & Bestellbestätigung wird eine Steuer-Position mit 0% MwSt. angezeigt
-----------------------------------------------------------------------------------

Für eine transparente Darstellung der anfallenden Zinsen beim Ratenkauf wird dem Warenkorb eine separate Position "Zinsen für Ratenzahlung" hinzugefügt. Da auf diese Position keine Umsatzsteuer berechnet wird, zeigt Shopware eine Steuer-Position mit 0% MwSt. an. Soll diese Position nicht angezeigt werden, kann dies durch eine Template-Überschreibung erreicht werden:

.. code-block:: twig

    {% sw_extends '@Storefront/storefront/page/checkout/summary/summary-tax.html.twig' %}

    {% block page_checkout_summary_tax %}

        {% if taxItem.taxRate != 0 && taxItem.tax != 0 %}
            {{ parent() }}
        {% endif %}

    {% endblock %}

In den E-Mail Templates lässt sich diese Position über eine Anpassung des Templates für die Bestellbestätigung entfernen:

.. code-block:: twig

    {% for calculatedTax in order.price.calculatedTaxes %}
        {% if calculatedTax.taxRate != 0 && calculatedTax.tax != 0 %}
            {% if order.taxStatus is same as('net') %}plus{% else %}including{% endif %} {{ calculatedTax.taxRate }}% VAT. {{ calculatedTax.tax|currency(currencyIsoCode) }}<br>
        {% endif %}
    {% endfor %}

Da es sich an dieser Stelle im Template nicht zweifelsfrei feststellen lässt, ob das easyCredit-Plugin für die Position ursächlich ist, haben wir diese Template-Überschreibung nicht ins Plugin aufgenommen.
