#include <WiFi.h>
#include <WebSocketsClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// WiFi Credentials
const char* ssid = "HONORniRenz";
const char* password = "pogiRinz";

// WebSocket Server IP & Port
const char* websocketServer = "192.168.252.164";  // Ensure this is the correct IP
const int websocketPort = 3000;

// RFID Module Pins
#define SS_PIN 5
#define RST_PIN 4
MFRC522 rfid(SS_PIN, RST_PIN);

// LCD I2C (16x2)
LiquidCrystal_I2C lcd(0x27, 16, 2);

// GPIO Pins
#define LED_INDICATOR 17
#define LED1 25
#define LED2 26
#define LED3 27
#define RELAY1 32
#define RELAY2 33
#define RELAY3 34

WebSocketsClient webSocket;

// Prevent duplicate scans
unsigned long lastScanTime = 0;
const unsigned long scanCooldown = 2000;  // 2-second cooldown between scans

// ============================
// WebSocket Event Handling
// ============================
void webSocketEvent(WStype_t type, uint8_t *payload, size_t length) {
    switch (type) {
        case WStype_CONNECTED:
            Serial.println("✅ WebSocket Connected!");
            webSocket.sendTXT("ESP32_CONNECTED");
            lcd.clear();
            lcd.print("WS Connected!");
            break;

        case WStype_TEXT: {
            String command = String((char*)payload);
            command.trim();

            if (command.startsWith("PIN_NUMBER:")) {
                int pinNumber = command.substring(11).toInt();
                handlePinActivation(pinNumber);
            } else {
                lcd.clear();
                lcd.print("Unknown Cmd:");
                lcd.setCursor(0, 1);
                lcd.print(command);
            }
            break;
        }

        case WStype_DISCONNECTED:
            Serial.println("⚠️ WebSocket Disconnected!");
            lcd.clear();
            lcd.print("WS Disconnected!");
            lcd.setCursor(0, 1);
            lcd.print("Reconnecting...");
            break;
    }
}

// ============================
// LED & Relay Activation
// ============================
void handlePinActivation(int pinNumber) {
    int targetLED, targetRelay;

    if (pinNumber == 25) {
        targetLED = LED1;
        targetRelay = RELAY1;
    } else if (pinNumber == 26) {
        targetLED = LED2;
        targetRelay = RELAY2;
    } else if (pinNumber == 27) {
        targetLED = LED3;
        targetRelay = RELAY3;
    } else {
        Serial.println("❌ Invalid Pin Number: " + String(pinNumber));
        lcd.clear();
        lcd.print("Invalid Pin!");
        return;
    }

    digitalWrite(targetLED, HIGH);
    digitalWrite(targetRelay, HIGH);
    Serial.println("✅ LED " + String(pinNumber) + " & Relay Activated!");
    lcd.clear();
    lcd.print("LED "+ String(pinNumber) + " ON");
    lcd.setCursor(0, 1);
    lcd.print("Relay ON");

    delay(5000);
    digitalWrite(targetLED, LOW);
    digitalWrite(targetRelay, HIGH);
    Serial.println("⏳ Deactivating LED & Relay");
    lcd.clear();
    lcd.print("Deactivating...");
}

// ============================
// WiFi Connection Handling
// ============================
void connectToWiFi() {
    WiFi.begin(ssid, password);
    lcd.clear();
    lcd.print("Connecting WiFi...");

    unsigned long startTime = millis();
    while (WiFi.status() != WL_CONNECTED && millis() - startTime < 20000) {
        delay(500);
        Serial.print(".");
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\n✅ WiFi Connected!");
        lcd.clear();
        lcd.print("WiFi Connected!");
    } else {
        Serial.println("\n❌ WiFi Failed!");
        lcd.clear();
        lcd.print("WiFi Failed!");
        delay(3000);
        ESP.restart();
    }
}

// ============================
// RFID Scanning Function
// ============================
void scanRFID() {
    if (!rfid.PICC_IsNewCardPresent()) return;
    if (!rfid.PICC_ReadCardSerial()) return;

    unsigned long currentTime = millis();
    if (currentTime - lastScanTime < scanCooldown) {
        return;  // Ignore duplicate scans within the cooldown period
    }
    lastScanTime = currentTime;  // Update last scan time

    String tag = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
        tag += String(rfid.uid.uidByte[i], HEX);
    }
    tag.toUpperCase();

    Serial.println("📤 Sending RFID Tag: " + tag);
    webSocket.sendTXT("SCANNED_TAG:" + tag);

    lcd.clear();
    lcd.print("Scanned:");
    lcd.setCursor(0, 1);
    lcd.print(tag);

    rfid.PICC_HaltA();  // Halt the current card
    rfid.PCD_StopCrypto1();  // Stop encryption to prepare for the next scan
}

// ============================
// Setup Function
// ============================
void setup() {
    Serial.begin(115200);
    WiFi.begin(ssid, password);
    lcd.init();
    lcd.backlight();
    
    lcd.setCursor(0, 0);
    lcd.print("Connecting WiFi...");

    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\n✅ WiFi Connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());

    lcd.clear();
    lcd.print("WiFi Connected!");

    // ====== Initialize WebSocket ======
    Serial.println("🔄 Connecting to WebSocket...");
    webSocket.begin(websocketServer, websocketPort, "/");
    webSocket.onEvent(webSocketEvent);
    webSocket.setReconnectInterval(5000); // Reconnect every 5 seconds

    // Initialize RFID
    SPI.begin();
    rfid.PCD_Init();

    // ✅ Ensure GPIOs are set as OUTPUT
    pinMode(LED1, OUTPUT);
    pinMode(LED2, OUTPUT);
    pinMode(LED3, OUTPUT);
    pinMode(LED_INDICATOR, OUTPUT);
    pinMode(RELAY1, OUTPUT);
    pinMode(RELAY2, OUTPUT);
    pinMode(RELAY3, OUTPUT);

    Serial.println("✅ WebSocket & RFID Initialized!");
}

// ============================
// Main Loop Function
// ============================
void loop() {
    webSocket.loop(); // Keep WebSocket running
    scanRFID();  // Always check for new RFID scans
}
