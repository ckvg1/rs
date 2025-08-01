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
const cache = new NodeCache({ stdTTL: 0, checkperiod: 1 }); // TTL 2s, odczyt co 1s

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

const temperature_timeout = 10000; // Odczyt temperatury co 10000 ms (10 sekund)
const lights_timeout = 200; // Odczyt świateł co 200ms (0.2 sekundy)
const blinds_timeout = 300; // Odczyt rolet co 500ms (0.5 sekundy)

/* 
Ustawiamy TTL (time to live) dla danych w cache
TTL to czas, przez jaki dane będą przechowywane w cache przed ich usunięciem
Wartości te są ustawione na podstawie czasu, jaki potrzebujemy do odczytu danych z PLC i ich aktualizacji w cache
Można je dostosować w zależności od potrzeb aplikacji i częstotliwości zmian danych
Wartości te są w milisekundach (ms)
*/
const temperature_ttl = 11000; // TTL dla temperatury
const lights_ttl = 400; // TTL dla świateł
const blinds_ttl = 400; // TTL dla rolet

/*
Klucze do wyjść, potrzebne zeby zapisywać wartosci do cache. 
Wszystkie klucze są (i powinny!) byc unikalne, więc używamy Set do usunięcia przypadkowych duplikatów. (co nie powinno mieć miejsca, ale lepiej dmuchać na zimne ;))
Zmienne są zdefiniowane w plikach variables/floorX/*.js
*/

const LIGHT_KEYS = [...new Set([...L2_out_keys, ...L3_out_keys])];
const TEMPERATURE_KEYS = [...new Set([...T3_keys, ...T2_keys])];
const BLINDS_KEYS = [...new Set([...B3_out_keys, ...B2_out_keys])];

// Inicjalizcja serwera Express
const server = app.listen(port, "0.0.0.0", () => {
  console.log(`Serwer nasłuchuje na porcie ${port}`);
});

// Inicjalizacja połączenia z PLC
conn.initiateConnection(
  {
    port: 102,
    host: "192.168.25.1",
    rack: 0,
    slot: 1,
    debug: false,
    doNotOptimize: true, // Wyłączamy optymalizacje, żeby mieć pełną kontrolę nad odczytami/zapisami
  },
  connected
);

/* 
  Funckja odpowiedzialna za odczyt danych po starcie serwera.
  Odczytuje światła i temperatury i zapisuje je do cache.
  Używamy mutexa, żeby zapewnić, że tylko jeden odczyt będzie wykonywany w danym momencie.
  Dzięki temu unikamy konfliktów przy odczycie/zapisie do PLC.

  Funkcja jest wywoływana tylko raz po starcie serwera, żeby wstępnie załadować dane do cache.
  Następnie cyklicznie odczytuje dane co 200ms dla świateł i co 10s dla temperatury.
 */
function readAfterStartup() {
  plcMutex.runExclusive(async () => {
    conn.removeItems();
    conn.addItems(LIGHT_KEYS);
    await new Promise((resolve) => {
      conn.readAllItems((err, val) => {
        if (!err) cache.set("swiatlaData", val, lights_ttl); //time to live
        resolve();
      });
    });
  });

  plcMutex.runExclusive(async () => {
    conn.removeItems();
    conn.addItems(TEMPERATURE_KEYS);
    await new Promise((resolve) => {
      conn.readAllItems((err, val) => {
        if (!err) cache.set("temperaturaData", val, temperature_ttl); //time to live
        resolve();
      });
    });
  });

  plcMutex.runExclusive(async () => {
    conn.removeItems();
    conn.addItems(BLINDS_KEYS);
    await new Promise((resolve) => {
      conn.readAllItems((err, val) => {
        if (!err) cache.set("roletyData", val, blinds_ttl); //time to live
        resolve();
      });
    });
  });
}
function connected(err) {
  if (err) {
    console.error("Błąd połączenia z PLC:", err);
    return;
  }

  readAfterStartup();

  // Ustawienie callbacka do tłumaczenia tagów na zmienne
  // https://github.com/plcpeople/nodeS7/tree/master?tab=readme-ov-file#nodes7settranslationcbtranslator
  conn.setTranslationCB((tag) => variables[tag]);

  // Cykliczne odczyty swiatel do cache co (lights_timeout) ms
  setInterval(() => {
    plcMutex.runExclusive(async () => {
      // światła
      conn.removeItems(); //usuwamy stare klucze
      conn.addItems(LIGHT_KEYS); //dodajemy nowe klucze (światła)
      await new Promise((resolve) => {
        conn.readAllItems((err, val) => {
          if (!err) cache.set("swiatlaData", val, lights_ttl); //zapisujemy do cache, jeśli nie ma błędu
          resolve();
        });
      });
    });
  }, lights_timeout);

  // Cykliczne odczyty temperatury do cache co (temperature_timeout) ms
  setInterval(() => {
    plcMutex.runExclusive(async () => {
      conn.removeItems(); // usuwamy stare klucze
      conn.addItems(TEMPERATURE_KEYS); //dodajemy nowe klucze (temperatura)
      await new Promise((resolve) => {
        conn.readAllItems((err, val) => {
          if (!err) cache.set("temperaturaData", val, temperature_ttl); //zapisujemy do cache, jeśli nie ma błędu
          resolve();
        });
      });
    });
  }, temperature_timeout);

  // Cykliczne odczyty rolet co (blinds_timeout) ms
  setInterval(() => {
    plcMutex.runExclusive(async () => {
      conn.removeItems(); // usuwamy stare klucze
      conn.addItems(BLINDS_KEYS); //dodajemy nowe klucze (rolety)
      await new Promise((resolve) => {
        conn.readAllItems((err, val) => {
          if (!err) cache.set("roletyData", val, blinds_ttl); //zapisujemy do cache, jeśli nie ma błędu
          resolve();
        });
      });
    });
  }, blinds_timeout);

  // GET: ping
  app.get("/", (req, res) => {
    res.send("Serwer działa");
  });

  /*
    !!!
    WSZYSTKIE endpointy GET OPRÓCZ /stream/* nie są używane do odczytu danych z PLC przez przeglądarkę.
    Służą one TYLKO do debugowania i testowania.
    Zamiast tego, dane są wysyłane przez SSE (Server-Sent Events), co określoną ilość czasu.
    !!!
  */

  // GET: temperatura z cache
  app.get("/temperatura/3", (req, res) => {
    const data = cache.get("temperaturaData");
    if (!data) return res.status(503).json({ error: "Dane niedostępne" });

    const temp = {};
    for (let k of T3_keys) temp[k] = data[k];
    res.json(temp);
  });

  // GET: swiatla z cache
  app.get("/swiatla/3/wyjscia", (req, res) => {
    const data = cache.get("swiatlaData");
    if (!data) return res.status(503).json({ error: "Dane niedostępne" });

    const lights = {};
    for (let k of L3_out_keys) lights[k] = data[k];
    res.json(lights);
  });

  // PUT: sterowanie światłem

  //3 piętro
  app.put("/swiatla/3/:swiatlo", async (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;

    await plcMutex.runExclusive(async () => {
      await new Promise((resolve) => {
        conn.writeItems(`wej_${swiatlo}`, wartosc, (err) => {
          if (err) return res.status(500).json({ error: "Błąd przy zapisie" });

          // Odczyt po zapisie do odświeżenia cache
          conn.removeItems();
          conn.addItems(L3_out_keys);
          conn.readAllItems((err, val) => {
            //ustawiliśmy nowe wartości dla świateł, więc odczytujemy wszystkie wartości i ustawiamy je w cache jako nowe.
            if (!err) cache.set("swiatlaData", val, lights_ttl);
            res.json({ status: "zapisano", wartosc });
            resolve();
          });
        });
      });
    });
  });

  //2 piętro
  app.put("/swiatla/2/:swiatlo", async (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;

    await plcMutex.runExclusive(async () => {
      await new Promise((resolve) => {
        conn.writeItems(`wej_${swiatlo}`, wartosc, (err) => {
          if (err) return res.status(500).json({ error: "Błąd przy zapisie" });

          conn.removeItems();
          conn.addItems(L2_out_keys);
          conn.readAllItems((err, val) => {
            if (!err) cache.set("swiatlaData", val, lights_ttl);
            res.json({ status: "zapisano", wartosc });
            resolve();
          });
        });
      });
    });
  });

  // PUT: sterowanie roletami

  //3 piętro
  app.put("/rolety/3/:roleta", async (req, res) => {
    const { roleta } = req.params;
    const { wartosc } = req.body;

    await plcMutex.runExclusive(async () => {
      await new Promise((resolve) => {
        conn.writeItems(`wej_${roleta}`, wartosc, (err) => {
          if (err) return res.status(500).json({ error: "Błąd przy zapisie" });

          conn.removeItems();
          conn.addItems(B3_out_keys);
          conn.readAllItems((err, val) => {
            if (!err) cache.set("roletyData", val, blinds_ttl);
            res.json({ status: "zapisano", wartosc });
            resolve();
          });
        });
      });
    });
  });

  //2 piętro
  app.put("/rolety/2/:roleta", async (req, res) => {
    const { roleta } = req.params;
    const { wartosc } = req.body;

    await plcMutex.runExclusive(async () => {
      await new Promise((resolve) => {
        conn.writeItems(`wej_${roleta}`, wartosc, (err) => {
          if (err) return res.status(500).json({ error: "Błąd przy zapisie" });

          conn.removeItems();
          conn.addItems(B2_out_keys);
          conn.readAllItems((err, val) => {
            if (!err) cache.set("roletyData", val, blinds_ttl);
            res.json({ status: "zapisano", wartosc });
            resolve();
          });
        });
      });
    });
  });

  // SSE: strumień świateł
  app.get("/stream/swiatla", (req, res) => {
    res.setHeader("Content-Type", "text/event-stream");
    res.setHeader("Cache-Control", "no-cache");
    res.setHeader("Connection", "keep-alive");

    const sendLights = () => {
      const data = cache.get("swiatlaData"); // Pobieramy wyjścia świateł ze cache
      if (data) res.write(`data: ${JSON.stringify(data)}\n\n`);
    };

    const interval = setInterval(sendLights, lights_timeout);
    req.on("close", () => clearInterval(interval)); //po zamknięciu połączenia, zatrzymujemy wysyłanie danych
  });

  // SSE: strumień temperatury
  app.get("/stream/temperatura", (req, res) => {
    res.setHeader("Content-Type", "text/event-stream");
    res.setHeader("Cache-Control", "no-cache");
    res.setHeader("Connection", "keep-alive");

    const sendTemp = () => {
      const data = cache.get("temperaturaData");
      if (data) res.write(`data: ${JSON.stringify(data)}\n\n`);
    };
    sendTemp(); // Wywołujemy od razu, żeby wysłać aktualne dane
    const interval = setInterval(sendTemp, temperature_timeout); // Ustawiamy interwał na odczyt temperatury
    req.on("close", () => clearInterval(interval));
  });

  // SSE: strumień rolet
  app.get("/stream/rolety", (req, res) => {
    res.setHeader("Content-Type", "text/event-stream");
    res.setHeader("Cache-Control", "no-cache");
    res.setHeader("Connection", "keep-alive");

    const sendBlinds = () => {
      const data = cache.get("roletyData");
      if (data) res.write(`data: ${JSON.stringify(data)}\n\n`);
    };
    sendBlinds(); // Wywołujemy od razu, żeby wysłać aktualne dane
    const interval = setInterval(sendBlinds, blinds_timeout);
    req.on("close", () => clearInterval(interval));
  });
}
