<?php
// IMPORTANT:
// call the function session_start() before
// any output (html) has been sent to the browser
session_start();
// include functions.php
include("inc/sql.php");
include("inc/htmlBasics.php");
// set some session indexes | keys
init();
// handle post request (if any)
handlePost();

htmlHead();
displayNavbar();
?>

<body>

<section class="main">
    <div class="container">
        <div class="box">

            <?php

displayShoppingCart();
?>

</body>
</html>