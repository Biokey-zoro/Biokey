<?php
require '../gen_functions/config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscribe"])) {
    $_SESSION["selected_plan"] = [
        "duration" => $_POST["duration"] ?? "N/A",
        "description" => $_POST["description"] ?? "N/A",
        "price" => $_POST["price"] ?? "N/A",
        "perks" => $_POST["perks"] ?? []
    ];

    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    exit;
}

$selected_plan = $_SESSION["selected_plan"] ?? null;
$first_name = $_SESSION['first_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? '';
$mobile_no = $_SESSION['mobile_no'] ?? '';
$email = $_SESSION['email'] ?? '';

if (!$selected_plan) {
    header("Location: subscription.php");
    exit;
}

$darkMode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled' ? 'dark-mode' : '';
$isDarkMode = $darkMode === 'dark-mode' ? 'checked' : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/subscription.css">
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Billing</title>
</head>

<body class="<?php echo $darkMode; ?>">

    <div class="billing-container">
        <div class="customer-info">
            <h2>
                <a href="javascript:history.back()" class="back-arrow">
                    <i class="fa-solid fa-arrow-left" style="color: #000000;"></i>
                </a>
                Billing
            </h2>

            <form id="billingForm">
                <h3 class="information">Customer Information</h3>
                <div class="input-group">
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="First Name" required>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Last Name" required>
                </div>

                <div class="input-group">
                    <select id="country-code">
                        <option value="+63">🇵🇭 +63</option>
                        <option value="+1">🇺🇸 +1</option>
                        <option value="+44">🇬🇧 +44</option>
                        <option value="+61">🇦🇺 +61</option>
                        <option value="+55">🇧🇷 +55</option>
                        <option value="+1">🇨🇦 +1</option>
                    </select>
                    <input type="text" name="mobile" value="<?php echo htmlspecialchars($mobile_no); ?>" placeholder="Phone Number" required>
                </div>

                <div class="input-group">
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Gmail" required>
                </div>

                <div class="input-group">
                    <input type="text" name="address" placeholder="Address" required>
                    <input type="text" name="postal" placeholder="Postal" required>
                </div>
            </form>
        </div>

        <div class="plan-summary">
            <h3 class="plan-header">Subscription</h3>
            <div class="plan">
                <div class="plan-details">
                    <?php if ($selected_plan): ?>
                        <div class="card-container">
                            <div class="card">
                                <h3 class="duration"><?= htmlspecialchars($selected_plan["duration"]) ?></h3>
                                <p class="description"><?= htmlspecialchars($selected_plan["description"]) ?></p>
                                <p class="price">$<?= htmlspecialchars($selected_plan["price"]) ?></p>
                                <ul class="perks">
                                    <?php foreach ($selected_plan["perks"] as $perk): ?>
                                        <li><?= htmlspecialchars($perk) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="selected">No plan selected.</p>
                    <?php endif; ?>
                </div>
            </div>

            <form action="create_checkout.php" method="POST">
    <input type="hidden" name="duration" value="<?= htmlspecialchars($_SESSION["selected_plan"]["duration"] ?? '') ?>">
    <input type="hidden" name="description" value="<?= htmlspecialchars($_SESSION["selected_plan"]["description"] ?? '') ?>">
    <input type="hidden" name="price" value="<?= htmlspecialchars($_SESSION["selected_plan"]["price"] ?? '') ?>">
    <input type="hidden" name="perks" value="<?= htmlspecialchars(json_encode($_SESSION["selected_plan"]["perks"] ?? [])) ?>">
    
    <button type="submit" class="payment-btn">Proceed to Payment</button>
</form>


            <div class="terms">
                <input type="checkbox" id="agreeTerms" required>
                <span>By confirming the order, I accept the <a href="#">terms of the user agreement</a>.</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("#billingForm");
            const paymentBtn = document.querySelector(".payment-btn");
            const termsCheckbox = document.querySelector("#agreeTerms");

            function validateForm() {
                let isValid = true;
                let emptyFields = [];
                let inputs = form.querySelectorAll("input[required]");

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.style.border = "2px solid red";
                        emptyFields.push(input.placeholder || input.name);
                        isValid = false;
                    } else {
                        input.style.border = "1px solid #ccc";
                    }
                });

                if (!termsCheckbox.checked) {
                    isValid = false;
                }

                if (!isValid) {
                    let message = emptyFields.length ?
                        `Please fill in: <b>${emptyFields.join(", ")}</b>.` :
                        "You must agree to the terms.";

                    Swal.fire({
                        icon: "error",
                        title: "Oops!",
                        html: message,
                        confirmButtonColor: "#6C63FF",
                    });
                }

                return isValid;
            }

            paymentBtn.addEventListener("click", function(event) {
    event.preventDefault();
    
    let formData = new FormData(form);
    formData.append("duration", "<?= htmlspecialchars($selected_plan["duration"]) ?>");
    formData.append("description", "<?= htmlspecialchars($selected_plan["description"]) ?>");
    formData.append("price", "<?= htmlspecialchars($selected_plan["price"]) ?>");
    formData.append("perks", "<?= htmlspecialchars(json_encode($selected_plan["perks"])) ?>");

    console.log("FormData being sent:", Object.fromEntries(formData)); 

    fetch("create_checkout.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response from server:", data); 
        if (data.error) {
            Swal.fire({
                icon: "error",
                title: "Payment Failed!",
                text: data.error,
                confirmButtonColor: "#6C63FF",
            });
        } else {
            window.location.href = data.checkout_url;
        }
    })
    .catch(error => {
        console.error("Fetch Error:", error);
        Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Something went wrong. Please try again.",
            confirmButtonColor: "#6C63FF",
        });
    });
});

        });
    </script>

</body>
</html>
