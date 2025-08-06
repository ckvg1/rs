document.addEventListener("DOMContentLoaded", () => {
  const lang = document.cookie
    .split("; ")
    .find((row) => row.startsWith("lang="))
    ?.split("=")[1];
  if (lang) {
    translatePage(lang);
    const flagaPolski = document.getElementById("poland_flag");
    const flagaNiemiec = document.getElementById("germany_flag");
    const flagaWielkiejBrytani = document.getElementById("uk_flag");
    const flagaSlaska = document.getElementById("silesia_flag");
    if (flagaPolski)
      flagaPolski.style.border = lang === "pl" ? "2px solid red" : "none";
    if (flagaNiemiec)
      flagaNiemiec.style.border = lang === "de" ? "2px solid red" : "none";
    if (flagaWielkiejBrytani)
      flagaWielkiejBrytani.style.border = lang === "uk" ? "2px solid red" : "none";
    if (flagaSlaska)
      flagaSlaska.style.border = lang === "si" ? "2px solid red" : "none";
  } else {
    translatePage("pl"); // domyślny język
  }
});
