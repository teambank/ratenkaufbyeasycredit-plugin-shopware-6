.. role:: latex(raw)
   :format: latex

Installation
============

Die Extension für easyCredit-Ratenkauf kann in der Administration unter :menuselection:`Einstellungen --> System --> Plugins` entweder über den direkten Download aus dem *Shopware Community Store* oder über den Datei-Upload des bereitgestellten Archives über *Plugin Hochladen* installiert werden.
Alternativ ist auch die Installation über die Kommandozeile möglich.

Shopware Community Store
------------------------

Sie finden das Plugin im Shopware Community Store unter der folgenden URL:
https://store.shopware.com/easyc36021249341f/ratenkauf-by-easycredit.html

.. image:: ./_static/installation-community_store.png

Legen Sie das Plugin in den Warenkorb und kaufen Sie es kostenlos unter der ihrer Lizenzdomain. In der Shopware-Administration sollte das Plugin nun unter :menuselection:`Einstellungen --> System --> Plugins --> Einkäufe` zu finden sein:

.. image:: ./_static/installation-licence.png

Das Plugin wird Ihnen nun unter "Meine Plugins" zur Installation angezeigt. Installieren Sie das Plugin durch Klick auf **Installieren**. Fahren Sie anschließend mit der :ref:`configuration` fort.

manueller Datei-Upload
---------------------------------

Navigieren Sie in der Shopware-Administration zu :menuselection:`Einstellungen --> System --> Plugins`. Klicken Sie dort oben rechts auf den Button *Plugin hochladen*. Wählen Sie den lokalen Pfad aus, unter dem sich das ZIP-Archiv des Shopware Plugins befindet und klicken Sie anschließend auf *Plugin hochladen*.

Fahren Sie anschließend mit der :ref:`configuration` fort.

.. image:: ./_static/installation-file_upload.png

Kommandozeile
-------------

Um das Plugin über die Kommandozeile zu installieren, entpacken Sie das Plugin nach :

.. code-block:: console

    $ unzip EasyCreditRatenkauf-x.x.x.zip -d custom/plugins/

Um sicher zu gehen, überprüfen Sie, ob das folgende Verzeichnis existiert: ``custom/plugins/EasyCreditRatenkauf``. Im Anschluss installieren und aktivieren Sie das Plugin mit den folgenden Befehlen:

.. code-block:: console

    $ cd /sw-base-dir
    $ ./bin/console plugin:refresh
    $ ./bin/console plugin:install EasyCreditRatenkauf
    $ ./bin/console plugin:activate EasyCreditRatenkauf

Fahren Sie anschließend mit der :ref:`configuration` fort.

..
..  Sollten Ihnen die Zugangsdaten bereits vorliegen, können Sie diese gleich bei der Installation mit den folgenden Befehlen setzen:
..
.. //code-block:: console
..
..    $ ./bin/console sw:plugin:config:set NetzkollektivEasyCredit easycreditApiKey 1.de.1234.4321
..  $ ./bin/console sw:plugin:config:set NetzkollektivEasyCredit easycreditApiToken abc-def-ghi
