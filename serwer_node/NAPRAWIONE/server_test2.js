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

const L3_in = require("./variables/L3_in");
const L3_out = require("./variables/L3_out");
const T3 = require("./variables/T3");
const B3_in = require("./variables/B3_in");
const B3_out = require("./variables/B3_out");

const variables = {
  ...L3_in,
  ...L3_out,
  ...T3,
  ...B3_in,
  ...B3_out,
};

const T3_keys = Object.keys(T3);
const L3_out_keys = Object.keys(L3_out);
const B3_out_keys = Object.keys(B3_out);
const ALL_KEYS = [...new Set([...T3_keys, ...L3_out_keys, ...B3_out_keys])];

const pendingTimers = new Map();

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

  // GET: swiatla z cache
  app.get("/swiatla/3/wyjscia", (req, res) => {
    const data = cache.get("plcData");
    if (!data) return res.status(503).json({ error: "Dane niedostępne" });

    const lights = {};
    for (let k of L3_out_keys) lights[k] = data[k];
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
}
