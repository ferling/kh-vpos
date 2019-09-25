<?php 

require("../src/KHPaymentGW/KHPaymentGateway.php");
require("../src/KHPaymentGW/CommunicationException.php");

$gw = new KHPaymentGW\KHPaymentGateway(1, "HUF", "HU", file_get_contents("../private.pem"));

var_dump($gw->getResults(1234567894));