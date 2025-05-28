<?php
include('../db.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!$_SESSION['logged_in']){
    header('Location: ../error.php');
    exit();
}

extract($_SESSION['userData']);

getInventory($pdo, $dbID);
getStockInventory($pdo, $dbID);
getEquippedItems($pdo, $dbID);

setcookie("lastPage", "getInventory");

if (isset($_COOKIE['lastPage'])) {
    if ($_COOKIE['lastPage'] == "bounties") {
        header('Location: bounties.php');
    } else if ($_COOKIE['lastPage'] == "market") {
        getMarket($pdo, $dbID);
        header('Location: market.php');
    } else if ($_COOKIE['lastPage'] == "blackmarket") {
        getMarket($pdo, $dbID);
        header('Location: blackmarket.php');
    } else {
        header('Location: inventory.php');
    }
} else {
    header('Location: inventory.php');
}


