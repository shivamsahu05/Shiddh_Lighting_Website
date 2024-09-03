<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>
    <!-- Ionicons Script -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Internal CSS -->
    <style>
        /* Reset and basic styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap; /* Added flex-wrap for responsiveness */
        }

        header {
            width: 100%;
            text-align: left;
            margin-bottom: 30px;
        }

        header img {
            max-width: 250px; /* Adjust size as needed */
            margin-bottom: 10px; /* Space between logo and heading */
        }

        h1 {
            font-size: 3rem;
            color: #333;
            text-transform: uppercase;
            text-align: center;
        }

        .products {
            flex: 2;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: calc(33.33% - 20px);
            margin-bottom: 20px;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card img {
            width: 100%;
            display: block;
            border-radius: 8px 8px 0 0; /* Rounded corners only at the top */
            transition: transform 0.3s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .card-body {
            padding: 20px;
            background-color: #f9f9f9; /* Light gray background for card body */
            border-radius: 0 0 8px 8px; /* Rounded corners only at the bottom */
        }

        .card-body h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }

        .card-body p {
            color: #666;
            margin-bottom: 15px;
            text-align: center;
        }

        .order-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 0 auto;
            display: block;
        }

        .order-button:hover {
            background-color: #0056b3;
        }

        .checkout-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            margin-top: 10px;
        }

        .checkout-button:hover {
            background-color: #0056b3;
        }

        .cart {
            flex: 1;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            position: sticky;
            top: 20px;
        }

        .cart h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
        }

        .clear-all-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 0 auto 20px;
            display: block;
            width: 100%;
        }

        .clear-all-button:hover {
            background-color: #c82333;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        ul li {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
            margin-bottom: 10px;
        }

        ul li:last-child {
            border-bottom: none;
        }
        #totalAmount{
            color:blue;
            text-align:center;
            font-weight: bold;
            font-size:20px;
        }
        #total{
            color: red;
            text-align:center;
            font-weight: bold;
            font-size:20px;
        }
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="tel"],
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: none;
            transition: border-color 0.3s ease;
        }

        input[type="tel"]:focus,
        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
        }

        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <header>
        <!-- Logo or image -->
        <img src="images/logob21.png" alt="Logo">
        <h1><u>Our Products</u></h1>
    </header>

    <div class="container">
        <section class="products">
            <!-- Product Cards -->
            <div class="card">
                <img src="images/led-night-bulb.jpg" alt="Product 1">
                <div class="card-body">
                    <h3>0.5 Watt Bulb</h3>
                    <p>Watt: 0.5<br>colors bulb: 7 Colors<br>Made in: INDIA<br>min. qty: 100<br>Price: ₹ 20/-</p>
              
                <button class="order-button" onclick="addToCart('0.5 Watt Bulb - <b>100Pcs ', 2000)">Add to Cart</button>
            
                </div>
            </div>

            <div class="card">
                <img src="images/philips-led-bulb.jpg" alt="Product 2">
                <div class="card-body">
                    <h3>Ph Bulb</h3>
                    <p>Watt: 9<br>Type of bulb: Cool White<br>Made in: INDIA<br>min. qty: 100<br>MRP: ₹60</p>
                
                <button class="order-button" onclick="addToCart('9 Watt Philips Type Bulb - <b>100Pcs', 6000)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-t-bulb.jpg" alt="Product 3">
                <div class="card-body">
                <h3>T Bulb</h3>
                <p>Watt: 10 W<br>Type of bulb: Cool White<br>Made in: INDIA<br>min. qty: 20<br>MRP: 110</p>
              
                <button class="order-button" onclick="addToCart('10 Watt T Bulb - <b>20Pcs', 2200)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/acdc-led-bulb.jpg" alt="Product 4">
                <div class="card-body">
                <h3>AC/DC Inverter</h3>
                <p>Watt: 9<br>Type of bulb: Cool White<br>Made in: INDIA<br>min. qty: 30 <br>Backup Hours: 4 Hours<br>MRP: 290</p>
              
                <button class="order-button" onclick="addToCart('Inverter (ac-dc) Bulb - <b>30Pcs', 8700)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/color-led-tubelight.jpg" alt="Product 5">
                <div class="card-body">
                <h3>Tube Light</h3>
                <p>Watt: 20<br>Type: Cool Multiple Colors<br>Made in: INDIA<br>Min. Qty.:30<br>MRP: 180</p>
             
                <button class="order-button" onclick="addToCart('20 Watt Tube Light Colors - <b>30Pcs', 5400)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-concealed-light-tiranga.jpg" alt="Product 6">
                <div class="card-body">
                <h3>Conceal Light</h3>
                <p>Watt: 7<br>Type: Cool Multiple Colors<br>Made in: INDIA<br>Min. Qty.:30Pcs<br>MRP: 170</p>
               
                <button class="order-button" onclick="addToCart('7 Watt tiranga conceal - <b>30Pcs', 5100)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-bulb.jpg" alt="Product 7">
                <div class="card-body">
                <h3>ph Bulb  </h3>
                <p>Watt: 15<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22<br>MRP: 155</p>
                
                <button class="order-button" onclick="addToCart('15 Watt sh Bulb - <b>30Pcs', 4650)">Add to Cart</button>
                </div>
             </div>
            <div class="card" id="product1">
            <img src="images/led-bulb.jpg" alt="Product 8">
                <div class="card-body">
                <h3>ph Bulb</h3>
                <p>Watt: 18<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22<br>MRP: 180</p>
              
                <button class="order-button" onclick="addToCart('18 Watt sh Bulb - <b>30Pcs', 5400)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/box20w.jpg" alt="Product 9">
                <div class="card-body">
                <h3>Ph Bulb </h3>
                <p>Watt: 20<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22<br>MRP: 190</p>
              
                <button class="order-button" onclick="addToCart('20 Watt sh Bulb - <b>20Pcs', 3800)">Add to Cart</button>
                </div>
             </div>
            <div class="card" id="product1">
            <img src="images/high-watt-40.jpg" alt="Product 7">
                <div class="card-body">
                <h3>40 Watt High</h3>
                <p>Watt: 40<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22<br>MRP: 310</p>
             
                <button class="order-button" onclick="addToCart('40 Watt High Bulb - <b>10Pcs', 3100)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/high-watt-55.jpg" alt="Product 7">
                <div class="card-body">
                <h3>55 Watt High</h3>
                <p>Watt: 55<br>Type of bulb: Cool White<br>Made in: INDIA<br>Type: b22<br>MRP: 340</p>
              
                <button class="order-button" onclick="addToCart('55 Watt High Bulb - <b>10Pcs', 3400 )">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-cob-light.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Movable Cob</h3>
                <p>Watt: 5<br>Type of bulb: Cool White,WW<br>Made in: INDIA<br>MRP: 290</p>
              
                <button class="order-button" onclick="addToCart('5 Watt Movable COB - <b>20Pcs', 5800)">Add to Cart</button>
                </div>
             </div>
            <div class="card" id="product1">
            <img src="images/led-panel-light.jpg" alt="Product 7">
                <div class="card-body">
                <h3>8 Watt 4/4</h3>
                <p>Watt: 8<br>Type: Cool White,WW<br>Made in: INDIA<br><br>MRP: 180</p>
                <button class="order-button" onclick="addToCart('8 Watt Panel Light - <b>30Pcs', 5400)">Add to Cart</button>
                </div>
                
            </div>
            <div class="card" id="product1">
            <img src="images/led-surface-panel-light.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Surface Panel</h3>
                <p>Watt: 8<br>Type: Cool White,WW<br>Made in: INDIA<br><br>MRP: 185</p>
                <button class="order-button" onclick="addToCart('8 Watt Surface Paanel - <b>30Pcs', 5550)">Add to Cart</button>
                </div>
               
            </div>
            <div class="card" id="product1">
            <img src="images/led-concealed-light-packaging-box.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Deep Light</h3>
                <p>Watt: 3<br>Type: Cool White,WW<br>Made in: INDIA<br><br>MRP: 85</p>
                <button class="order-button" onclick="addToCart('3 Watt Lenc COB W+WW - <b>30Pcs', 2550)">Add to Cart</button>
                </div>
                
            </div>
            <div class="card" id="product1">
            <img src="images/led-concealed-light-packaging-box.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Deep Light ORI</h3>
                <p>Watt: 3<br>Type: Multiple Colors<br>Made in: INDIA<br><br>MRP: 85</p>
                
                <button class="order-button" onclick="addToCart('3 Watt ORI Deep Light Colors - <b>30Pcs', 2550)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-dob-bulb.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Beta Dob</h3>
                <p>Watt: 9<br>warranty: Non<br>Min. Qty: 100<br>Type: b22<br>MRP: 18/-</p>
                
                <button class="order-button" onclick="addToCart('Dob Bulb Non warranty - <b>100Pcs', 1800)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/syskaled.webp" alt="Product 7">
                <div class="card-body">
                <h3>sh Bulb </h3>
                <p>Watt: 9<br>Type of bulb: Cool White<br>Made in: INDIA<br>Min. Qty.: 100Pcs<br>MRP: 55</p>
                <button class="order-button" onclick="addToCart('9 Watt sh 7222 Bulb - <b>100Pcs', 5500)">Add to Cart</button>
            </div>
                
            </div>
            <div class="card" id="product1">
            <img src="images/all-rounder-bulb.jpg" alt="Product 7">
                <div class="card-body">
                <h3>All Rounder</h3>
                <p>Watt: 15, 12, 0.5 W<br>Type of bulb: Cool White<br>Made in: INDIA<br>Min. Qty.: 30Pcs<br>Price: 150</p>
               
                <button class="order-button" onclick="addToCart('3 in 1 All Rounder Bulb - <b>30Pcs', 4500)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-street-light20.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Street Light</h3>
                <p>Watt: 20<br>Type: Cool White<br>Made in: INDIA<br>Min. Qty: 10Pcs<br>MRP: 520</p>
                
                <button class="order-button" onclick="addToCart('20 watt Street Light - <b>10Pcs', 5200)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-street-light30.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Street Light</h3>
                <p>Watt: 30<br>Type: Cool White<br>Made in: INDIA<br>Min. Qty: 10Pcs<br>MRP: 650</p>
                
                <button class="order-button" onclick="addToCart('30 Watt Street Light - <b>10Pcs', 6500)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-street-light50.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Street Light </h3>
                <p>Watt: 50<br>Type: Cool White<br>Made in: INDIA<br>Min. Qty: 10Pcs<br>MRP: 1100</p>
              
                <button class="order-button" onclick="addToCart('50 Watt Street Light - <b>10Pcs', 11000)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-street-light100.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Street Light</h3>
                <p>Watt: 100<br>Type: Cool White<br>Made in: INDIA<br>Min. Qty: 10Pcs<br>MRP: 1600</p>
               
                <button class="order-button" onclick="addToCart('100 Watt Street Light - <b>10Pcs', 16000)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-flood-light30.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Flood Light </h3>
                <p>Watt: 30<br>Type: Cool White<br>Made in: INDIA<br>Min. Qty: 10Pcs<br>MRP: 450</p>
               
                <button class="order-button" onclick="addToCart('30 Watt Flood Light CY Series - <b>10Pcs', 4500)">Add to Cart</button>
                </div>
             </div>
            <div class="card" id="product1">
            <img src="images/led-flood-light50.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Flood Light </h3>
                <p>Watt: 50<br>Type: Cool White<br>Made in: INDIA<br>Min. Qty: 10Pcs<br>MRP: 670</p>
              
                <button class="order-button" onclick="addToCart('50 Watt Flood Light CY Series - <b>10Pcs', 6700)">Add to Cart</button>
                </div>
            </div>
            <div class="card" id="product1">
            <img src="images/led-flood-light100.jpg" alt="Product 7">
                <div class="card-body">
                <h3>Flood Light</h3>
                <p>Watt: 50<br>Type: Cool White<br>Made in: INDIA<br>Min. Qty: 10Pcs<br>MRP: 1400</p>
               
                <button class="order-button" onclick="addToCart('100 Watt Flood Light CY Series - <b>10Pcs', 14000)">Add to Cart</button>
                </div>
             </div>
            <!-- Add more product cards similarly -->

        </section>

        <aside class="cart">
            <h2>Your Cart</h2>
            <button type="button" class="clear-all-button" onclick="clearAll()">Clear All</button>
            <ul id="cart-items"></ul>
            <div id="totalAmount"> <hr>
                Total Amount: ₹ <span id="total"> 0</span>
            </div>
            <form id="checkout-form" action="#" method="post" onsubmit="return validateAndCheckout()">
                <!-- Hidden inputs for cart data -->
                <input type="hidden" name="itemName" id="itemName">
                <input type="hidden" name="price" id="price">
                <div class="form-group">
                    <hr>
                    <label for="Name">Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your Full Name">
                </div>
                <div class="form-group">
                    <label for="mobileNumber">Mobile Number:</label>
                    <input type="tel" id="mobileNumber" name="mobileNumber" pattern="[0-9]{10}" required placeholder="Enter your 10 digit Mobile Number">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" rows="4" required placeholder="Enter your Address"></textarea>
                </div>
                <div class="form-group">
                    <label for="paymentMethod">Payment Method:</label>
                    <select id="payment" name="payment" required>
                        <option value="">Select payment method</option>
                        <option value="credit">Credit Card</option>
                        <option value="debit">Debit Card</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                <input type="hidden" name="orderDate" id="orderDate">
                <button type="submit" class="checkout-button">Checkout</button>
            </form>
        </aside>
    </div>

    <script>
        // Function to add item to cart
        function addToCart(itemName, price) {
            // Create list item for cart
            const li = document.createElement('li');
            li.innerHTML = `${itemName} - ₹${price}`;
            document.getElementById('cart-items').appendChild(li);

            // Update total price
            const totalElement = document.getElementById('total');
            const currentTotal = parseInt(totalElement.innerText);
            totalElement.innerText = currentTotal + price;

            // Set hidden input values for form submission
            document.getElementById('itemName').value = itemName;
            document.getElementById('price').value = price;
        }

        // Function to clear all items from cart
        function clearAll() {
            const cartItems = document.getElementById('cart-items');
            while (cartItems.firstChild) {
                cartItems.removeChild(cartItems.firstChild);
            }
            document.getElementById('total').innerText = '0';
        }

        // Function to validate form and submit checkout
        function validateAndCheckout() {
            const name = document.getElementById('name').value.trim();
            const mobileNumber = document.getElementById('mobileNumber').value.trim();
            const address = document.getElementById('address').value.trim();

            // Simple validation
            if (name === '' || mobileNumber === '' || address === '') {
                alert('Please fill in all fields.');
                return false;
            }

            // Validation for mobile number
            const mobileNumberPattern = /^[0-9]{10}$/;
            if (!mobileNumberPattern.test(mobileNumber)) {
                alert('Please enter a valid 10 digit mobile number.');
                return false;
            }

            // Form submission logic can go here
            // For demo purposes, we will just log the data
            console.log('Name:', name);
            console.log('Mobile Number:', mobileNumber);
            console.log('Address:', address);

            // You can submit the form using AJAX or fetch API here if needed

            // Reset form after successful submission
            document.getElementById('checkout-form').reset();
            clearAll(); // Clear cart after checkout

            alert('Order placed successfully!');
            return false; // Prevent default form submission
        }
    </script>
</body>

</html>
