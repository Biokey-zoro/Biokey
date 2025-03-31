document.getElementById("registerFingerprint").addEventListener("click", async () => {
    if (!window.PublicKeyCredential) {
        Swal.fire("Error", "Your browser does not support WebAuthn.", "error");
        return;
    }
    try {
        let response = await fetch("/register", { method: "POST" });
        let options = await response.json();
        options.challenge = Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0));
        options.user.id = Uint8Array.from(atob(options.user.id), c => c.charCodeAt(0));
        
        let credential = await navigator.credentials.create({ publicKey: options });

        let registerResponse = await fetch("/verify", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                credentialId: credential.id,
                clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON)))
            })
        });

        let result = await registerResponse.json();
        Swal.fire(result.message, "", result.status === "success" ? "success" : "error");
    } catch (error) {
        Swal.fire("Error", error.message, "error");
    }
});