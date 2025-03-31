import { WebSocketServer } from "ws";

const wss = new WebSocketServer({ port: 3000 });

wss.on("connection", (ws) => {
    console.log("✅ Client Connected");

    ws.on("message", (message) => {
        console.log("📩 Received:", message.toString());

        if (message.toString().startsWith("RFID_TAG:")) {
            console.log("🔍 Scanned RFID:", message.toString().replace("RFID_TAG:", ""));
            ws.send("RFID_RECEIVED");
        }
    });

    ws.on("close", () => {
        console.log("❌ Client Disconnected");
    });
});

console.log("🚀 WebSocket server running on ws://localhost:3030");
