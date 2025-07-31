const nodes7 = require("nodes7");
const express = require("express");
const cors = require("cors");
const { Mutex } = require("async-mutex");
const NodeCache = require("node-cache");

const app = express();
const port = 3000;
app.use(cors());
app.use(express.json());

const conn = new nodes7();
const plcMutex = new Mutex();
const cache = new NodeCache({ stdTTL: 2, checkperiod: 1 }); // TTL 2s, odczyt co 1s

// 3 pietro
const L3_in = require("./variables/floor3/L3_in");
const L3_out = require("./variables/floor3/L3_out");
const T3 = require("./variables/floor3/T3");
const B3_in = require("./variables/floor3/B3_in");
const B3_out = require("./variables/floor3/B3_out");

// 2 pietro
const L2_in = require("./variables/floor2/L2_in");
const L2_out = require("./variables/floor2/L2_out");
const T2 = require("./variables/floor2/T2");
const B2_in = require("./variables/floor2/B2_in");
const B2_out = require("./variables/floor2/B2_out");

const variables = {
  ...L3_in,
  ...L3_out,
  ...T3,
  ...B3_in,
  ...B3_out,
  ...L2_in,
  ...L2_out,
  ...T2,
  ...B2_in,
  ...B2_out,
};

const T3_keys = Object.keys(T3);
const L3_out_keys = Object.keys(L3_out);
const B3_out_keys = Object.keys(B3_out);
const T2_keys = Object.keys(T2);
const L2_out_keys = Object.keys(L2_out);
const B2_out_keys = Object.keys(B2_out);

// ALL_KEYS zawiera wszystkie klucze ktore odczytujemy z PLC i aktualizujemy w cache
const ALL_KEYS = [
  ...new Set([...T3_keys, ...L3_out_keys, ...T2_keys, ...L2_out_keys]),
];

// TEMPERATURE_KEYS zawiera wszystkie klucze temperatury
const TEMPERATURE_KEYS = [...new Set([...T3_keys, ...T2_keys])];

const server = app.listen(port, () => {
  console.log(`Serwer nasłuchuje na porcie ${port}`);
});

conn.initiateConnection(
  {
    port: 102,
    host: "192.168.25.1",
    rack: 0,
    slot: 1,
    debug: false,
    doNotOptimize: true,
  },
  connected
);

function connected(err) {
  if (err) {
    console.error("Błąd połączenia z PLC:", err);
    return;
  }

  conn.setTranslationCB((tag) => variables[tag]);

  // Cykliczne odczyty do cache co 1s
  setInterval(() => {
    plcMutex.runExclusive(async () => {
      conn.removeItems();
      conn.addItems(ALL_KEYS);
      await new Promise((resolve) => {
        conn.readAllItems((err, val) => {
          if (!err) cache.set("plcData", val);
          resolve();
        });
      });
    });
  }, 1000);

  // GET: ping
  app.get("/", (req, res) => {
    res.send("Serwer działa");
  });

  // GET: temperatura z cache
  app.get("/temperatura/3", (req, res) => {
    const data = cache.get("plcData");
    if (!data) return res.status(503).json({ error: "Dane niedostępne" });

    const temp = {};
    for (let k of T3_keys) temp[k] = data[k];
    res.json(temp);
  });

  app.get("/temperatura/2", (req, res) => {
    const data = cache.get("plcData");
    if (!data) return res.status(503).json({ error: "Dane niedostępne" });

    const temp = {};
    for (let k of T2_keys) temp[k] = data[k];
    res.json(temp);
  });

  // GET: swiatla z cache
  app.get("/swiatla/3/wyjscia", (req, res) => {
    const data = cache.get("plcData");
    if (!data) return res.status(503).json({ error: "Dane niedostępne" });

    const lights = {};
    for (let k of L3_out_keys) lights[k] = data[k];
    res.json(lights);
  });

  app.get("/swiatla/2/wyjscia", (req, res) => {
    const data = cache.get("plcData");
    if (!data) return res.status(503).json({ error: "Dane niedostępne" });

    const lights = {};
    for (let k of L2_out_keys) lights[k] = data[k];
    res.json(lights);
  });

  // PUT: sterowanie światłem
  app.put("/swiatla/3/:swiatlo", async (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;

    await plcMutex.runExclusive(async () => {
      await new Promise((resolve) => {
        conn.writeItems(`wej_${swiatlo}`, wartosc, (err) => {
          if (err) return res.status(500).json({ error: "Błąd przy zapisie" });

          // Odczyt po zapisie do odświeżenia cache
          conn.readAllItems((err, val) => {
            if (!err) cache.set("plcData", val);
            res.json({ status: "zapisano", wartosc });
            resolve();
          });
        });
      });
    });
  });

  app.put("/swiatla/2/:swiatlo", async (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;

    await plcMutex.runExclusive(async () => {
      await new Promise((resolve) => {
        conn.writeItems(`wej_${swiatlo}`, wartosc, (err) => {
          if (err) return res.status(500).json({ error: "Błąd przy zapisie" });

          // Odczyt po zapisie do odświeżenia cache
          conn.readAllItems((err, val) => {
            if (!err) cache.set("plcData", val);
            res.json({ status: "zapisano", wartosc });
            resolve();
          });
        });
      });
    });
  });
}
