const local = localStorage.getItem("theme");
if(local === "ciemny-motyw"){
  document.querySelectorAll("body, header, .modal-content, .hamburger, .tab-button, .oaerror, section, .przycisk, input, select, .icons_div, .bar1, .bar2, .bar3").forEach((el) => {
    el.classList.add("ciemny");
  });


  document.getElementById("settings_icon").classList.add("ciemny");

  document.querySelector(".logo").src = "logo-dark.svg"
  document.getElementById("settings_icon").src = "./img_main/settings-dark.png";
  document.getElementById("dark-mode").src = "img_main/dark-mode-dark.png"
}

function darkMode(){
  body = document.querySelectorAll("body");

  body.forEach((el) => {
    el.classList.toggle("ciemny");
  });

  if (document.body.classList.contains("ciemny")){
      localStorage.setItem("theme", "ciemny-motyw");
  }
  else if(document.body.classList.contains("jasny")){
      localStorage.setItem("theme", "jasny-motyw");
  }
}

