# MECFS Tracker

WordPress-Plugin zum täglichen Erfassen von Bell-Score, emotionalem Zustand, Symptomen und Notizen.

## Features
- Frontend-Formular als Gutenberg-Block für Tagesprotokolle
- Speicherung der Daten in eigenen Datenbanktabellen
- Gutenberg-Blöcke für Formular, Diagramm und Export-Button
- Export der eigenen Einträge als CSV über den Export-Block
- REST-API-Endpunkt `/wp-json/mecfs/v1/entries` für Diagramme und externe Abfragen

## Konfiguration
- **Tabellenbereinigung:** Unter *Einstellungen → MECFS Tracker* kann festgelegt werden, ob die Datenbanktabellen bei Deaktivierung des Plugins gelöscht werden (`mecfs_tracker_cleanup`).

## Bell-Score und emotionaler Zustand
Der Bell-Score beschreibt die allgemeine Belastbarkeit auf einer Skala von 0–100. Im Formular wird er über einen kurzen Fragebogen ermittelt. 

Der emotionale Zustand wird weiterhin über einen Schieberegler von 0–100 erfasst.

### Berechnungsgrundlage
Der Fragebogen besteht aus fünf Fragen, die jeweils von 0 (schlechtester Zustand) bis 4 (bester Zustand) bewertet werden:

1. Wie belastbar fühlen Sie sich heute körperlich?
2. Wie gut können Sie heute mentale Aufgaben bewältigen?
3. Wie oft müssen Sie sich heute ausruhen?
4. Wie weit können Sie sich heute außer Haus bewegen?
5. Wie gut sind Ihre Symptome heute kontrollierbar?

Die Antworten werden summiert, durch die maximale Punktzahl (5 Fragen × 4 Punkte = 20) geteilt und auf eine Skala von 0–100 hochgerechnet:

```
Bell-Score = (Summe der Antworten / 20) × 100
```
