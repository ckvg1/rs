const nodes7 = require("nodes7");
const express = require("express");
const cors = require("cors");
const { Mutex } = require("async-mutex");
const NodeCache = require("node-cache");
const app = express();
const port = 3000;
app.use(cors());
app.use(express.json());
const fs = require("fs");
const readConn = new nodes7();
const writeConn = new nodes7();
const readMutex = new Mutex();
const writeMutex = new Mutex();
const cache = new NodeCache({ stdTTL: 0, checkperiod: 1 });

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

// 1 pietro
// const L1_in = require("./variables/floor1/L1_in");
// const L1_out = require("./variables/floor1/L1_out");
// const T1 = require("./variables/floor1/T1");
// const B1_in = require("./variables/floor1/B1_in");
// const B1_out = require("./variables/floor1/B1_out");

// parter
// const L0_in = require("./variables/floor0/L0_in");
// const L0_out = require("./variables/floor0/L0_out");
// const T0 = require("./variables/floor0/T0");
// const B0_in = require("./variables/floor0/B0_in");
// const B0_out = require("./variables/floor0/B0_out");

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
  // odkomentuj, jak bedziesz robil 1 pietro i parter
  // ...L1_in,
  // ...L1_out,
  // ...T1,
  // ...B1_in,
  // ...B1_out,
  // ...L0_in,
  // ...L0_out,
  // ...T0,
  // ...B0_in,
  // ...B0_out,
};

const T3_keys = Object.keys(T3);
const L3_out_keys = Object.keys(L3_out);
const L3_in_keys = Object.keys(L3_in);
const B3_out_keys = Object.keys(B3_out);
const T2_keys = Object.keys(T2);
const L2_out_keys = Object.keys(L2_out);
const L2_in_keys = Object.keys(L2_in);
const B2_out_keys = Object.keys(B2_out);

// odkomentuj, jak bedziesz robil 1 pietro i parter

//const T1_keys = Object.keys(T1);
//const L1_out_keys = Object.keys(L1_out);
//const L1_in_keys = Object.keys(L1_in);
//const B1_out_keys = Object.keys(B1_out);
//const T0_keys = Object.keys(T0);
//const L0_out_keys = Object.keys(L0_out);
//const L0_in_keys = Object.keys(L0_in);
//const B0_out_keys = Object.keys(B0_out);

const temperature_timeout = 10000; // Odczyt temperatury co 10000 ms (10 sekund)
const lights_timeout = 200; // Odczyt świateł co 200ms (0.2 sekundy)
const blinds_timeout = 300; // Odczyt rolet co 500ms (0.5 sekundy)

/* 
Ustawiamy TTL (time to live) dla danych w cache
TTL to czas, przez jaki dane będą przechowywane w cache przed ich usunięciem
Wartości te są ustawione na podstawie czasu, jaki potrzebujemy do odczytu danych z PLC i ich aktualizacji w cache
Można je dostosować w zależności od potrzeb aplikacji i częstotliwości zmian danych
Wartości te są w sekundach
*/
const temperature_ttl = 11; // TTL dla temperatury
const lights_ttl = 0.4; // TTL dla świateł
const blinds_ttl = 0.6; // TTL dla rolet

/*
Klucze do wyjść, potrzebne zeby zapisywać wartosci do cache. 
Wszystkie klucze są (i powinny!) byc unikalne, więc używamy Set do usunięcia przypadkowych duplikatów. (co nie powinno mieć miejsca, ale lepiej dmuchać na zimne ;))
Zmienne są zdefiniowane w plikach variables/floorX/*.js
*/

const LIGHT_KEYS = [...new Set([...L2_out_keys, ...L3_out_keys])];
const TEMPERATURE_KEYS = [...new Set([...T3_keys, ...T2_keys])];
const BLINDS_KEYS = [...new Set([...B3_out_keys, ...B2_out_keys])];

/* 
Do rozbudowy: usunąc to co wyżej i wstawic: 

const LIGHT_KEYS = [...new Set([...L0_out_keys, ...L1_out_keys, ...L2_out_keys, ...L3_out_keys])]; 
const TEMPERATURE_KEYS = [...new Set([...T0_keys, ...T1_keys, ...T2_keys, ...T3_keys])];
const BLINDS_KEYS = [...new Set([...B0_out_keys, ...B1_out_keys, ...B2_out_keys, ...B3_out_keys])];
*/

// Inicjalizcja serwera Express
const server = app.listen(port, "0.0.0.0", () => {
  console.log(`Serwer nasłuchuje na porcie ${port}`);
});

// Inicjalizacja połączenia z PLC (odczyt)
readConn.initiateConnection(
  {
    port: 102,
    host: "192.168.25.1",
    rack: 0,
    slot: 1,
    debug: true,
    doNotOptimize: true, // Wyłączamy optymalizacje, żeby mieć pełną kontrolę nad odczytami/zapisami
  },
  connectedRead //po połączeniu z PLC, wywołujemy funkcję connectedRead
);

/* 
  Funkcja odpowiedzialna za odczyt danych po starcie serwera.
  Odczytuje światła i temperatury i zapisuje je do cache.

  Używamy mutexa, żeby zapewnić, że tylko jeden odczyt będzie wykonywany w danym momencie.
  Dzięki temu unikamy konfliktów przy odczycie/zapisie do PLC.

  Funkcja jest wywoływana tylko raz po starcie serwera, żeby wstępnie załadować dane do cache.

 */
function readAfterStartup() {
  readMutex.runExclusive(async () => {
    readConn.removeItems();
    readConn.addItems(LIGHT_KEYS);
    await new Promise((resolve) => {
      readConn.readAllItems((err, val) => {
        if (!err) cache.set("swiatlaData", val, lights_ttl); //time to live
        resolve();
      });
    });
  });

  readMutex.runExclusive(async () => {
    readConn.removeItems();
    readConn.addItems(TEMPERATURE_KEYS);
    await new Promise((resolve) => {
      readConn.readAllItems((err, val) => {
        if (!err) cache.set("temperaturaData", val, temperature_ttl); //time to live
        resolve();
      });
    });
  });

  readMutex.runExclusive(async () => {
    readConn.removeItems();
    readConn.addItems(BLINDS_KEYS);
    await new Promise((resolve) => {
      readConn.readAllItems((err, val) => {
        if (!err) cache.set("roletyData", val, blinds_ttl); //time to live
        resolve();
      });
    });
  });
}
function connectedRead(err) {
  if (err) {
    console.error("Błąd połączenia z PLC:", err);
    return;
  }
  // Połączenie readConn udało się, więc możemy rozpocząć odczyty i nawiązać połączenie writeConn
  writeConn.initiateConnection(
    {
      port: 102,
      host: "192.168.25.1",
      rack: 0,
      slot: 1,
      debug: true,
      doNotOptimize: true, // Wyłączamy optymalizacje, żeby mieć pełną kontrolę nad odczytami/zapisami
    },
    connectedWrite // po połączeniu z PLC, wywołujemy funkcję connectedWrite
  );
  readAfterStartup(); // Pierwszy odczyt danych po starcie serwera (potem juz co okreslony czas)

  // Ustawienie callbacka do tłumaczenia tagów na zmienne
  // https://github.com/plcpeople/nodeS7/tree/master?tab=readme-ov-file#nodes7settranslationcbtranslator
  readConn.setTranslationCB((tag) => variables[tag]);

  // Cykliczne odczyty swiatel do cache co (lights_timeout) ms
  setInterval(() => {
    readMutex.runExclusive(async () => {
      // światła
      readConn.removeItems(); //usuwamy stare klucze
      readConn.addItems(LIGHT_KEYS); //dodajemy nowe klucze (światła)
      await new Promise((resolve) => {
        readConn.readAllItems((err, val) => {
          if (!err) cache.set("swiatlaData", val, lights_ttl); //zapisujemy do cache, jeśli nie ma błędu
          resolve();
        });
      });
    });
  }, lights_timeout);

  // Cykliczne odczyty temperatury do cache co (temperature_timeout) ms
  setInterval(() => {
    readMutex.runExclusive(async () => {
      readConn.removeItems(); // usuwamy stare klucze
      readConn.addItems(TEMPERATURE_KEYS); //dodajemy nowe klucze (temperatura)
      await new Promise((resolve) => {
        readConn.readAllItems((err, val) => {
          if (!err) cache.set("temperaturaData", val, temperature_ttl); //zapisujemy do cache, jeśli nie ma błędu
          resolve();
        });
      });
    });
  }, temperature_timeout);

  // Cykliczne odczyty rolet co (blinds_timeout) ms
  setInterval(() => {
    readMutex.runExclusive(async () => {
      readConn.removeItems(); // usuwamy stare klucze
      readConn.addItems(BLINDS_KEYS); //dodajemy nowe klucze (rolety)
      await new Promise((resolve) => {
        readConn.readAllItems((err, val) => {
          if (!err) cache.set("roletyData", val, blinds_ttl); //zapisujemy do cache, jeśli nie ma błędu
          resolve();
        });
      });
    });
  }, blinds_timeout);
}

function connectedWrite(err) {
  if (err) {
    console.error("Błąd połączenia z PLC:", err);
    return;
  }

  // Ustawienie callbacka do tłumaczenia tagów na zmienne
  writeConn.setTranslationCB((tag) => variables[tag]);

  // GET: ping
  app.get("/", (req, res) => {
    res.send("Serwer działa");
  });

  //debug (do usuniecia, ale na razie przydatne)
  app.get("/debug/cache", (req, res) => {
    res.json(
      cache.keys().reduce((out, k) => {
        out[k] = cache.get(k);
        return out;
      }, {})
    );
  });

  // PUT: sterowanie światłem
  app.put("/swiatla/:swiatlo", async (req, res) => {
    const { swiatlo } = req.params; // np. "l3_1_1" (zdefiniowane w HTML, parametr funkcji wyslijTrue(swiatlo))
    const { wartosc } = req.body; // true lub false

    await writeMutex.runExclusive(
      // używamy mutexa, żeby zapewnić, że tylko jeden zapis będzie wykonywany w danym momencie
      () =>
        new Promise((resolve) => {
          // przypisujemy do zmiennej result wynik funkcji writeItems
          // writeItems zwraca 0, jeśli zapis się udał, lub inną wartość, jeśli zapis się nie powiódł
          const result = writeConn.writeItems(
            `wej_${swiatlo}`, // dodajemy wej_ przed swiatlo, żeby dopasować do zmiennych w pliku variables/floorX/LX_in.js
            wartosc,
            (err) => {
              if (err) {
                console.error("Błąd przy zapisie:", err);
                res.status(500).json({ error: "Błąd przy zapisie" });
              } else {
                res.json({ status: "zapisano", wartosc });
                var godzina = new Date().toISOString();
                console.info(swiatlo, wartosc, godzina, req.ip); // logujemy operacje jaką wykonaliśmy
              }
              resolve(); // mutex zostanie zwolniony dopiero po zakończeniu callbacka
            }
          );

          // Jezeli result nie jest 0, to znaczy, że zapis został odrzucony (np. inny zapis już trwa)
          // W takim przypadku zwracamy błąd 409 (Conflict) i nie wykonujemy dalszych operacji
          if (result != 0) {
            console.error("writeItems odrzucone: zapis już trwa");
            res
              .status(409)
              .json({ error: "Zapis w trakcie, spróbuj ponownie" });
            resolve(); // zwalniamy mutex nawet jeśli writeItems nie zadziałał (zeby kolejne zapisy mialy szanse sie wykonac)
          }
        })
    );
  });

  // PUT: sterowanie roletami
  app.put("/rolety/:roleta", async (req, res) => {
    const { roleta } = req.params; // np. "b2_r4_1" (zdefiniowane w HTML, parametr funkcji roletaWlacz(roleta))
    const { wartosc } = req.body; // true lub false

    // Funkcja działa tak samo jak w przypadku świateł, ale oddzielamy ją (i cały endpoint), żeby było jasne, że chodzi o rolety
    await writeMutex.runExclusive(async () => {
      await new Promise((resolve) => {
        let result = writeConn.writeItems(`wej_${roleta}`, wartosc, (err) => {
          if (err) return res.status(500).json({ error: "Błąd przy zapisie" });

          res.json({ status: "zapisano", wartosc });
          var godzina = new Date().toISOString();
          console.info(roleta, wartosc, godzina, req.ip);
          return resolve();
        });
        if (result != 0) {
          res.json({ status: "blad przy zapisie" });
          console.error("Błąd przy zapisie rolety!");
          return resolve();
        }
      });
    });
  });

  // SSE: strumienie danych (Server-Sent Events)
  // Używamy ich do wysyłania aktualnych danych z cache
  // Komunikacja serwer -> klient

  // SSE: strumień świateł
  app.get("/stream/swiatla", (req, res) => {
    res.setHeader("Content-Type", "text/event-stream");
    res.setHeader("Cache-Control", "no-cache");
    res.setHeader("Connection", "keep-alive");

    const sendLights = () => {
      const data = cache.get("swiatlaData"); // Pobieramy wyjścia świateł z cache
      if (data) res.write(`data: ${JSON.stringify(data)}\n\n`);
    };
    s;
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

  // PUT: ustawianie harmonogramu
  // PUT: aktualizacja/dodanie wpisów w harmonogramie
  app.put("/harmonogram/add", (req, res) => {
    const noweWartosci = req.body; // np. { "all_OFF_l2": "17:30", "all_OFF_l3": "18:00" }

    let harmonogram = {};

    // Nadpisz lub dodaj nowe wartości
    Object.entries(noweWartosci).forEach(([key, value]) => {
      harmonogram[key] = value;
    });

    fs.writeFile(
      "harmonogram.json",
      JSON.stringify(harmonogram, null, 2),
      (err) => {
        if (err) {
          console.error("Błąd zapisu pliku harmonogram.json:", err);
          return res
            .status(500)
            .json({ error: "Błąd zapisu pliku harmonogramu" });
        }

        console.log("Harmonogram zaktualizowany:", noweWartosci);
        res.json({ status: "harmonogram ustawiony", harmonogram });
      }
    );
  });

  // GET: pełny harmonogram
  app.get("/harmonogram", (req, res) => {
    fs.readFile("harmonogram.json", "utf8", (err, data) => {
      if (err) {
        console.error("Błąd odczytu pliku harmonogram.json:", err);
        return res
          .status(500)
          .json({ error: "Błąd odczytu pliku harmonogramu" });
      }

      try {
        const harmonogram = JSON.parse(data);
        res.json(harmonogram);
      } catch (parseError) {
        console.error("Błąd parsowania JSON:", parseError);
        res.status(500).json({ error: "Błąd parsowania danych harmonogramu" });
      }
    });
  });
  // Cykliczne sprawdzanie harmonogramu i wwylaczanie swiatel.
  // Automatyczne wyłączanie świateł na podstawie harmonogramu
  setInterval(() => {
    console.log("Próba odczytu harmonogramu");
    fs.readFile("harmonogram.json", "utf8", (err, data) => {
      if (err) {
        console.error("Błąd odczytu pliku harmonogram.json:", err);
        return;
      }

      const harmonogram = JSON.parse(data);
      console.log("Godzina z harmonogramu: ", harmonogram);
      const currentHour = new Date().toLocaleTimeString().slice(0, 5);
      console.log("Aktualna godzina: ", currentHour);
      Object.entries(harmonogram).forEach(([key, value]) => {
        if (value === currentHour) {
          console.log("Aktualna godzina taka sama jak w harmonogramie.");
          writeMutex.runExclusive(async () => {
            console.log(`Wyłączam ${key} według harmonogramu`);

            // l3
            await new Promise((resolve) => {
              writeConn.writeItems(`wej_${key}`, true, (err) => {
                if (err) console.error(`Blad wej_${key} na true:`, err);
                setTimeout(() => {
                  writeConn.writeItems(`wej_${key}`, false, (err) => {
                    if (err) console.error(`Blad wej_${key} na false:`, err);
                    console.log(`Wyłaczenie swiatla ${key} powiodlo sie. `);
                    resolve();
                  });
                }, 100);
              });
            });
          });
        }
      });
    });
  }, 60000);
}
