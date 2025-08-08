function delay(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}
const alertBox = document.getElementById("alertBox");

let isFetchingLight = false;
let isFetchingBlinds = false;

function reconnectStreams() {
  location.reload(); // odświeżenie strony w celu ponownego nawiązania połączenia z serwerem
}
let no_internet = "../img_main/icony/no-internet.png";
function showAlert(
  message = "<strong>Uwaga</strong> - Nie udało się połączyć z serwerem. <img src='" +
    no_internet +
    "' style='width: 20px; height: 20px; vertical-align: middle;' /> <a onclick='window.location.reload()' style='cursor: pointer; margin-left: 20px'> <strong> spróbuj ponownie </strong> </a>"
) {
  if (document.body.classList.contains("ciemny")) {
    no_internet = "../img_main/icony/no-internet-dark.png";
  } else {
    no_internet = "../img_main/icony/no-internet.png";
  }
  alertBox.style.display = "block";
  document.querySelector(".oaerror").innerHTML = message;
  translatePage(getCookie("lang") || "pl");
}
function hideAlert() {
  alertBox.style.display = "none";
}
addEventListener("DOMContentLoaded", () => {
  showAlert(
    " Trwa łączenie z PLC. <img src='../img_main/icony/loading.gif' style='width: 30px; height: 30px; vertical-align: middle; margin-left: 10px' />"
  );
  const connectionTimeout = setTimeout(() => {
    showAlert();
  }, 10000);
  const swiatlaStream = new EventSource(
    `${window.config.apiBaseUrl}/stream/swiatla`
  );
  swiatlaStream.onmessage = (event) => {
    clearTimeout(connectionTimeout);
    const dane = JSON.parse(event.data);
    Object.entries(dane).forEach(([id, wartosc]) => {
      const el = document.getElementById(id);
      if (el) {
        if (el.id[0] == "w") {
          el.src = wartosc
            ? "../img_main/icony/bulb_on.png"
            : "../img_main/icony/bulb_of.png";
        }
      }
    });
  };
  const temperaturaStream = new EventSource(
    `${window.config.apiBaseUrl}/stream/temperatura`
  );
  temperaturaStream.onmessage = (event) => {
    clearTimeout(connectionTimeout);
    const dane = JSON.parse(event.data);

    Object.entries(dane).forEach(([id, wartosc]) => {
      const el = document.getElementById(id);
      if (el) el.innerText = `${wartosc.toFixed(1)} °C`;
    });
  };

  const roletyStream = new EventSource(
    `${window.config.apiBaseUrl}/stream/rolety`
  );
  roletyStream.onmessage = (event) => {
    clearTimeout(connectionTimeout);
    const dane = JSON.parse(event.data);
    Object.entries(dane).forEach(([id, wartosc]) => {
      const el = document.getElementById(
        id.split("_")[1] + "_" + id.split("_")[2] + "_" + id.split("_")[3]
      );
      if (el) {
        el.src = wartosc
          ? "../img_main/icony/arrow.png"
          : "../img_main/icony/arrow_of.png";
      }
    });
  };
  roletyStream.onerror = (err) => {
    showAlert();
    console.error("Błąd połączenia ze streamem rolety:", err);
  };
  temperaturaStream.onerror = (err) => {
    showAlert();
    console.error("Błąd połączenia ze streamem temperatury:", err);
  };
  swiatlaStream.onerror = (err) => {
    showAlert();
    console.error("Błąd połączenia ze streamem świateł:", err);
  };
  roletyStream.onopen = () => {
    hideAlert();
    console.log("Połączono ze streamem rolety");
  };
  temperaturaStream.onopen = () => {
    hideAlert();
    console.log("Połączono ze streamem temperatury");
  };
  swiatlaStream.onopen = () => {
    hideAlert();
    console.log("Połączono ze streamem świateł");
  };
});

async function wyslijTrue(swiatlo) {
  if (isFetchingLight) return;
  try {
    isFetchingLight = true;
    // Najpierw wł.
    await axios.put(`${window.config.apiBaseUrl}/swiatla/${swiatlo}`, {
      wartosc: true,
    });

    //Potem false
    await axios.put(`${window.config.apiBaseUrl}/swiatla/${swiatlo}`, {
      wartosc: false,
    });
  } catch (err) {
    alertBox.style.display = "block";
    showAlert();
  } finally {
    isFetchingLight = false;
  }
}

async function roletaWlacz(roleta) {
  if (isFetchingBlinds) return;
  isFetchingBlinds = false;
  try {
    await axios.put(`${window.config.apiBaseUrl}/rolety/${roleta}`, {
      wartosc: true,
    });
    await axios.put(`${window.config.apiBaseUrl}/rolety/${roleta}`, {
      wartosc: false,
    });
  } catch (err) {
    showAlert();
    console.error("Błąd wysyłania sygnału do rolet:", err);
  } finally {
    isFetchingBlinds = false;
  }
}
