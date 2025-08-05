document.addEventListener("DOMContentLoaded", () => {
  const lang = document.cookie
    .split("; ")
    .find((row) => row.startsWith("lang="))
    ?.split("=")[1];
  if (lang) {
    translatePage(lang);
    document.getElementById("poland_flag").style.border =
      lang === "pl" ? "2px solid red" : "none";
    document.getElementById("germany_flag").style.border =
      lang === "de" ? "2px solid red" : "none";
  } else {
    translatePage("pl"); // domyślny język
  }
});
