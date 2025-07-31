var nodes7 = require("nodes7");
const express = require("express");
const app = express();
const cors = require("cors");
const port = 3000;
app.use(cors());
var conn = new nodes7();
app.use(express.json());

const zmienne1 = require("./zmienne1");
const zmienne2 = require("./zmienne2");
const zmienne3_wejscia = require("./zmienne3_wejscia");
const zmienne3_wyjscia = require("./zmienne3_wyjscia");
const zmienne3_temperatura = require("./zmienne3_temperatura");
let variables = {
  ...zmienne3_wejscia,
  ...zmienne3_wyjscia,
  ...zmienne3_temperatura,
}; // scalanie wszystkich zmiennych z pietr w jeden obiekt
const server = app.listen(3000, "0.0.0.0", () => {
  console.log("Serwer nasłuchuje na porcie 3000");
});
conn.initiateConnection(
  { port: 102, host: "192.168.25.1", rack: 0, slot: 1, debug: false },
  connected
);
function readAllItemsAsync() {
  return new Promise((res, rej) => {
    conn.readAllItems((err, val) => {
      res(val);
    });
  });
}
async function connected(err) {
  conn.setTranslationCB(function (tag) {
    return variables[tag];
  });
  conn.addItems(Object.keys(variables));

  if (typeof err !== "undefined") {
    console.log(err);
    process.exit();
  }

  app.get("/", (req, res) => {
    res.send("Serwer działa");
  });

  app.get("/swiatla/3", (req, res) => {
    conn.readAllItems((err, val) => {
      if (err) {
        return res.status(500).json({ error: "Blad przy pobieraniu swiatel" });
      }
      res.json(
        Object.fromEntries(
          Object.entries(val).filter(([klucz, _]) => klucz.includes("l3")) //filtrowanie tylko wejsc (powinny byc wszystkie na false )
        )
      );
    });
  });

  app.get("/swiatla/3/wyjscia", (req, res) => {
    conn.removeItems();
    conn.addItems(Object.keys(zmienne3_wyjscia));
    conn.readAllItems((err, val) => {
      if (err) {
        return res.status(500).json({ error: "Blad przy pobieraniu wyjsc" });
      }
      res.json(
        Object.fromEntries(
          Object.entries(val).filter(([klucz, _]) => klucz.includes("wyj_l3")) //filtrowanie tylko wejsc (powinny byc wszystkie na false )
        )
      );
    });
  });
  /*app.get("/swiatla/3/wejscia", (req, res) => {
    conn.readAllItems((err, val) => {
      if (err) {
        return res.status(500).json({ error: "Blad przy pobieraniu wejsc" });
      }
      res.json(
        Object.fromEntries(
          Object.entries(val).filter(([klucz, _]) => klucz.includes("wej_l3")) //filtrowanie tylko wejsc (powinny byc wszystkie na false )
        )
      );
    });
  });*/
  app.get("/temperatura/3", (req, res) => {
    conn.removeItems();
    conn.addItems(Object.keys(zmienne3_temperatura));
    conn.readAllItems((err, val) => {
      if (err) {
        return res
          .status(500)
          .json({ error: "Blad przy pobieraniu temperatury" });
      }
      res.json(
        val
        //Object.fromEntries(
        //  Object.entries(val).filter(([klucz, _]) => klucz.includes("t3")) //filtrowanie tylko wejsc (powinny byc wszystkie na false )
        //)
      );
    });
  });
  app.put("/swiatla/3/:swiatlo", (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;
    conn.removeItems();
    conn.addItems([`wej_${swiatlo}`]);
    conn.writeItems(`wej_${swiatlo}`, wartosc);

    conn.readAllItems((err, val) => {
      res.send(val);
    });
  });

  const pendingTimers = new Map();

  // 5. Funkcja zapisująca bit (1 lub 0)
  function writeBit(tagName, boolValue) {
    return new Promise((resolve, reject) => {
      conn.writeItems(tagName, boolValue ? 1 : 0, (err) => {
        if (err) return reject(err);
        resolve();
      });
    });
  }
  app.get("/rolety/3/wyjscia/:roleta", (req, res) => {
    conn.readAllItems((err, val) => {
      if (err) return res.status(500).json({ error: "Błąd przy odczycie" });
      const { roleta } = req.params;
      res.json({
        wartosc: val[`wyj_b3_${roleta}`],
      });
    });
  });
  app.put("/rolety/3/:roleta", async (req, res) => {
    const { roleta } = req.params;
    const { wartosc } = req.body;
    const duration = 5000; // 5 sekund

    // Jeśli wysłano TRUE
    if (wartosc) {
      await writeBit(`wej_${roleta}`, true);

      // Kasowanie starego timeouta (jeśli był)
      if (pendingTimers.has(roleta)) {
        clearTimeout(pendingTimers.get(roleta));
        pendingTimers.delete(roleta);
      }

      // Ustawienie nowego timeouta do automatycznego resetu
      const timeout = setTimeout(async () => {
        try {
          await writeBit(`wej_${roleta}`, false);
        } catch (e) {
          console.error(`Błąd auto-dezaktywacji wej_${roleta}:`, e);
        }
        pendingTimers.delete(roleta);
      }, duration);

      pendingTimers.set(roleta, timeout);
    }

    // Jeśli wysłano FALSE (przerwanie działania)
    else {
      await writeBit(`wej_${roleta}`, false);
      if (pendingTimers.has(roleta)) {
        clearTimeout(pendingTimers.get(roleta));
        pendingTimers.delete(roleta);
      }
    }

    res.json({ status: "success", roleta, wartosc });
  });
  app.get("/swiatla/3/:swiatlo", (req, res) => {
    const { swiatlo } = req.params;

    conn.readAllItems((err, val) => {
      res.json({
        wejscie: val[`wej_${swiatlo}`],
        wyjscie: val[`wyj_${swiatlo}`],
      });
    });
  });
}
function cleanup() {
  conn.dropConnection(() => {
    console.log("zamykanie...");
    process.exit(0);
  });
}
process.on("SIGINT", cleanup);
process.on("SIGTERM", cleanup);
