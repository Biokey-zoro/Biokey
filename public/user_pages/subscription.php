<?php

$_SESSION["selected_plan"] = [
    "duration" => $_POST["duration"] ?? "N/A",
    "description" => $_POST["description"] ?? "N/A",
    "price" => $_POST["price"] ?? "N/A",
    "perks" => $_POST["perks"] ?? []
];
echo "Session ID: " . session_id() . "<br>";
print_r($_SESSION);
    // Redirect to billing.php
    // header("Location: billing.php");
    exit;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Choose the best locker subscription plan that fits your needs. Weekly, Monthly, and Yearly plans available!">
    <title>Pricing Plans</title>

</head>

<body>

    <section class="pricing" id="plans">
        <div class="title">
            <h2>Ready to Get Started?</h2>
            <p class="info">
                Select the perfect locker plan that fits your needs. <br>
                Enjoy secure, convenient, and flexible storage options tailored just for you.
            </p>
        </div>

        <div class="cards-container">

            <!-- Weekly Plan -->
            <div class="inner-container">
                <div class="card">
                    <h3 class="plan-name">Weekly</h3>
                    <p class="description">Perfect for short-term users who need flexibility.</p>
                    <p class="price">$10 <span>/ week</span></p>

                    <form method="POST" action="billing.php">
                        <input type="hidden" name="duration" value="Weekly">
                        <input type="hidden" name="description" value="Perfect for short-term users who need flexibility.">
                        <input type="hidden" name="price" value="10">

                        <input type="hidden" name="perks[]" value="24/7 Access">
                        <input type="hidden" name="perks[]" value="No long-term commitment">
                        <input type="hidden" name="perks[]" value="Basic customer support">

                        <button type="submit" name="subscribe" class="btn btn-primary">Subscribe</button>
                    </form>


                    <ul class="perks">
                        <li>24/7 Access</li>
                        <li>No long-term commitment</li>
                        <li>Basic customer support</li>
                    </ul>
                </div>
            </div>

            <!-- Monthly Plan -->
            <div class="inner-container">
                <div class="card">
                    <h3 class="plan-name">Monthly</h3>
                    <p class="description">Great for regular users looking for a cost-effective plan with full access.</p>
                    <p class="price">$30 <span>/ month</span></p>

                    <form method="POST" action="billing.php">
                        <input type="hidden" name="duration" value="Monthly">
                        <input type="hidden" name="description" value="Great for regular users looking for a cost-effective plan with full access.">
                        <input type="hidden" name="price" value="30">

                        <input type="hidden" name="perks[]" value="24/7 Access">
                        <input type="hidden" name="perks[]" value="Priority support">
                        <input type="hidden" name="perks[]" value="Discount on additional services">
                        <input type="hidden" name="perks[]" value="Free upgrade opportunities">

                        <button type="submit" name="subscribe" class="btn btn-primary">Subscribe</button>
                    </form>

                    <ul class="perks">
                        <li>24/7 Access</li>
                        <li>Priority support</li>
                        <li>Discount on additional services</li>
                        <li>Free upgrade opportunities</li>
                    </ul>
                </div>
            </div>

            <!-- Yearly Plan -->
            <div class="inner-container">
                <div class="card">
                    <h3 class="plan-name">Yearly</h3>
                    <p class="description">Best value! Get unlimited access for a whole year at a discounted rate.</p>
                    <p class="price">$100 <span>/ year</span></p>

                    <form method="POST" action="billing.php">
                        <input type="hidden" name="duration" value="Yearly">
                        <input type="hidden" name="description" value="Best value! Get unlimited access for a whole year at a discounted rate.">
                        <input type="hidden" name="price" value="100">

                        <input type="hidden" name="perks[]" value="24/7 Access">
                        <input type="hidden" name="perks[]" value="VIP priority support">
                        <input type="hidden" name="perks[]" value="Exclusive discounts & offers">
                        <input type="hidden" name="perks[]" value="Free premium upgrades">
                        <input type="hidden" name="perks[]" value="Complimentary bonus services">

                        <button type="submit" name="subscribe" class="btn btn-primary">Subscribe</button>
                    </form>

                    <ul class="perks">
                        <li>24/7 Access</li>
                        <li>VIP priority support</li>
                        <li>Exclusive discounts & offers</li>
                        <li>Free premium upgrades</li>
                        <li>Complimentary bonus services</li>
                    </ul>
                </div>
            </div>


        </div>
    </section>

</body>

</html>