// server.js

const nodes7 = require("nodes7");
const express = require("express");
const cors = require("cors");
const app = express();
const conn = new nodes7();
const port = 3000;

app.use(cors());
app.use(express.json());

// Import zmienne z każdego piętra i połącz w jeden obiekt
const zmienne1 = require("./zmienne1");
const zmienne2 = require("./zmienne2");
const zmienne3 = require("./zmienne3");
const variables = { ...zmienne1, ...zmienne2, ...zmienne3 };

// Mapowanie timera dla każdego tagu rolety
const pendingTimers = new Map();

/**
 * Zapis bitu do PLC
 * @param {string} tagName – nazwa tagu (np. "wej_b3_r4_1")
 * @param {boolean} value – true = 1, false = 0
 */
function writeBit(tagName, value) {
  return new Promise((resolve, reject) => {
    conn.writeItems(tagName, value ? 1 : 0, (err) => {
      if (err) return reject(err);
      resolve();
    });
  });
}

/**
 * Czyta wszystkie dodane tagi jako Promise
 */
function readAllItemsAsync() {
  return new Promise((resolve, reject) => {
    conn.readAllItems((err, val) => {
      if (err) return reject(err);
      resolve(val);
    });
  });
}

// Rozpocznij nasłuchiwanie serwera
app.listen(port, "0.0.0.0", () => {
  console.log(`Serwer nasłuchuje na porcie ${port}`);
});

// Inicjalizacja połączenia z PLC
conn.initiateConnection(
  { host: "192.168.25.1", port: 102, rack: 0, slot: 1, debug: false },
  async (err) => {
    if (err) {
      console.error("Błąd połączenia z PLC:", err);
      process.exit(1);
    }

    // Przetłumacz nazwy tagów na adresy z plików zmiennych
    conn.setTranslationCB((tag) => variables[tag]);
    conn.addItems(Object.keys(variables));

    // Wypisz początkowe wartości
    try {
      const initial = await readAllItemsAsync();
      console.log("_______________ POCZĄTKOWE WARTOŚCI _______________");
      console.log(initial);
      console.log("___________________________________________________");
    } catch (e) {
      console.error("Błąd odczytu początkowego:", e);
    }

    // --- ENDPOINTY ---

    app.get("/", (req, res) => {
      res.send("Serwer działa");
    });

    app.get("/swiatla/3", (req, res) => {
      conn.readAllItems((err, val) => {
        if (err)
          return res
            .status(500)
            .json({ error: "Błąd przy pobieraniu świateł" });
        const filtered = Object.fromEntries(
          Object.entries(val).filter(([k]) => k.includes("l3"))
        );
        res.json(filtered);
      });
    });

    app.get("/swiatla/3/wyjscia", (req, res) => {
      conn.readAllItems((err, val) => {
        if (err)
          return res.status(500).json({ error: "Błąd przy pobieraniu wyjść" });
        const filtered = Object.fromEntries(
          Object.entries(val).filter(([k]) => k.includes("wyj_l3"))
        );
        res.json(filtered);
      });
    });

    app.get("/swiatla/3/wejscia", (req, res) => {
      conn.readAllItems((err, val) => {
        if (err)
          return res.status(500).json({ error: "Błąd przy pobieraniu wejść" });
        const filtered = Object.fromEntries(
          Object.entries(val).filter(([k]) => k.includes("wej_l3"))
        );
        res.json(filtered);
      });
    });

    app.get("/temperatura/3", (req, res) => {
      conn.readAllItems((err, val) => {
        if (err)
          return res
            .status(500)
            .json({ error: "Błąd przy pobieraniu temperatury" });
        const filtered = Object.fromEntries(
          Object.entries(val).filter(([k]) => k.includes("t3"))
        );
        res.json(filtered);
      });
    });

    app.get("/swiatla/3/:swiatlo", (req, res) => {
      const { swiatlo } = req.params;
      conn.readAllItems((err, val) => {
        if (err) return res.status(500).json({ error: "Błąd przy odczycie" });
        res.json({
          wejscie: val[`wej_${swiatlo}`],
          wyjscie: val[`wyj_${swiatlo}`],
        });
      });
    });

    app.put("/swiatla/3/:swiatlo", (req, res) => {
      const { swiatlo } = req.params;
      const { wartosc } = req.body;
      const tag = `wej_${swiatlo}`;

      conn.writeItems(tag, wartosc ? 1 : 0, (err) => {
        if (err) return res.status(500).json({ error: "Błąd zapisu" });
        conn.readAllItems((e, val) => {
          if (e) console.error(e);
          const filtered = Object.fromEntries(
            Object.entries(val).filter(
              ([k]) =>
                k.includes(`wyj_${swiatlo}`) || k.includes(`wej_${swiatlo}`)
            )
          );
          res.json(filtered);
        });
      });
    });

    // PUT dla rolet z logiką 5s i nadpisaniem
    app.put("/rolety/3/:roleta", async (req, res) => {
      try {
        const { roleta } = req.params;
        const { wartosc, duration = 5000 } = req.body;
        const tag = `wej_${roleta}`;

        if (!variables[tag]) {
          return res.status(400).json({ error: `Nieznana roleta: ${roleta}` });
        }

        if (wartosc) {
          // anuluj stary timer, jeśli istnieje
          if (pendingTimers.has(tag)) {
            clearTimeout(pendingTimers.get(tag));
          }

          // włącz bit
          await writeBit(tag, true);

          // zaplanuj wyłączenie po `duration` ms
          const to = setTimeout(async () => {
            try {
              await writeBit(tag, false);
            } catch (e) {
              console.error(`Błąd auto-dezaktywacji ${tag}:`, e);
            }
            pendingTimers.delete(tag);
          }, duration);

          pendingTimers.set(tag, to);
        } else {
          // wyłącz natychmiast i anuluj timer
          if (pendingTimers.has(tag)) {
            clearTimeout(pendingTimers.get(tag));
            pendingTimers.delete(tag);
          }
          await writeBit(tag, false);
        }

        res.json({ status: "ok", roleta, wartosc, duration });
      } catch (err) {
        console.error(err);
        res.status(500).json({ error: err.toString() });
      }
    });
  }
);

// Sprzątanie przy zamykaniu procesu
function cleanup() {
  conn.dropConnection(() => {
    console.log("Rozłączono z PLC. Zamykanie serwera...");
    process.exit(0);
  });
}

process.on("SIGINT", cleanup);
process.on("SIGTERM", cleanup);
