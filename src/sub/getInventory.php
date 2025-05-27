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

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];

getInventory($pdo, $db_id);
getStockInventory($pdo, $db_id);
getEquippedItems($pdo, $db_id);

setcookie("lastPage", "getInventory");

if (isset($_COOKIE['lastPage'])) {
    if ($_COOKIE['lastPage'] == "bounties") {
        header('Location: bounties.php');
    } else if ($_COOKIE['lastPage'] == "market") {
        getMarket($pdo, $db_id);
        header('Location: market.php');
    } else if ($_COOKIE['lastPage'] == "blackmarket") {
        getMarket($pdo, $db_id);
        header('Location: blackmarket.php');
    } else {
        header('Location: inventory.php');
    }
} else {
    header('Location: inventory.php');
}


