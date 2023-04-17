<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn] function htmlHead($page = "Home"): void
{ ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $page; ?></title>
        <link rel="stylesheet" href="./CSS/stylesheet.css">
    </head>

    <body class="bg">


    <?php
}

