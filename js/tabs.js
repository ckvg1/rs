document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM załadowany, dodawanie listenerów do przycisków zakładek");
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((button) => {
    console.log("Dodano listener do przycisku zakładki:", button);
    button.addEventListener("click", () => {
      // dezaktwyuje wszystkie zakldadki i usuwa klase
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      console.log("Dezaktywowano wszystkie zakładki");
      tabContents.forEach((content) => {
        content.classList.remove("active");
        // Wysłanie komunikatu "cleanup" do starego iframe
        const oldIframe = content.querySelector("iframe");
        if (oldIframe && oldIframe.contentWindow) {
          oldIframe.contentWindow.postMessage("cleanup", "*");
        }
        console.log("Wysłano komunikat cleanup do starego iframe");
        // Usunięcie iframe z DOM
        content.innerHTML = "";
      });

      // aktywuje zakladke kliknieta i dodaje klase active
      button.classList.add("active");
      const tabId = button.getAttribute("data-tab");
      const iframe = button.getAttribute("data-src");
      document.getElementById(
        tabId
      ).innerHTML = `<iframe src="${iframe}" frameborder="0" width="100%" height="100%"></iframe>`;
      document.getElementById(tabId).classList.add("active");
    });
  });
});
function hamburger(x) {
  console.log("Kliknięto hamburger menu");
  x.classList.toggle("change");
  tabsList = document.querySelector(".tabs-list");
  if(tabsList.style.right == "0px"){
    tabsList.style.right = "-300px";
  }
  else{
    tabsList.style.right = "0px";
  }
}

function znikanie(){
  tabs_list = document.querySelector(".tabs_list");
  burger = document.querySelector(".hamburger");
  if(window.innerWidth <= 810){
    burger.classList.remove("change");
    tabsList.style.right = "-300px";
  }
}

function darkMode(){
  body = document.body;
  header = document.querySelector("header");
  settings = document.getElementById("settings_icon");
  dark_mode = document.getElementById("dark-mode");
  if(body.classList == "jasny"){
    body.classList.remove("jasny");
    body.classList.add("ciemny");
  }
  else if(body.classList == "ciemny"){
    body.classList.remove("ciemny");
    body.classList.add("jasny");
  }
  if(header.classList == "jasny"){
    header.classList.remove("jasny");
    header.classList.add("ciemny");
  }
  else if(header.classList == "ciemny"){
    header.classList.remove("ciemny");
    header.classList.add("jasny");
  }
  if(settings.classList == "jasny"){
    settings.src = "./img_main/settings-dark.png"
    settings.classList.remove("jasny");
    settings.classList.add("ciemny");
  }
  else if(settings.classList == "ciemny"){
    settings.src = "./img_main/settings.png"
    settings.classList.remove("ciemny");
    settings.classList.add("jasny");
  }
  if(dark_mode.classList == "jasny"){
    dark_mode.src = "img_main/dark-mode-dark.png"
    dark_mode.classList.remove("jasny");
    dark_mode.classList.add("ciemny");
  }
  else if(dark_mode.classList == "ciemny"){
    dark_mode.src = "img_main/dark-mode.png"
    dark_mode.classList.remove("ciemny");
    dark_mode.classList.add("jasny");
  }

}