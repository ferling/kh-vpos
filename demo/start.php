<?php 

require("../src/KHPaymentGW/KHPaymentGateway.php");
require("../src/KHPaymentGW/CommunicationException.php");

$gw = new KHPaymentGW\KHPaymentGateway(1, "HUF", "HU", file_get_contents("../private.pem"));

echo $gw->startPayment(500, 1234567894);