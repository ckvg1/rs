document.addEventListener("DOMContentLoaded", function () {
  //console.log("DOM załadowany, dodawanie listenerów do przycisków zakładek");
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((button) => {
    //console.log("Dodano listener do przycisku zakładki:", button);
    button.addEventListener("click", () => {
      // dezaktwyuje wszystkie zakldadki i usuwa klase
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      //console.log("Dezaktywowano wszystkie zakładki");
      tabContents.forEach((content) => {
        content.classList.remove("active");
        // Wysłanie komunikatu "cleanup" do starego iframe
        const oldIframe = content.querySelector("iframe");
        if (oldIframe && oldIframe.contentWindow) {
          oldIframe.contentWindow.postMessage("cleanup", "*");
        }
        //console.log("Wysłano komunikat cleanup do starego iframe");
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
function sleep(ms){
  return new Promise(resolve => setTimeout(resolve, ms))
}
async function hamburger(x) {
  //console.log("Kliknięto hamburger menu");
  x.classList.toggle("change");
  tabsList = document.querySelector(".tabs-list");
  if (tabsList.style.right == "0px") {
    tabsList.style.right = "-300px";
    await sleep(500)
    tabsList.style.display= "none"
  } else {
    tabsList.style.display= "block"
    await sleep(1)
    tabsList.style.right = "0px";
  }
}

async function znikanie() {
  tabs_list = document.querySelector(".tabs_list");
  burger = document.querySelector(".hamburger");
  if (window.innerWidth <= 810) {
    tabsList.style.right = "-300px";
    await sleep(500)
    tabsList.style.display= "none"
  }
}
