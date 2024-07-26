<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://kit.fontawesome.com/74e6741759.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        nav {
            min-height: 2vh;
            display: flex;
            background-color: #177E89;
            align-items: center;
            justify-content: space-between;
            padding: 5px 7%;
            top: 0;
            width: 100%;
        }

        .logo {
            width: 140px;
            cursor: pointer;
        }

        .nav-links li {
            list-style: none;
            display: inline-block;
            margin: 10px 30px;
        }

        .nav-links li a {
            text-decoration: none;
            color: #fff;
        }

        .register-btn {
            background: #fff;
            color: black;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
<nav>
    <a href="index.php"><img src="images/logo_tdu.png" class="logo" alt=""></a>
    <ul class="nav-links">
        <li><a href="index.php">Popular places</a></li>
        <li><a href="#">Travel Outside</a></li>
        <li><a href="#">Online Packages</a></li>
    </ul>
    <div style="position: relative;" id="right-side">
        <a href="#" class="register-btn">Register Now</a>
        <button class="btn " type="button" data-bs-toggle="offcanvas" data-bs-target="#demo"><i class="fa-solid fa-cart-shopping" style="color: #ffff;"></i></button>
        <p id="cart_counter" style="position: absolute;top: 0;right: 0;background-color: red;color: white;width: 21px;margin: 0;text-align: center;border-radius: 50px;font-size: 12px;">0</p>
    </div>
</nav>
<div class="header1">
    <div class="container">
        <div class="offcanvas offcanvas-end" id="demo">
            <div class="offcanvas-header">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <div class="maincont">
                    <div class="content" >
                          <h3 style="font-size: 24px; font-weight: bold; margin-bottom: 20px;">Your Cart</h3>
                        <!-- Cart items will be loaded here via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCounter();
        updateCartItems();
    });

    function getCartItems() {
        let cartItems = sessionStorage.getItem('selectedExtras');
        return cartItems ? JSON.parse(cartItems) : [];
    }

    function updateCartCounter() {
        let cartItems = getCartItems();
        document.getElementById('cart_counter').textContent = cartItems.length;
    }

    function updateCartItems() {
        let cartItems = getCartItems();
        let cartItemsContainer = document.querySelector('.offcanvas-body .maincont .content');
        cartItemsContainer.innerHTML = '';

        let totalAmount = cartItems.reduce((sum, item) => sum + Number(item.Amount), 0);

        let cartContent = document.createElement('div');

        cartContent.innerHTML = `<h3 style="font-size: 24px; font-weight: bold; margin-bottom: 20px;">Your Cart</h3>`;
        
        cartItems.forEach(function(item) {
            let cartItem = document.createElement('div');
            cartItem.className = 'd-flex gap-3';
            cartItem.style = 'align-items: center; background-color: #64A6BD; padding: 8px; border-radius: 10px; margin-bottom: 25px;';
            
            let imgDiv = document.createElement('div');
            let img = document.createElement('img');
            img.style = 'width: 100px; height: 100px;';
            img.src = item.imgUrl;
            img.alt = '';
            imgDiv.appendChild(img);

            let textDiv = document.createElement('div');
            let productName = document.createElement('p');
            productName.style = 'font-size: small; font-weight: bold;';
            productName.textContent = item.ProductName;

            let productDescription = document.createElement('p');
            productDescription.style = 'font-size: small;';
            productDescription.textContent = item.productdescription;

            let amount = document.createElement('p');
            amount.style = 'font-size: small;';
            amount.textContent = `Amount : $AUD ${item.Amount}`;

            textDiv.appendChild(productName);
            textDiv.appendChild(productDescription);
            textDiv.appendChild(amount);

            cartItem.appendChild(imgDiv);
            cartItem.appendChild(textDiv);

            cartContent.appendChild(cartItem);
        });

        if (cartItems.length != 0) {
            let totalDiv = document.createElement('div');
            totalDiv.className = 'd-flex justify-content-between align-items-baseline';

            let totalAmountP = document.createElement('p');
            totalAmountP.className = 'm-0 p-2 btn text-light';
            totalAmountP.style = 'background-color:#1be414;';
            totalAmountP.textContent = `$ AUD ${totalAmount}`;

            let continueLink = document.createElement('a');
            continueLink.className = 'btn btn-primary';
            continueLink.href = `bookings.php?productCode=${cartItems[0].ProductCode}`;
            continueLink.textContent = 'Continue';

            totalDiv.appendChild(totalAmountP);
            totalDiv.appendChild(continueLink);

            cartContent.appendChild(totalDiv);
        } else {
            let emptyMessage = document.createElement('p');
            emptyMessage.className = 'text-center';
            emptyMessage.textContent = 'Your cart is empty!';
            cartContent.appendChild(emptyMessage);
        }

        cartItemsContainer.appendChild(cartContent);
    }
</script>
</body>
</html>
