document.getElementById("registerFingerprint").addEventListener("click", async () => {

    document.addEventListener("DOMContentLoaded", () => {
        if (!window.PublicKeyCredential) {
            Swal.fire("Error", "Your browser does not support WebAuthn.", "error");
        }
    });

    
    if (!window.PublicKeyCredential) {
        Swal.fire("Error", "Your browser does not support WebAuthn.", "error");
        return;
    }

    try {
        let response = await fetch("fprint_function.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "register" }),
        });

        let options = await response.json();
        if (options.status !== "success") throw new Error(options.message);

        let credential = await navigator.credentials.create({
            publicKey: {
                challenge: Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0)),
                rp: options.rp,
                user: {
                    id: Uint8Array.from(atob(options.user.id), c => c.charCodeAt(0)),
                    name: options.user.name,
                    displayName: options.user.displayName
                },
                pubKeyCredParams: [
                    { type: "public-key", alg: -7 },    // ES256 (Elliptic Curve)
                    { type: "public-key", alg: -257 }  // RS256 (RSA)
                ],
                authenticatorSelection: {
                    authenticatorAttachment: "platform", // Use built-in biometrics
                    userVerification: "required"
                },
                timeout: 60000
            }
        });

        let registerResponse = await fetch("fprint_function.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "verify",
                credentialId: credential.id,
                publicKey: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON)))
            })
        });

        let result = await registerResponse.json();
        Swal.fire(result.message, "", result.status === "success" ? "success" : "error");

    } catch (error) {
        Swal.fire("Error", error.message, "error");
        console.error(error);
    }
});
