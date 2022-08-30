.. role:: latex(raw)
   :format: latex

Häufige Fragen
============================

Im Checkout wird die Ratenauswahl und das Zahlartenlogo nicht angezeigt
--------------------------------------------------------------------------------

Das easyCredit-Ratenkauf Plugin ermöglicht die Ratenauswahl nach Auswahl der Zahlungsart und zeigt einen Button "Weiter zum Ratenkauf" an. Wird dieser Button nicht angezeigt, so überschreibt möglicherweise ein anderes Plugin das Template `storefront/component/payment/payment-method.html.twig` in inkompatibler Weise. Die Template-Vererbung funktioniert in diesem Fall nicht korrekt und das für das easyCredit-Ratenkauf Plugin spezifische Template wird nicht geladen. Bitte prüfen Sie, ob alle Plugins und Ihr Theme, die das Template `payment-method.html.twig` überschreiben oder erweitern, einen Aufruf auf das parent-Template durchführen.

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

Möglicherweise ist in der Installation eine Regel über den Rule Builder definiert, die die Zinsposition in den Bedingungen miteinbezieht. Das Plugin stellt hierfür zwei alternative Bedingungen zur Verfügung, welche die Zinsen aus der Summe bzw. Gesamtsumme herausrechnen. Somit lassen sich zinsunabhängige Regeln erstellen, welche mit easyCredit-Ratenkauf funktionieren. Die beiden Bedingungen sind wie folgt bezeichnet:

  * Summe, inkl. Zinsen (kompatibel mit easyCredit-Ratenkauf) 
  * Gesamtsumme, inkl. Zinsen (kompatibel mit easyCredit-Ratenkauf)
