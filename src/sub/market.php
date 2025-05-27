<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../db.php');
include('../update_user.php');

updateUser($pdo);

if(!$_SESSION['logged_in']){
    header('Location: ../error.php');
    exit();
}
extract($_SESSION['userData']);

if(!in_array('694979041270300733', $roles)) {
    header('Location: ../dashboard.php');
    exit();
}

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

setcookie("lastPage", "market");

if (isset($_COOKIE['lastPage'])) {
    if ($_COOKIE['lastPage'] != "getInventory") {
        header('Location: getInventory.php');
    }
} else {
    header('Location: market.php');
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
        <link href="../../dist/market.css" rel="stylesheet">
        <script type="module" src="market.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='marketScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="marketContent">
                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="marketBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="marketLogout"></a>
                    </div>
                </div>

                <div class="menu" id="marketMenu">
                    
                    <div id="invMenuButtons">
                        <input type="button" value="/items" id="itemsButton" class="invMenuButton">
                        <input type="button" value="/stocks" id="stocksButton" class="invMenuButton">
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
                                    if ($itemEvil == 0) {
                                        if ($inventoryType == 0) {
                                            if ($itemQuantity == 0) {
                                                echo "";
                                            } else if ($itemQuantity == -1) {
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
                    
                    <div class="submenu" id="stocksMenu">
                        <div id="ownedStocks">
                            <?php 
                                $stockRows = $_SESSION["market_stocks"];
                                foreach ($stockRows as $currentRow) {
                                    
                                    $stockTicker = $currentRow['ticker'];
                                    $stockName = $currentRow['name'];
                                    $stockID = $currentRow['id'];
                                    $stockCategory = $currentRow['category'];
                                    $stockValue = $currentRow['current_value'];
                                    $stockData = json_encode($currentRow, JSON_UNESCAPED_UNICODE);
                                    $safeStockData = htmlspecialchars($stockData, ENT_QUOTES, 'UTF-8');

                                    $stockHistoryDecoded = getStockHistory($pdo, $stockID);
                                    $stockHistory = json_encode($stockHistoryDecoded, JSON_UNESCAPED_UNICODE);
                                        echo "
                                        <div class='stockRow' id='stock$stockID' data-json='$safeStockData'>
                                            <img class='stockPNG' src='../../assets/stocks/$stockID.png' alt='$stockName'>
                                            <div class='stockInfoRow'>
                                                <h2 class='stockTicker'>($stockTicker)</h2>
                                                <h2 class='stockName'>$stockName</h2>
                                            </div>
                                            <div class='stockValueRow'>
                                                <h2 class='stockValue' data-json='$stockHistory'>Value: $$stockValue</h2>
                                            </div>
                                            <div class='stockInfoRow'>
                                                <h2 class='stockCategory'>$stockCategory</h2>
                                            </div>
                                        </div>
                                        ";
                                    
                                }
                            ?>
                        </div>
                        <div id="stockInfo">
                            <h2 id="noStockSelectedNotice">select a stock to view its market history.</h2>
                            <div id="stockData">
                                <div class="stockDataRow">
                                    <h2 class="stockDataInfo" id="stockDataTicker">(NULL)</h2>
                                    <h2 class="stockDataInfo" id="stockDataName">Null</h2>
                                    <h2 class="stockDataInfo" id="stockDataValue">$0</h2>
                                    <h2 class="stockDataInfo" id="stockDataCategory">Null</h2>
                                </div>
                                <div id="chartContainer">
                                    <canvas id="stockChart"></canvas>
                                </div>
                                <div class="stockDataRadioRow">
                                    <div id="stockButtons">
                                        <input type="text" placeholder="#" value="1" id="stockQuanToBuy">
                                        <input type="button" value="/purchase" id="stockPurchaseButton">
                                    </div>
                                    <label class="stockDataRadio">
                                        <input class="timelineInput" type="radio" name="timeRange" value="day"> /day
                                    </label>
                                    <label class="stockDataRadio">
                                        <input class="timelineInput" type="radio" name="timeRange" value="week"> /week
                                    </label>
                                    <label class="stockDataRadio">
                                        <input class="timelineInput" type="radio" name="timeRange" value="month"> /month
                                    </label>
                                    <label class="stockDataRadio">
                                        <input class="timelineInput" type="radio" name="timeRange" value="year"> /year
                                    </label>
                                    <label class="stockDataRadio">
                                        <input class="timelineInput" type="radio" name="timeRange" value="all" checked> /all time
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="marketReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>