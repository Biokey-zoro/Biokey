import express from "express";
import db from "./db.js"; // Ensure `.js` extension for ES Modules
import base64url from "base64url";
import crypto from "crypto";

const router = express.Router();

router.post("/register", async (req, res) => {
    let challenge = base64url.encode(crypto.randomBytes(32));
    res.json({
        challenge: challenge,
        rp: { name: "BIOKEY Locker System" },
        user: {
            id: base64url.encode(crypto.randomBytes(16)),
            name: "user@example.com",
            displayName: "User"
        },
        pubKeyCredParams: [
            { type: "public-key", alg: -7 },  // ES256
            { type: "public-key", alg: -257 } // RS256
        ],
        timeout: 60000,
        authenticatorSelection: {
            authenticatorAttachment: "platform",
            userVerification: "required"
        }
    });
});

router.post("/verify", async (req, res) => {
    const { credentialId, clientDataJSON } = req.body;
    let userId = "user-id";
    let query = "INSERT INTO webauthn_credentials (user_id, credential_id, client_data) VALUES (?, ?, ?)";
    
    db.query(query, [userId, credentialId, clientDataJSON], (err) => {
        if (err) return res.json({ status: "error", message: "Failed to register fingerprint." });
        res.json({ status: "success", message: "Fingerprint registered successfully." });
    });
});

// ✅ Use `export default` instead of `module.exports`
export default router;
