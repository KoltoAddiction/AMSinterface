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

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

setcookie("lastPage", "inventory");

if (isset($_COOKIE['lastPage'])) {
    if ($_COOKIE['lastPage'] != "getInventory") {
        header('Location: getInventory.php');
    }
} else {
    header('Location: inventory.php');
}

$equippedItems = getEquippedItems($pdo, $db_id);
$userStats = calculateStats($_SESSION['user_inventory'], $equippedItems);

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../../dist/style.css" rel="stylesheet">
        <link href="../../dist/inventory.css" rel="stylesheet">
        <script type="module" src="inventory.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='inventoryScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="inventoryContent">
                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="inventoryBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="inventoryLogout"></a>
                    </div>
                </div>

                <div class="menu" id="invMenu">
                    <div id="invMenuButtons">
                        <input type="button" value="/items" id="itemsButton" class="invMenuButton">
                        <input type="button" value="/gear" id="gearButton" class="invMenuButton">
                        <input type="button" value="/stocks" id="stocksButton" class="invMenuButton">
                    </div>
                    <div class="submenu" id="itemMenu">
                        <div id="itemInventory">
                            <?php
                                $invRows = $_SESSION["user_inventory"];
                                foreach ($invRows as $currentRow) {
                                    
                                    $itemName = $currentRow['name'];
                                    $itemID = $currentRow['item_id'];
                                    $itemQuantity = $currentRow['quantity'];
                                    $inventoryType = $currentRow['inventoryType'];
                                    $itemData = json_encode($currentRow, JSON_UNESCAPED_UNICODE);
                                    $safeItemData = htmlspecialchars($itemData, ENT_QUOTES, 'UTF-8');
                                    if ($inventoryType == 0) {
                                        if ($itemQuantity < 1) {
                                            echo "";
                                        } else if ($itemQuantity == 1) {
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
                        </div>
                    </div>
                    <div class="submenu" id="gearMenu">
                        <div id="equippedGear">
                            <img id="gearPortrait" src="../../assets/ascii/gearPortraitMarkup.png">
                            <?php
                                $gearSlots = [
                                    "accessory" => "accessoryIcon",
                                    "weapon" => "weaponIcon",
                                    "headwear" => "headwearIcon",
                                    "catalyst" => "catalystIcon",
                                    "bodywear" => "bodywearIcon",
                                    "signature" => "signatureIcon"
                                ];

                                foreach ($gearSlots as $slot => $iconId) {
                                    $equippedItem = $equippedItems[$slot];
                                    echo "<div class='gearIcon' id='$iconId'>";
                                    if ($equippedItem > 0) {
                                        echo "<img class='itemPNG' src='../../assets/items/$equippedItem.png'>";
                                    }
                                    echo "</div>";
                                }
                            ?>
                            <div class="gearStatsTable" id="userStatsTable">
                                    <div>
                                        <span class="gstHeader">stat</span>
                                        <span>damage</span>
                                        <span>armor</span>
                                        <span>resistance</span>
                                    </div>
                                    <div>
                                        <span class="gstHeader">total</span>
                                        <span id="dmgTotal"><?php 
                                            $totalDamage = ($userStats[0] * (1 + ($userStats[1] / 100)));
                                            echo $totalDamage;
                                        ?></span>
                                        <span id="armTotal"><?php 
                                            $totalArmor = ($userStats[2] * (1 + ($userStats[3] / 100)));
                                            echo $totalArmor;
                                        ?></span>
                                        <span id="resTotal"><?php 
                                            $totalResistance = ($userStats[4] * (1 + ($userStats[5] / 100)));
                                            echo $totalResistance;
                                        ?></span>
                                    </div>
                                </div>
                        </div>
                        <div id="selectedSlot">
                            <h2 id="noSlotSelectedNotice">select a gear slot to view equippable gear.</h2>
                            <div id="gearList">
                                <?php
                                // Define gear slots with corresponding IDs
                                $gearSlots = [
                                    0 => 'accessoryIconList',
                                    1 => 'weaponIconList',
                                    2 => 'headwearIconList',
                                    3 => 'catalystIconList',
                                    4 => 'bodywearIconList',
                                    5 => 'signatureIconList',
                                ];

                                // Get inventory rows
                                $invRows = $_SESSION["user_inventory"];

                                // Loop through gear slots
                                foreach ($gearSlots as $gearType => $iconListId) {
                                    echo "<div id='$iconListId' class='gearTypeList'>";
                                    
                                    foreach ($invRows as $currentRow) {
                                        $itemName = $currentRow['name'];
                                        $itemID = $currentRow['item_id'];
                                        $itemQuantity = $currentRow['quantity'];
                                        $inventoryType = $currentRow['inventoryType'];
                                        $itemGearSlot = $currentRow['gearType'];
                                        $itemGearSet = $currentRow['gearSetName'];
                                        $itemData = json_encode($currentRow, JSON_UNESCAPED_UNICODE);
                                        $safeItemData = htmlspecialchars($itemData, ENT_QUOTES, 'UTF-8');

                                        // Check conditions for rendering items
                                        if ($inventoryType == 1 && $itemGearSlot == $gearType && $itemQuantity > 0) {
                                            echo "
                                                <div class='gearRow' id='item$itemID' data-json='$safeItemData'>
                                                    <img class='gearPNG' src='../../assets/items/$itemID.png' alt='$itemName'>
                                                    <div class='gearInfoList'>
                                                        <h2 class='gearName'>$itemName</h2>
                                                        <h2 class='gearSet'>$itemGearSet Set</h2>
                                                    </div>
                                                </div>
                                            ";
                                        }
                                    }
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                        <div id="selectedGear">
                            <h2 id="noGearSelectedNotice">select gear to view its information.</h2>
                            <div id="selectedGearInfo">
                                <div id="imgQuantityGear">
                                    <img class = "gearInfo" id="gearImage" src='../../assets/items/0.png' alt='Item Image'>
                                    <h2 class = "gearInfo" id="gearAmountOwned"></h2>
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
                                <h2 class="gearInfo" id="statsTitle">Gear Stats:</h2>
                                <div class="gearStatsTable">
                                    <div>
                                        <span class="gstHeader">stat</span>
                                        <span>damage</span>
                                        <span>armor</span>
                                        <span>resistance</span>
                                    </div>
                                    <div>
                                        <span class="gstHeader"> + </span>
                                        <span id="dmgFlat">0</span>
                                        <span id="armFlat">0</span>
                                        <span id="resFlat">0</span>
                                    </div>
                                    <div>
                                        <span class="gstHeader"> % </span>
                                        <span id="dmgPer">0</span>
                                        <span id="armPer">0</span>
                                        <span id="resPer">0</span>
                                    </div>
                                </div>
                                <div id="setInfo">
                                    <h2 class="gearInfo" id="gearSetName">Unknown Set</h2>
                                    <h2 class="gearInfo" id="requiredPieces">Pieces Required: Unknown</h2>
                                    <h2 class="gearInfo">Set Bonus:</h2>
                                    <div class="gearStatsTable" id="setStatsTable">
                                        <div>
                                            <span class="gstHeader">stat</span>
                                            <span>damage</span>
                                            <span>armor</span>
                                            <span>resistance</span>
                                        </div>
                                        <div>
                                            <span class="gstHeader"> + </span>
                                            <span id="dmgFlatSet">0</span>
                                            <span id="armFlatSet">0</span>
                                            <span id="resFlatSet">0</span>
                                        </div>
                                        <div>
                                            <span class="gstHeader"> % </span>
                                            <span id="dmgPerSet">0</span>
                                            <span id="armPerSet">0</span>
                                            <span id="resPerSet">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="selectedGearButtons">
                                    <input type="button" id="equipButton" value="> equip">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="submenu" id="stocksMenu">
                        <div id="ownedStocks">
                            <?php 
                                $stockRows = $_SESSION["user_stocks"];
                                foreach ($stockRows as $currentRow) {
                                    
                                    $stockTicker = $currentRow['ticker'];
                                    $stockName = $currentRow['name'];
                                    $stockID = $currentRow['stock_id'];
                                    $stockCategory = $currentRow['category'];
                                    $stockQuantity = $currentRow['quantity'];
                                    $stockValue = $currentRow['current_value'];
                                    $stockData = json_encode($currentRow, JSON_UNESCAPED_UNICODE);
                                    $safeStockData = htmlspecialchars($stockData, ENT_QUOTES, 'UTF-8');

                                    $stockHistoryDecoded = getStockHistory($pdo, $stockID);
                                    $stockHistory = json_encode($stockHistoryDecoded, JSON_UNESCAPED_UNICODE);


                                    if ($stockQuantity < 1) {
                                        echo "";
                                    } else {
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
                                                <h2 class='stockQuantity'>Owned: $stockQuantity</h2>
                                            </div>
                                        </div>
                                        ";
                                    }
                                    
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
                <div class="reauth" id="inventoryReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>