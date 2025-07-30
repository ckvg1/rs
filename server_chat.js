const express = require("express");
const nodes7 = require("nodes7");
const NodeCache = require("node-cache");
const cors = require("cors");

const app = express();
const port = 3000;

app.use(cors());
app.use(express.json());

const plc = new nodes7();
let isConnected = false;

// Import zmiennych z plików
const zmienne1 = require("./zmienne1");
const zmienne2 = require("./zmienne2");
const zmienne3 = require("./zmienne3");

// Scalanie zmiennych w jeden obiekt
const variables = { ...zmienne1, ...zmienne2, ...zmienne3 };

// Cache na wyniki PLC, TTL = 2 sekundy (do regulacji)
const cache = new NodeCache({ stdTTL: 2, checkperiod: 1 });

// Inicjalizacja połączenia z PLC
plc.initiateConnection(
  { port: 102, host: "192.168.25.1", rack: 0, slot: 1, debug: false },
  (err) => {
    if (err) {
      console.error("Błąd połączenia z PLC:", err);
      return;
    }
    plc.setTranslationCB((tag) => variables[tag]);
    plc.addItems(Object.keys(variables));
    isConnected = true;
    console.log("Połączono z PLC. Serwer działa na porcie", port);
  }
);

// Funkcja asynchronicznego odczytu wszystkich wartości z PLC
function readAllItemsAsync() {
  return new Promise((resolve, reject) => {
    plc.readAllItems((err, values) => {
      if (err) return reject(err);
      resolve(values);
    });
  });
}

// Endpoint testowy
app.get("/", (req, res) => {
  res.send("Serwer działa");
});

// Endpoint do pobrania wartości filtrowanych po prefiksie
app.get("/:typ/:id/:podtyp?", async (req, res) => {
  if (!isConnected) {
    return res.status(503).json({ error: "Brak połączenia z PLC" });
  }

  const { typ, id, podtyp } = req.params;
  const cacheKey = `${typ}_${id}_${podtyp || "all"}`;

  // Sprawdzenie cache
  const cached = cache.get(cacheKey);
  if (cached) {
    return res.json(cached);
  }

  try {
    const values = await readAllItemsAsync();
    let filtered;

    if (typ === "swiatla") {
      if (podtyp === "wyjscia") {
        filtered = Object.fromEntries(
          Object.entries(values).filter(([k]) => k.includes(`wyj_l${id}`))
        );
      } else if (podtyp === "wejscia") {
        filtered = Object.fromEntries(
          Object.entries(values).filter(([k]) => k.includes(`wej_l${id}`))
        );
      } else {
        filtered = Object.fromEntries(
          Object.entries(values).filter(([k]) => k.includes(`l${id}`))
        );
      }
    } else if (typ === "temperatura") {
      filtered = Object.fromEntries(
        Object.entries(values).filter(([k]) => k.includes(`t${id}`))
      );
    } else if (typ === "rolety" && podtyp === "wyjscia") {
      filtered = Object.fromEntries(
        Object.entries(values).filter(([k]) => k.includes(`wyj_b${id}_`))
      );
    } else {
      return res.status(400).json({ error: "Nieobsługiwany typ żądania" });
    }

    cache.set(cacheKey, filtered);
    return res.json(filtered);
  } catch (e) {
    console.error("Błąd odczytu z PLC:", e);
    return res.status(500).json({ error: "Błąd odczytu z PLC" });
  }
});

// Endpoint PUT do sterowania światłami i roletami
app.put("/:typ/:id/:nazwa", async (req, res) => {
  if (!isConnected) {
    return res.status(503).json({ error: "Brak połączenia z PLC" });
  }

  const { typ, id, nazwa } = req.params;
  const { wartosc } = req.body;

  try {
    if (typ === "swiatla") {
      await new Promise((resolve, reject) => {
        plc.writeItems(`wej_${nazwa}`, wartosc, (err) =>
          err ? reject(err) : resolve()
        );
      });
    } else if (typ === "rolety") {
      await new Promise((resolve, reject) => {
        plc.writeItems(`wej_${nazwa}`, wartosc ? 1 : 0, (err) =>
          err ? reject(err) : resolve()
        );
      });

      // Auto reset po 5 sekundach jeśli TRUE
      if (wartosc) {
        setTimeout(() => {
          plc.writeItems(`wej_${nazwa}`, 0, (err) => {
            if (err) console.error("Błąd auto-resetu:", err);
          });
        }, 5000);
      }
    } else {
      return res.status(400).json({ error: "Nieobsługiwany typ PUT" });
    }

    return res.json({ status: "success", typ, id, nazwa, wartosc });
  } catch (e) {
    console.error("Błąd zapisu:", e);
    return res.status(500).json({ error: "Błąd zapisu do PLC" });
  }
});

// Graceful shutdown
function cleanup() {
  plc.dropConnection(() => {
    console.log("Zamykanie połączenia...");
    process.exit(0);
  });
}
process.on("SIGINT", cleanup);
process.on("SIGTERM", cleanup);

app.listen(port, "0.0.0.0", () => {
  console.log(`Serwer nasłuchuje na porcie ${port}`);
});