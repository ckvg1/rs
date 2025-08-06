const dictionary = {
  de: {
    "Wyłącz automatycznie wszystkie światła o:":
      "Schalte alle Lichter automatisch aus um:",
    "Błąd połączenia z PLC": "Verbindungsfehler zum PLC",
    "Błąd połączenia z bazą danych": "Verbindungsfehler zur Datenbank",
    "Zmieniaj nazwy czujników i pomieszczeń":
      "Ändere die Namen von Sensoren und Räumen",
    "Wyłącz wszystkie światła": "Schalte alle Lichter aus ",
    "Włącz wszystkie światła": "Schalte alle Lichter ein",
    "w firmie": "im Unternehmen",
    "w mieszkaniu": "in der Wohnung",
    "Pobierz miesięczny raport temperatury":
      "Lade den monatlichen Temperaturbericht herunter",
    "Pobierz miesięczny raport światła":
      "Lade den monatlichen Lichtbericht herunter",
    "używam danych z: ": "Ich verwende Daten von: ",
    "Brak danych do wyświetlenia wykresu. Proszę podać daty pomiaru.":
      "Keine Daten zum Anzeigen des Diagramms. Bitte geben Sie die Messdaten an.",
    "Wybierz daty pomiaru": "Wählen Sie die Messdaten aus",
    "wybierz zakres dat": "Wählen Sie den Datumsbereich",
    "od-do": "von-bis",
    "wykonania pomiaru": "Messung durchgeführt am",
    "Trwa łączenie z PLC.": "Verbindung zum PLC wird hergestellt.",
    "Sterowanie urządzeniami": "Steuerung von Geräten",
    "spróbuj ponownie": "Versuchen Sie es erneut",
    Ustawienia: "Einstellungen",
    Temperatura: "Temperatur",
    Światło: "Licht",
    Biuro: "Büro",
    Statystyki: "Statistiken",
    piętro: "Stock",
    harmonogram: "Zeitplan",
    raporty: "Berichte",
    filtruj: "Filter",
    Wszystkie: "alle",
    piętra: "Etagen",
    ostatnie: "Letzte",
    godziny: "Stunden",
    "brak danych": "Keine Daten",
    Data: "Datum",
    Zapisz: "Speichern",
    dni: "tagen",
    uwaga: "Achtung",
  },
};

function preserveCase(match, val) {
  // Cały tekst wielkimi literami?
  if (match === match.toUpperCase()) {
    return val.toUpperCase();
  }
  // Tylko pierwsza litera wielka?
  if (match[0] === match[0].toUpperCase()) {
    return val.charAt(0).toUpperCase() + val.slice(1);
  }
  // Pozostaw w oryginalnym kształcie
  return val;
}

function translatePage(lang) {
  if (lang === "pl") {
    return; // język bazowy
  }

  document.querySelectorAll("body *:not(script):not(style)").forEach((el) => {
    el.childNodes.forEach((node) => {
      if (node.nodeType !== Node.TEXT_NODE) return;

      let originalText = node.nodeValue;
      let normalized = originalText
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

      for (const [key, val] of Object.entries(dictionary[lang])) {
        const normalizedKey = key
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "");

        if (!normalized.includes(normalizedKey)) continue;

        // Escape znaków specjalnych w kluczu
        const escKey = key.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
        const regex = new RegExp(escKey, "gi");

        originalText = originalText.replace(regex, (match) =>
          preserveCase(match, val)
        );
      }

      node.nodeValue = originalText;
    });
  });
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i].trim();
    if (c.indexOf(name) === 0) {
      return c.substring(name.length);
    }
  }
  return "";
}

document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM załadowany, tłumaczenie strony");
  const lang = getCookie("lang") || "pl";
  translatePage(lang);
});

// Obsługa przycisków zmiany języka
document.querySelectorAll(".language-button").forEach((button) => {
  button.addEventListener("click", () => {
    const lang = button.getAttribute("data-lang");
    translatePage(lang);
  });
});
