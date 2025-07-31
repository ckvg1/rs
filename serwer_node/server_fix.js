var nodes7 = require("nodes7");
const express = require("express");
const app = express();
const cors = require("cors");
const port = 3000;
app.use(cors());
var conn = new nodes7();
app.use(express.json());

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

function connected(err) {
  conn.setTranslationCB(function (tag) {
    return variables[tag];
  });

  app.get("/", (req, res) => {
    return res.send("Serwer działa");
  });

  app.get("/swiatla/3/wyjscia", (req, res) => {
    conn.removeItems();
    conn.addItems(Object.keys(zmienne3_wyjscia));
    conn.readAllItems((err, val) => {
      return res.json(val);
    });
  });

  app.get("/temperatura/3", (req, res) => {
    conn.removeItems();
    conn.addItems(Object.keys(zmienne3_temperatura));
    conn.readAllItems((err, val) => {
      if (err) {
        return;
      }
      return res.json(val);
    });
  });

  app.put("/swiatla/3/:swiatlo", (req, res) => {
    const { swiatlo } = req.params;
    const { wartosc } = req.body;
    conn.removeItems();
    conn.addItems([`wej_${swiatlo}`]);
    conn.writeItems(`wej_${swiatlo}`, wartosc);

    conn.readAllItems((err, val) => {
      if (err) {
        return;
      }
      return res.json(val);
    });
  });
}
