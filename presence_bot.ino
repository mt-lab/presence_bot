/*
    Created by Ivan Grokhotkov, 2015.
    Modified by ku-dmitry of MT.lab https://mtlab.su, 2020.
    This example is in public domain.
*/

#include <ESP8266WiFi.h>

#ifndef STASSID
#define STASSID "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
#define STAPSK  "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
#endif

const char* ssid = STASSID;
const char* password = STAPSK;

const char* host = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
const int Port = 80;
void setup() {
  Serial.begin(115200);
  Serial.println();
  Serial.print("connecting to ");
  Serial.println(ssid);
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
    // Use WiFiClientSecure class to create TLS connection
  WiFiClient client;
  Serial.print("connecting to ");
  Serial.println(host);

  if (!client.connect(host, Port)) {
    Serial.println("connection failed");
    return;
  }

  String url = "/.updateTimestamp.php";
  Serial.print("requesting URL: ");
  Serial.println(url);

  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "User-Agent: Node_Presence_Checker\r\n" +
               "Accept: text/html\r\n" +
               "Connection: close\r\n\r\n");

  Serial.println("request sent");
  while (client.connected()) {
    String line = client.readStringUntil('\n');
    if (line == "\r") {
      Serial.println("headers received");
      break;
    }
  }
  Serial.println("closing connection");
  delay(60000);                 //частота обновления таймстампа на сервере
}
