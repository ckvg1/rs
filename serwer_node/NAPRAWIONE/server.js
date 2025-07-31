var nodes7 = require("nodes7");
const express = require("express");
const app = express();
const cors = require("cors");
const port = 3000;
app.use(cors());
var conn = new nodes7();
app.use(express.json());

const L3_in = require("./variables/floor3/L3_in");
const L3_out = require("./variables/floor3/L3_out");
const T3 = require("./variables/floor3/T3");
const B3_in = require("./variables/floor3/B3_in");
const B3_out = require("./variables/floor3/B3_out");
let variables = {
  ...L3_in,
  ...L3_out,
  ...T3,
  ...B3_in,
  ...B3_out,
}; // scalanie wszystkich zmiennych z pietr w jeden obiekt

const server = app.listen(3000, () => {
  console.log("Serwer nasłuchuje na porcie 3000");
});

conn.initiateConnection(
  { port: 102, host: "192.168.25.1", rack: 0, slot: 1, debug: false },
  connected
);

function connected(err) {
  conn.setTranslationCB(function (tag) {
    return variables[tag];
  });

  app.get("/", (req, res) => {
    res.send("Serwer działa");
  });

  //temperatura

  app.get("/temperatura/3", (req, res) => {
    conn.removeItems();
    conn.addItems(Object.keys(T3));
    conn.readAllItems((err, val) => {
      if (err) {
        return res
          .status(500)
          .json({ error: "Blad przy pobieraniu temperatury" });
      }
      res.json(val);
      return;
    });
  });

  //swiatla

  app.get("/swiatla/3/wyjscia", (req, res) => {
    conn.removeItems();
    conn.addItems(Object.keys(L3_out));
    conn.readAllItems((err, val) => {
      res.json(val);
      return;
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
      return;
    });
  });

  //rolety
  const pendingTimers = new Map();

  //funckja zapisujaca bit (chatgpt)
  function writeBit(tagName, boolValue) {
    return new Promise((resolve, reject) => {
      conn.writeItems(tagName, boolValue ? 1 : 0, (err) => {
        if (err) return reject(err);
        resolve();
      });
    });
  }

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

    res.json({ status: "sukces zmiany rolety", roleta, wartosc });
  });

  app.get("/rolety/3/wyjscia/:roleta", (req, res) => {
    const { roleta } = req.params;
    conn.removeItems();
    conn.addItems([`wyj_${roleta}`]);

    conn.readAllItems((err, val) => {
      if (err) {
        return res
          .status(500)
          .json({ error: "Blad przy pobieraniu stanu rolety" });
      }
      res.json(val);
      return;
    });
  });
}
