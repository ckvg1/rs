//tlumaczenia
const dictionary = {
  de: {
    "Wyłącz automatycznie wszystkie swiatła o:":
      "Schalte alle Lichter automatisch aus um:",
    "Zmieniaj nazwy czujników i pomieszczeń":
      "Ändere die Namen von Sensoren und Räumen",

    "Wyłącz wszystkie światła": "Schalte alle Lichter ",
    "Włącz wszystkie światła": "Schalte alle Lichter ein",
    "w firmie": "im Unternehmen aus",
    "w mieszkaniu": "in der Wohnung",
    "Pobierz miesięczny raport temperatury":
      "Lade den monatlichen Temperaturbericht herunter",

    "Pobierz miesięczny raport światła":
      "Lade den monatlichen Lichtbericht herunter",

    "używam danych z: ": "Ich verwende Daten von: ",

    "wybierz zakres dat": "Wählen Sie den Datumsbereich",
    "od-do": "von-bis",
    "wykonania pomiaru": "Messung durchgeführt am",
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
    pietra: "Etagen",
    ostatnie: "Letzte",
    godziny: "Stunden",
    Data: "Datum",
    Zapisz: "Speichern",
    dni: "tagen",
  },
};

function translatePage(lang) {
  // przechodzimy po wszystkich elementach na stronie
  console.log("Przetłumaczono na:", lang);

  if (lang === "pl") {
    return; // język bazowy - nic nie tłumaczymy
  }

  document.querySelectorAll("body *:not(script):not(style)").forEach((el) => {
    if (el.childNodes.length > 0) {
      el.childNodes.forEach((node) => {
        if (node.nodeType === Node.TEXT_NODE) {
          let text = node.nodeValue;

          for (const [key, val] of Object.entries(dictionary[lang])) {
            // Normalizacja: usuwamy znaki diakrytyczne i ignorujemy wielkość liter
            let normalizedKey = key
              .toLowerCase()
              .normalize("NFD")
              .replace(/[\u0300-\u036f]/g, "");
            let normalizedText = text
              .toLowerCase()
              .normalize("NFD")
              .replace(/[\u0300-\u036f]/g, "");

            // szukamy dopasowania po znormalizowanym tekście
            if (normalizedText.includes(normalizedKey)) {
              // prostsze podejście: zamiana "surowa"
              let regex = new RegExp(normalizedKey, "gi");
              text = normalizedText.replace(regex, val);
            }
          }
          node.nodeValue = text;
        }
      });
    }
  });
}
function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM załadowany, tłumaczenie strony");
  const lang = getCookie("lang") || "pl"; // domyślny język to polski
  translatePage(lang);
});
//obsluga zmiany języka
document.querySelectorAll(".language-button").forEach((button) => {
  button.addEventListener("click", () => {
    const lang = button.getAttribute("data-lang");
    translatePage(lang);
  });
});
