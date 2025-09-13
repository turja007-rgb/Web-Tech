<?php
use App\Customer\Controllers\CustomerController;
use App\Customer\Controllers\CartController;
use App\Customer\Controllers\CheckoutController;
use App\Customer\Controllers\OrderController;


route('GET', '/', function() {
    (new CustomerController())->home();
});
//Show Login Form -> app/views/login.php
route('GET', '/login', function() {
    (new CustomerController())->showLogin();
});
//Check User Credentials To Give Access Handle Login Form app/views/login.php
route('POST', '/login', function() {
    (new CustomerController())->login();
});
/* Login Actions End Here */


//Show Register From -> app/views/register.php
route('GET', '/register', function() {
    (new CustomerController())->showRegister();
});
//Submit Register Form Data -> app/views/register.php
route('POST', '/register', function() {
    (new CustomerController())->register();
});
/* Register Actions End Here */


//When Request To Logout
route('POST', '/logout', function() {
    (new CustomerController())->logout();
});
route('GET', '/logout', function() {
    (new CustomerController())->logout();
});
/* Logout Actions End Here */


//Show Profile on app/views/profile.php
route('GET', '/profile', function() {
    (new CustomerController())->profile();
});
//UpdateProfile-> Name Email Phone Pass-> app/views/profile.php
route('POST', '/profile_update', function() {
    (new CustomerController())->profileUpdate();
});

//Show Order History on app/views/profile.php
route('GET', '/orders', function() {
    (new CustomerController())->orderHistory();
});
//When Customer Cilick (View Details)->Button
route('POST', '/order/summary', function() {
    (new OrderController())->orderSummary();
});
/* Profile Actions End Here */


// Get ACTIVE Cart from DB- Display on app/views/cart.php
route('GET', '/cart', function() {
    (new CartController())->view();
});

//When Request To Update Item From app/views/cart.php
route('POST', '/cart/update-quantity', function() {
    (new CartController())->updateQuantity();
});
//When Request To Delete Item From app/views/cart.php
route('POST', '/cart/remove-item', function() {
    (new CartController())->remove();
});

//Execute When Hit Product (Add To Cart)-> app/views/index.php
route('POST', '/cart_add', function() {
    (new CartController())->add();
});
/* Cart Actions End Here*/


//Fetch Deliver Fee When select Delivery on app/views/checkout.php
route('POST', '/cart/delivery-fee', function (){
    (new CartController())->deliveryFee();
});
//Handle (ProccedToCheckout)-> Button app/views/checkout.php
route('GET', '/checkout', function() {
    (new CheckoutController())->checkout();
});
//When Place Order Request Send From Checkout.php Page
route('POST', '/place-order', function() {
    (new OrderController())->place();
});
/*Checkout Actions End Here*/

