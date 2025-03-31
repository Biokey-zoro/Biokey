import { WebSocketServer } from "ws";
import express from "express";
import http from "http";
import bodyParser from "body-parser";
import mysql from "mysql2/promise";
import axios from "axios";

// Create Express App & HTTP Server
const app = express();
const server = http.createServer(app);
const wss = new WebSocketServer({ server });

app.use(express.static("public"));
app.use(bodyParser.json());

// Database Connection
const db = mysql.createPool({
    host: "localhost",
    user: "root",
    password: "",  // Adjust based on your XAMPP/MariaDB settings
    database: "lockerv1_db"
});

// WebSocket Server
wss.on("connection", (ws) => {
    console.log("🔗 New Client Connected");

    ws.on("message", async (message) => {
        const cleanMessage = message.toString().trim();
        console.log(`📩 Received: ${cleanMessage}`);

        if (cleanMessage === "ESP32_CONNECTED") {
            console.log("✅ ESP32 Successfully Connected!");
        } else if (cleanMessage === "START_SCANNING") {
            console.log("📡 Activating RFID Scanning...");
            broadcastToClients("START_SCANNING");
        } else if (cleanMessage.startsWith("RFID_TAG:")) {
            const tag = cleanMessage.replace("RFID_TAG:", "").trim();
            console.log(`🟦 RFID Tag Received: ${tag}`);
            broadcastToClients(JSON.stringify({ type: "RFID_TAG", tag }));
        } else if (cleanMessage.startsWith("SCANNED_TAG:")) {
            const scannedTag = cleanMessage.replace("SCANNED_TAG:", "").trim();
            console.log(`🟦 Scanned RFID: ${scannedTag}`);

            if (!scannedTag) {
                console.warn("⚠️ Empty RFID tag.");
                ws.send("ERROR: Empty RFID tag received.");
                return;
            }

            try {
                const [results] = await db.query(`
                    SELECT u.user_id, l.locker_id, l.pin_number
                    FROM registered_rfid r
                    JOIN user_tbl u ON r.user_id = u.user_id
                    JOIN lockers l ON u.user_id = l.user_id
                    WHERE r.rfid_tag = ? AND r.status = 'available'
                    LIMIT 1
                `, [scannedTag]);

                if (results.length === 0) {
                    console.log("🚫 Unregistered or inactive RFID detected.");
                    ws.send("ERROR: Unregistered RFID or inactive status.");
                    console.log(results[0].pin_number);
                    return;
                }

                const pinNumber = results[0].pin_number;
                if (!pinNumber) {
                    console.log(`⚠️ No pin number found for RFID: ${scannedTag}`);
                    ws.send("ERROR: No assigned pin for this RFID.");
                    return;
                }

                console.log(`🔐 Sending Pin Number to ESP32: ${pinNumber}`);
                broadcastToClients(`PIN_NUMBER:${pinNumber}`);

            } catch (error) {
                console.error(`❌ Database Error: ${error.message}`);
                ws.send("ERROR: Database error. Please try again.");
            }
        } else {
            broadcastToClients(cleanMessage);
        }
    });

    ws.on("close", () => console.log("❌ Client Disconnected"));
});

// Function to Broadcast Messages to All Clients
function broadcastToClients(message) {
    wss.clients.forEach((client) => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(message);
        }
    });
}

// API Route: Validate Users and Control LED
app.get("/validate-users/:scanned_tag", async (req, res) => {
    const scannedTag = req.params.scanned_tag;

    try {
        // Step 1: Find RFID in the database
        const [rfidResult] = await db.query(
            "SELECT user_id FROM registered_rfid WHERE rfid_tag = ?",
            [scannedTag]
        );

        if (rfidResult.length === 0) {
            return res.status(404).json({ success: false, message: "RFID tag not found." });
        }

        const userId = rfidResult[0].user_id;

        // Step 2: Find assigned locker pin
        const [pinResult] = await db.query(
            "SELECT pin_number FROM lockers_pin_hw WHERE user_id = ?",
            [userId]
        );

        if (pinResult.length === 0) {
            return res.status(404).json({ success: false, message: "No pin assigned to this user." });
        }

        const pinNumber = pinResult[0].pin_number;

        // Step 3: Send to ESP32
        await axios.post("http://<ESP32_IP_ADDRESS>/control-led", { pin: pinNumber });

        res.json({ success: true, message: `LED with PIN ${pinNumber} activated.` });

    } catch (error) {
        console.error("❌ Error:", error);
        res.status(500).json({ error: "Failed to process request." });
    }
});

// API Route: Fetch Users
app.get("/get-users", async (req, res) => {
    try {
        const [users] = await db.query("SELECT user_id, first_name, last_name FROM user_tbl");
        res.json(users);
    } catch (error) {
        console.error("❌ Error fetching users:", error);
        res.status(500).json({ error: "Failed to fetch users." });
    }
});

// API Route: Save RFID with Assigned User
app.post("/save-rfid", async (req, res) => {
    const { rfidTag, userId } = req.body;

    try {
        await db.query(
            "INSERT INTO rfid_tbl (rfid_tag, user_id, rfid_status) VALUES (?, ?, ?)",
            [rfidTag, userId, "active"]
        );
        res.json({ message: "✅ RFID assigned successfully!" });
    } catch (error) {
        console.error("❌ Error saving RFID:", error);
        res.status(500).json({ message: "❌ Failed to assign RFID." });
    }
});

// Start the WebSocket & Express Server
const PORT = 3000;
server.listen(PORT, () => console.log(`🚀 Server running on http://localhost:${PORT}`));
