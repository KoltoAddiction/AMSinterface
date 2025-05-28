<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../db.php');
include('../update_user.php');

if(!$_SESSION['logged_in']){
    header('Location: ../error.php');
    exit();
}

updateUser($pdo);
extract($_SESSION['userData']);

if(!in_array('732204665319718972', $roles)) {
    header('Location: ../dashboard.php');
    exit();
}

$day = date('w');
if(!($day == 1)) {
    header('Location: ../dashboard.php');
    exit();
}

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

setcookie("lastPage", "blackmarket");

if (isset($_COOKIE['lastPage'])) {
    if ($_COOKIE['lastPage'] != "getInventory") {
        header('Location: getInventory.php');
    }
} else {
    header('Location: blackmarket.php');
}

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../../dist/style.css" rel="stylesheet">
        <link href="../../dist/blackmarket.css" rel="stylesheet">
        <script type="module" src="blackmarket.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='blackmarketScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="blackmarketContent">
                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="blackmarketBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="blackmarketLogout"></a>
                    </div>
                </div>

                <div class="menu" id="blackmarketMenu">

                    <div id="invMenuButtons">
                        <input type="button" value="/items" id="itemsButton" class="invMenuButton">
                        <input type="button" value="/gear" id="gearButton" class="invMenuButton">
                    </div>
                    
                    <div class="submenu" id="itemMenu">
                        <div id="itemInventory">
                            <?php
                                $invRows = $_SESSION["market_items"];
                                foreach ($invRows as $currentRow) {
                                    
                                    $itemName = $currentRow['name'];
                                    $itemID = $currentRow['id'];
                                    $itemQuantity = $currentRow['shop_quantity'];
                                    $inventoryType = $currentRow['inventoryType'];
                                    $itemEvil = $currentRow['evil'];
                                    $itemData = json_encode($currentRow, JSON_UNESCAPED_UNICODE);
                                    $safeItemData = htmlspecialchars($itemData, ENT_QUOTES, 'UTF-8');
                                    if ($itemEvil == 1) {
                                        if ($inventoryType == 0) {
                                            if ($itemQuantity == 0) {
                                                echo "";
                                            }  else if ($itemQuantity == -1) {
                                                echo "
                                                <div class='itemIcon' id='item$itemID' data-json='$safeItemData'>
                                                    <img class='itemPNG' src='../../assets/items/$itemID.png' alt='$itemName'>
                                                </div>
                                                ";
                                            } else {
                                                echo "
                                                <div class='itemIcon' id='item$itemID' data-json='$safeItemData'>
                                                    <img class='itemPNG'  src='../../assets/items/$itemID.png' alt='$itemName'>
                                                    <h2 class='itemQuantity'>$itemQuantity</h2>
                                                </div>
                                                ";
                                            }
                                        }
                                    }
                                }

                            ?>
                        </div>
                        <div id="noSelItemInfo">
                            <h2 id="noItemSelectedNotice">select an item to view its information.</h2>
                        </div>
                        <div id="selItemInfo">
                            <div id="imgQuantity">
                                <img class = "itemInfo" id="itemImage" src='../../assets/items/0.png' alt='Item Image'>
                                <h2 class = "itemInfo" id="amountOwned"></h2>
                            </div>
                            <div id="collectName">
                                <h2 class = "itemInfo" id="itemCollection">Unknown Collection</h2>
                                <h2 class = "itemInfo" id="itemName">Unknown Name</h2>
                            </div>
                            <div id="otherInfo">
                                <h2 class = "itemInfo" id="itemRarity">Unknown Rarity</h2>
                                <h2 class = "itemInfo" id="itemValue">Approx. Value: Unknown</h2>
                                <h2 class = "itemInfo" id="itemDescription">Unknown Description</h3>
                            </div>
                            <div id="buttons">
                                <input type="text" placeholder="#" value="1" id="quanToBuy">
                                <input type="button" value="/purchase" id="purchaseButton">
                            </div>
                        </div>
                    </div>

                    <div class="submenu" id="gearMenu">
                        <div id="gearInventory">
                            <?php
                                $gearRows = $_SESSION["market_items"];
                                foreach ($gearRows as $currentRow) {
                                    
                                    $gearName = $currentRow['name'];
                                    $gearID = $currentRow['id'];
                                    $gearQuantity = $currentRow['shop_quantity'];
                                    $inventoryType = $currentRow['inventoryType'];
                                    $gearEvil = $currentRow['evil'];
                                    $gearData = json_encode($currentRow, JSON_UNESCAPED_UNICODE);
                                    $safeItemData = htmlspecialchars($gearData, ENT_QUOTES, 'UTF-8');
                                    if ($gearEvil == 1) {
                                        if ($inventoryType == 1) {
                                            if ($gearQuantity == 0) {
                                                echo "";
                                            } else if ($gearQuantity == -1) {
                                                echo "
                                                <div class='gearIcon' id='gear$gearID' data-json='$safeItemData'>
                                                    <img class='gearPNG' src='../../assets/items/$gearID.png' alt='$gearName'>
                                                </div>
                                                ";
                                            } else {
                                                echo "
                                                <div class='gearIcon' id='gear$gearID' data-json='$safeItemData'>
                                                    <img class='gearPNG'  src='../../assets/items/$gearID.png' alt='$gearName'>
                                                    <h2 class='gearQuantity'>$gearQuantity</h2>
                                                </div>
                                                ";
                                            }
                                        }
                                    }
                                }

                            ?>
                        </div>
                        <div id="noSelGearInfo">
                            <h2 id="noGearSelectedNotice">select an item to view its information.</h2>
                        </div>
                        <div id="selGearInfo">
                            <div id="imgQuantityGear">
                                <img class = "gearInfo" id="gearImage" src='../../assets/items/0.png' alt='Item Image'>
                                <h2 class = "gearInfo" id="amountOwned"></h2>
                            </div>
                            <div id="collectNameGear">
                                <h2 class = "gearInfo" id="gearCollection">Unknown Collection</h2>
                                <h2 class = "gearInfo" id="gearName">Unknown Name</h2>
                            </div>
                            <div id="otherInfoGear">
                                <h2 class = "gearInfo" id="gearRarity">Unknown Rarity</h2>
                                <h2 class = "gearInfo" id="gearValue">Approx. Value: Unknown</h2>
                                <h2 class = "gearInfo" id="gearDescription">Unknown Description</h3>
                            </div>
                            <div id="gearButtons">
                                <input type="text" placeholder="#" value="1" id="gearQuanToBuy">
                                <input type="button" value="/purchase" id="gearPurchaseButton">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="blackmarketReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>