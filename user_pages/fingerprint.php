<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fingerprint Registration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register Fingerprint</h2>
        <button id="registerFingerprint">Register Fingerprint</button>
    </div>

    <script>
        document.getElementById("registerFingerprint").addEventListener("click", async () => {
            if (!window.PublicKeyCredential) {
                Swal.fire("Error", "Your browser does not support WebAuthn.", "error");
                return;
            }

            try {
                // Create new WebAuthn credential
                const credential = await navigator.credentials.create({
                    publicKey: {
                        challenge: new Uint8Array(32),
                        rp: { name: "BIOKEY Locker System" },
                        user: {
                            id: new Uint8Array(16),
                            name: "user@example.com",
                            displayName: "User",
                        },
                        pubKeyCredParams: [{ type: "public-key", alg: -7 }],
                        authenticatorSelection: {
                            authenticatorAttachment: "platform", // Use built-in biometrics
                            userVerification: "required",
                        },
                        timeout: 60000,
                    },
                });

                // Send credential ID and public key to backend
                const response = await fetch("register_webauthn.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        credentialId: credential.id,
                        publicKey: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
                    }),
                });

                const result = await response.json();
                Swal.fire(result.message, "", result.status === "success" ? "success" : "error");

            } catch (error) {
                Swal.fire("Error", "Failed to register fingerprint.", "error");
                console.error(error);
            }
        });
    </script>
</body>
</html>
