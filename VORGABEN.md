# Allgemeine Beschreibung
"mecfs-tracker" ist ein Wordpress-Plugin, welches es angemeldeten Benutzern ermöglicht, täglich Daten zur Belastbarkeit, dem emotionalen Zustand und ihren Symptomen inkl. deren Schwere abzulegen. Es bietet darüber hinaus die Möglichkeit, zu jedem Tag individuelle Besonderheiten zu speichern. In Summe gibt das dem Nutzer die Möglichkeit, Verlauf und Dynamik seiner Erkrankung zu protokollieren, besondere Belastungen zu erkennen und das persönliche Pacing besser zu organisieren.

# Anforderungen an das Plugin
## erfasste Daten
Für jeden Tag sollen folgende Daten erfasst werden:
- der Bell-Score
- Angaben zum emotionalen Zustand
- Art und Schwere der Symptome
- Besonderheiten (besondere Ereignisse, Termine, Medikamente etc.)
- "was hat mir gutgetan?"
- "was hat mich besonders belastet?"

Der Bell-Score soll anhand möglichst weniger und einfach zu beantwortender Fragen ermittelt werden. Gleiches gilt für den emotionalen Zustand. Fragen und und Antwortmöglichkeiten sollten so gewählt sein, dass auch schwer Betroffene mit stärkeren kognitiven Einschränkungen sie ohne besondere Anstrengung beantworten können.
Benutzer sollen in ihren Einstellungen auch eigene Symptome anlegen können, die dann täglich protokolliert werden. Die wichtigsten Symptome von ME/CFS sollten bereits angelegt sein. Zu jedem Symptom soll auch der Schweregrad auf einer Skala erfasst werden.

Bereite die Daten so auf, dass sie möglichst immer auf einer Skala von 1 - 100 dargestellt werden können.

## technische Umsetzung
Die Daten sollen in einem möglichst einfach und intuitiv zu bedienenden, optisch ansprechenden und zeitgemäßem Fragebogen erfasst werden. Die Verwendung verbreiteter Layout-Tools wie Bootstrap oder die Orientierung des Layouts an verbreiteten mobilen Apps ist anzustreben. Benutzer sollen die Möglichkeit haben, auch Daten in der Vergangenheit zu erfassen und bestehende Einträge jederzeit editieren zu können. Wo immer Möglich, sollten für Werte, welche auf einer Skala angegeben werden können, entsprechende Schieberegler als Eingabemöglichkeit verwendet werden. Vermeide grundsätzlich Dropdowns, um eine bessere Les- und Bedienbarkeit sicherzustellen.

Die erfassten Daten sollen in einer relationalen Datenbank erfasst werden. Dabei soll die Tabellenstruktur so angelegt sein, dass bestehende Daten zu jeder Zeit wieder aufgerufen und editiert werden können. Die benötigten Tabellen sollen bei der Plugin-Aktivierung angelegt werden. Das Plugin soll über eine Einstellungs-Seite im Admin-Bereich verfügen, wo festgelegt werden kann ob die Tabellen bei Deaktivierung des Plugins gelöscht werden können. Auch soll hier die Möglichkeit bestehen sämtliche Daten eines Nutzers strukturiert zu exportieren oder zu löschen, um den Anforderungen der DSGVO gerecht zu werden.

Die erfassten Daten sollen im Frontend auch grafisch aufbereitet werden. Das gilt insbesondere für Bell-Score, emotionalen Zustand und die Symptome. Der Benutzer soll dabei die Möglichkeit haben dynamisch zu wählen, welche Parameter im Diagramm gezeigt werden und welcher Zeitraum angezeigt wird.

Es soll für den Benutzer eine Möglichkeit geschaffen werden, seine gespeicherten Daten zu exportieren (als Excel, CSV und PDF). Das dient zur Unterstützung bei Arztbesuchen und zur Dokumentation des Verlaufs gegenüber Behörden und Kostenträgern. Auch eine Importmöglichkeit aus Excel soll geschaffen werden, da viele ME/CFS-Betroffene bereits eigene Dokumentationsmöglichkeiten nutzen. Eine Beispieldatei mit Musterdaten zum Download vereinfacht den Export. Import- und Exportmöglichkeiten sollen im Frontend erreichbar sein, da normale Benutzer keinen Zugang zum Backend haben werden.

Alle Formulare und Anzeigen sollen als Blöcke im Gutenberg-Editor verfügbar sein:
- Formular zur Datenerfassung und -bearbeitung
- Eingabe für Datums-Auswahl als separater Block, dessen ausgewähltes Datum die Anzeige im Erfassungsformular dynamisch steuert
- Grafische Darstellung der erfassten Werte
- Export-Buttons
- Import-Formular

Wo immer es möglich ist, sollen Daten-Transaktionen asynchron über Javascript ausgeführt werden, um häufiges Neuladen der Seite zu vermeiden.

Im Layout sollen wo immer möglich die Vorgaben des jeweiligen Wordpress-Templates berücksichtigt werden.

## Code-Erstellung
Bitte erstelle möglichst "aufgeräumten" Code: wann immer möglich sollen Funktionen sauber in Klassen organisiert werden. Ein eigener Namespace soll Kollisionen mit anderen Plugins vermeiden. Kommentare im Code, insbesondere an Stellen die eine weitere Bearbeitung erfordern, sollen präzise, selbsterklärend und trotzdem so kompakt wie möglich sein

## Dokumentation
Fasse alle Funktionen des Plugins in der Readme zusammen. Erstelle dort auch eine Dokumentation der Konfigurationsmöglichkeiten des Plugins. Ergänze die Readme mit einer Erläuterung der Berechnungsgrundlage für Bellscore und emotionalen Zustand. 

