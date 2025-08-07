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
Der Bell-Score beschreibt die allgemeine Belastbarkeit auf einer Skala von 0–100. Im Formular wird er über einen kurzen Fragebogen ermittelt. 

Der emotionale Zustand wird weiterhin über einen Schieberegler von 0–100 erfasst.

### Berechnungsgrundlage
Der Fragebogen besteht aus vier Fragen. Jede Antwort hat einen fest zugewiesenen Punktwert:

1. **Liegezeit (außer Schlaf)**  
   Fast den ganzen Tag (22–24 h) → 0 Punkte  
   Mehr als 18 Stunden → 10 Punkte  
   Zwischen 12 und 18 Stunden → 20 Punkte  
   Weniger als 12 Stunden → 30 Punkte
2. **Anstrengendste körperliche Aktivität**  
   Nur Toilette/Zähneputzen o. ä. → 0 Punkte  
   Körperpflege & Anziehen → 20 Punkte  
   Kleine Mahlzeit zubereiten, 1–2 kurze Wege in der Wohnung → 30 Punkte  
   Spaziergang, Haushalt, Einkauf oder > 30 Min aufrecht → 40 Punkte
3. **Symptomverschlechterung nach Aktivität (PEM)**  
   Stark – bereits nach kleinster Aktivität → 0 Punkte  
   Deutlich – auch bei einfacher Aktivität → 20 Punkte  
   Leicht – nach moderater Aktivität → 30 Punkte  
   Keine spürbare Verschlechterung → 40 Punkte
4. **Maximale Konzentrationsdauer**  
   Unter 5 Minuten → 10 Punkte  
   5–15 Minuten → 20 Punkte  
   15–30 Minuten → 30 Punkte  
   Über 30 Minuten → 40 Punkte

Auswertung:

1. Punkte aller Antworten addieren
2. Summe durch 4 teilen
3. Ergebnis auf den nächsten 10er-Wert runden

Das Resultat ist der tagesaktuelle Mini-Bell-Score (0–100 in 10er-Schritten).
