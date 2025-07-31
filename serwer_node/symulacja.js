const express = require("express");
const cors = require("cors");
const app = express();
const port = 3000;

app.use(cors());
app.use(express.json());

const zmienne1 = require("./zmienne1");
const zmienne2 = require("./zmienne2");
const zmienne3 = require("./zmienne3_wejscia");

const variables = { ...zmienne1, ...zmienne2, ...zmienne3 };
console.log(variables);
// 🔌 SYMULOWANY STEROWNIK PLC
class FakePLC {
  constructor() {
    this.items = {};
    this.interval = null;
    this.previousInputs = {}; // <- do śledzenia poprzednich wartości
  }

  setTranslationCB(cb) {
    this.translationCB = cb;
  }

  addItems(itemNames) {
    itemNames.forEach((name) => {
      if (name.startsWith("t")) {
        this.items[name] = this.randomTemp();
      } else {
        this.items[name] = false;
      }
    });

    this.interval = setInterval(() => {
      this.simulateLogic();
      this.simulateTemperatures();
    }, 100);
  }

  simulateLogic() {
    for (const key of Object.keys(this.items)) {
      if (key.startsWith("wej_")) {
        const base = key.replace("wej_", "");
        const outKey = `wyj_${base}`;
        const current = this.items[key];
        const prev = this.previousInputs[key] ?? false;

        // console.log(`🔎 SPRAWDZAM: ${key} | current: ${current}, prev: ${prev}`);

        // REAKCJA TYLKO NA ZBOCZE NARASTAJĄCE
        if (current === true && prev === false && outKey in this.items) {
          console.log(`🔁 TOGGLE: ${outKey} ← ${!this.items[outKey]}`);
          this.items[outKey] = !this.items[outKey];
        }

        // Zapisz aktualny stan jako "poprzedni" do kolejnego cyklu
        this.previousInputs[key] = current;
      }
    }
  }

  simulateTemperatures() {
    for (const key of Object.keys(this.items)) {
      if (key.startsWith("t3_")) {
        const current = this.items[key];
        const delta = (Math.random() - 0.5) * 0.4; // ±0.2
        this.items[key] = Math.round((current + delta) * 10) / 10;
      }
    }
  }

  randomTemp() {
    return Math.round((20 + Math.random() * 5) * 10) / 10; // np. 20–25°C
  }

  readAllItems(cb) {
    cb(null, { ...this.items });
  }

  writeItems(name, value) {
    if (name in this.items) {
      this.items[name] = value;
      console.log(`📥 ZAPISANE: ${name} = ${value}`); // 🔍 LOG
    } else {
      console.log(`❌ NIE MA TAKIEGO KLUCZA: ${name}`);
    }
  }

  dropConnection(cb) {
    clearInterval(this.interval);
    console.log("Symulowany sterownik rozłączony.");
    cb();
  }
}

const conn = new FakePLC();

// 🟢 START SYMULACJI
function connected() {
  conn.setTranslationCB((tag) => variables[tag]);
  conn.addItems(Object.keys(variables));

  console.log("Sterownik PLC zasymulowany ✅");

  // Endpoints

  app.get("/", (req, res) => {
    res.send("Symulowany serwer PLC działa");
  });

  app.get("/swiatla", (req, res) => {
    conn.readAllItems((err, val) => {
      const result = Object.fromEntries(
        Object.entries(val).filter(([k]) => k.includes("l"))
      );
      res.json(result);
    });
  });

  app.get("/swiatla/wejscia", (req, res) => {
    conn.readAllItems((err, val) => {
      const wejscia = Object.fromEntries(
        Object.entries(val).filter(([k]) => k.startsWith("wej_"))
      );
      res.json(wejscia);
    });
  });

  app.get("/swiatla/wyjscia", (req, res) => {
    conn.readAllItems((err, val) => {
      const wyjscia = Object.fromEntries(
        Object.entries(val).filter(([k]) => k.startsWith("wyj_"))
      );
      res.json(wyjscia);
    });
  });
  app.get("/swiatla/3/wyjscia", (req, res) => {
    conn.readAllItems((err, val) => {
      const wyjscia = Object.fromEntries(
        Object.entries(val).filter(([k]) => k.startsWith("wyj_l3"))
      );
      res.json(wyjscia);
    });
  });

  app.get("/temperatura/3", (req, res) => {
    conn.readAllItems((err, val) => {
      const temp = Object.fromEntries(
        Object.entries(val).filter(([k]) => k.startsWith("t3_"))
      );
      res.json(temp);
    });
  });

  app.put("/swiatla/3/:swiatlo", (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;
    const wejKey = `wej_${swiatlo}`;

    conn.writeItems(wejKey, wartosc);
    console.log(`Przycisk: wej_${swiatlo} = ${wartosc}`);
    setTimeout(() => {
      conn.readAllItems((err, val) => {
        const filtered = Object.fromEntries(
          Object.entries(val).filter(
            ([k]) => k === `wej_${swiatlo}` || k === `wyj_${swiatlo}`
          )
        );
        res.json(filtered);
      });
    }, 300);
  });

  app.get("/swiatla/:swiatlo", (req, res) => {
    const { swiatlo } = req.params;

    conn.readAllItems((err, val) => {
      res.json({
        wejscie: val[`wej_${swiatlo}`],
        wyjscie: val[`wyj_${swiatlo}`],
      });
    });
  });
}

// Start serwera
app.listen(port, "0.0.0.0", () => {
  console.log(`Symulowany serwer PLC nasłuchuje na porcie ${port}`);
  connected();
});

// Zamknięcie
function cleanup() {
  conn.dropConnection(() => {
    console.log("Zamykanie serwera...");
    process.exit(0);
  });
}
process.on("SIGINT", cleanup);
process.on("SIGTERM", cleanup);
