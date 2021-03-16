# 1.1.2

* Verbesserung der Multichannel-Kompatibilität (behebt einen Fehler im Checkout bei mehreren SalesChannel mit unterschiedlichen Einstellungen)
* Verbesserung der Fehler-Toleranz bei unerwarteten Rückgabewerten der API
* das Ratenkauf Widget loggt Betragsunterschreitungen nicht mehr als Fehler

# 1.1.1

* Version 1.1.0 konnte nicht installiert werden (Composer Version Constraint)

# 1.1.0

* Verbesserung der Kompatibilität mit dem Shopware Rule Builder

# 1.0.0

* Bestellungen werden nach Abschluss statt als "Bezahlt" als "Authorisiert" markiert (erst nach Meldung der Lieferung im Händler-Portal ist die Bestellung bezahlt)
* der Lieferstatus wird nun an das Händler-Portal übermittelt (order_delivery.state.shipped, order_delivery.state.returned)
* die Konfiguration wurde zur Standardisierung auf config.xml migriert 
* API: das Feld Kategorie wird nach 255 Zeichen abgeschnitten, um einen Fehler bei zu langen Kategorienamen zu vermeiden

# 0.9.8

* Integration des Händler-Interface Widgets
* Verbesserung der Validierung (Firma, abweichende Adresse, Betragsgrenzen)
* die Zinsen enthalten nun 0% Steuern (vorher keine Steuerdefinition)
* die Zinsen können nun automatisch entfernt werden (Standardeinstellung: entfernen)
* das Debug-Logging bei Weiterleitung zum Payment Terminal wurde verbessert
* Weiterleitung auf das Payment Terminal erfolgt nur nach erfolgreicher Validierung
* die Adresse kann in der Administration für ratenkauf by easyCredit Bestellungen nicht mehr angepasst werden
* das Widget stellt den Preis über einen meta-Tag zur Verfügung (vorher: Erkennung über itemprop="price")
* Debug-Logging kann über die Plugin-Einstellungen aktiviert werden
* Anpassung von Übersetzungen

# 0.9.7

* Fehlerbehebung in Zahlartenauswahl in Zusammenspiel mit anderen Plugins

# 0.9.6

* Kompatibilität mit Shopware 6.3

# 0.9.5

* die Bestellnummer wird zur einfacheren Bestellbearbeitung an easyCredit übermittelt
* das Plugin verwendet nun v2 der easyCredit API
* behebt einen Fehler in der Zahlartenauswahl

# 0.9.4

* Verbesserung des Error Handlings bei fehlenden oder inkorrekten Zugangsdaten & Server-Fehlern
* Entfernen der Zahlungsmethode easyCredit bei Fehlern aus dem Checkout
* Entfernen von Zahlungsmethode und Widget, wenn im Sales Channel nicht zugeordnet

# 0.9.3

* Anpassungen gemäß Shopware Quality Guide

# 0.9.1

* erstes Release für Shopware 6.1
