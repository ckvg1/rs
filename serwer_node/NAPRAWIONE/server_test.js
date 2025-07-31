const nodes7 = require("nodes7");
const express = require("express");
const cors = require("cors");
const { Mutex } = require("async-mutex");

const app = express();
const port = 3000;
app.use(cors());
app.use(express.json());

const conn = new nodes7();
const plcMutex = new Mutex();

const L3_in = require("./variables/L3_in");
const L3_out = require("./variables/L3_out");
const T3 = require("./variables/T3");
const B3_in = require("./variables/B3_in");
const B3_out = require("./variables/B3_out");

let variables = {
  ...L3_in,
  ...L3_out,
  ...T3,
  ...B3_in,
  ...B3_out,
};

const server = app.listen(port, () => {
  console.log(`Serwer nasłuchuje na porcie ${port}`);
});

conn.initiateConnection(
  { port: 102, host: "192.168.25.1", rack: 0, slot: 1, debug: false },
  connected
);

function connected(err) {
  if (err) {
    console.error("Błąd połączenia z PLC:", err);
    return;
  }

  conn.setTranslationCB((tag) => variables[tag]);

  app.get("/", (req, res) => {
    res.send("Serwer działa");
  });

  // GET: temperatura
  app.get("/temperatura/3", async (req, res) => {
    await plcMutex.runExclusive(async () => {
      conn.removeItems();
      conn.addItems(Object.keys(T3));

      await new Promise((resolve) => {
        conn.readAllItems((err, val) => {
          if (err) {
            res.status(500).json({ error: "Błąd przy pobieraniu temperatury" });
          } else {
            res.json(val);
          }
          resolve();
        });
      });
    });
  });

  // GET: swiatla wyjscia
  app.get("/swiatla/3/wyjscia", async (req, res) => {
    await plcMutex.runExclusive(async () => {
      conn.removeItems();
      conn.addItems(Object.keys(L3_out));

      await new Promise((resolve) => {
        conn.readAllItems((err, val) => {
          if (err) {
            res.status(500).json({ error: "Błąd przy pobieraniu świateł" });
          } else {
            res.json(val);
          }
          resolve();
        });
      });
    });
  });

  // PUT: swiatlo zmiana
  app.put("/swiatla/3/:swiatlo", async (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;

    await plcMutex.runExclusive(async () => {
      conn.removeItems();
      conn.addItems([`wej_${swiatlo}`]);

      await new Promise((resolve) => {
        conn.writeItems(`wej_${swiatlo}`, wartosc, () => {
          conn.readAllItems((err, val) => {
            if (err) {
              res.status(500).json({ error: "Błąd przy zapisie światła" });
            } else {
              res.json(val);
            }
            resolve();
          });
        });
      });
    });
  });
}
