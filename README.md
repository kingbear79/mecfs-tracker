# MECFS Tracker

WordPress-Plugin zum täglichen Erfassen von Bell-Score, emotionalem Zustand, Symptomen und Notizen.

## Features
- Frontend-Formular mit Shortcode `[mecfs_tracker_form]` für Tagesprotokolle
- Speicherung der Daten in eigenen Datenbanktabellen
- Gutenberg-Blöcke für Formular und Diagramm
- Export der eigenen Einträge als CSV über `[mecfs_export_button]`
- REST-API-Endpunkt `/wp-json/mecfs/v1/entries` für Diagramme und externe Abfragen

## Konfiguration
- **Tabellenbereinigung:** Unter *Einstellungen → MECFS Tracker* kann festgelegt werden, ob die Datenbanktabellen bei Deaktivierung des Plugins gelöscht werden (`mecfs_tracker_cleanup`).

## Bell-Score und emotionaler Zustand
Der Bell-Score beschreibt die allgemeine Belastbarkeit auf einer Skala von 0–100. Für diese erste Version erfolgt die Eingabe manuell über einen Schieberegler.

Der emotionale Zustand wird ebenfalls über einen Schieberegler von 0–100 erfasst. Eine detaillierte Berechnungsgrundlage kann in späteren Versionen durch Fragebögen ergänzt werden.
