<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('/var/config.php');

try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//  echo "Connected successfully";
}
catch (PDOException $e) {
    error_log("PDO Error: " . $e->getMessage());
    echo "Database error occurred.";
}

function setUserUpdateTime($pdo, $record_id) {
    $timestamp = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("UPDATE users SET last_update = :ts WHERE id = :id");
    $stmt->execute(['ts' => $timestamp, 'id' => $record_id]);
}

function updateJobItemProgress($pdo, $record_id, $p1, $p2, $p3) {
    $stmt = $pdo->prepare("UPDATE users SET item1_progress = :p1, item2_progress = :p2, item3_progress = :p3 WHERE id = :id");
    $stmt->execute([
    'p1' => $p1,
    'p2' => $p2,
    'p3' => $p3,
    'id' => $record_id
]);
}

function auditInventory($pdo, $record_id, $item_id, $quantity){
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE user_id = :user_id AND item_id = :item_id");
    $stmt->execute(['user_id' => $record_id, 'item_id' => $item_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // No entry exists: insert one with 0 quantity
        $insertStmt = $pdo->prepare("INSERT INTO inventory (user_id, item_id, quantity) VALUES (:user_id, :item_id, 0)");
        $insertStmt->execute(['user_id' => $record_id, 'item_id' => $item_id]);
        $currentQuantity = 0;
    } else {
        $currentQuantity = (int)$row['quantity'];
    }
    if ($quantity < 0) {
        if ($currentQuantity < abs($quantity)) {
            return false; // Not enough to subtract
        }
    }

    $newQuantity = $currentQuantity + $quantity;
    $updateStmt = $pdo->prepare("UPDATE inventory SET quantity = :quantity WHERE user_id = :user_id AND item_id = :item_id");
    $updateStmt->execute([
        'quantity' => $newQuantity,
        'user_id' => $record_id,
        'item_id' => $item_id
    ]);

    return true;
}

function auditStockInventory($pdo, $record_id, $stock_id, $quantity){
    $stmt = $pdo->prepare("SELECT quantity FROM stockinventory WHERE user_id = :user_id and stock_id = :stock_id");
    $stmt->execute(['user_id' => $record_id, 'stock_id' => $stock_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // No entry exists: insert one with 0 quantity
        $insertStmt = $pdo->prepare("INSERT INTO stockinventory (user_id, stock_id, quantity) VALUES (:user_id, :stock_id, 0)");
        $insertStmt->execute(['user_id' => $record_id, 'stock_id' => $stock_id]);
        $currentQuantity = 0;
    } else {
        $currentQuantity = (int)$row['quantity'];
    }
    if ($quantity < 0) {
        if ($currentQuantity < abs($quantity)) {
            return false; // Not enough to subtract
        }
    }

    $newQuantity = $currentQuantity + $quantity;
    $updateStmt = $pdo->prepare("UPDATE stockinventory SET quantity = :quantity WHERE user_id = :user_id AND stock_id = :stock_id");
    $updateStmt->execute([
        'quantity' => $newQuantity,
        'user_id' => $record_id,
        'stock_id' => $stock_id
    ]);

    return true;
}

function auditAccount($pdo, $record_id, $account, $amount) {
    if (!in_array($account, [0, 1, 2, 3], true)) {
        return false;
    }

    $allowed_columns = ['balChecking', 'balSavings', 'balOffshore', 'balHighYield'];
    $column = $allowed_columns[$account];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $record_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    
    $curMoney = (int)$row[$allowed_columns[$account]];

    if ($amount < 0) {
        if ($curMoney < abs($amount)) {
            return false;
        }
    }

    $newMoney = $curMoney + $amount;
    $updateStmt = $pdo->prepare("UPDATE users SET $column = :bal WHERE id = :user_id");
    if($updateStmt->execute(['bal' => $newMoney, 'user_id' => $record_id])) {
        return true;
    } else {
        return false;
    }
    

}

function addUserToDatabase($pdo,$discord_id,$discord_username,$discord_avatar){
    $sql = "INSERT INTO users (discord_id,discord_username,discord_avatar) VALUES (:discord_id,:discord_username,:discord_avatar)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'discord_id'=>$discord_id,
            'discord_username'=>$discord_username,
            'discord_avatar'=>$discord_avatar,
        ]);
        echo 'inserted successfully';
    } catch (Exception $e) {
        echo $e;
    }
}
function updateUserToDatabase($pdo,$user_id,$discord_username,$discord_avatar){
    $sql = "UPDATE users SET discord_username = :discord_username, discord_avatar = :discord_avatar WHERE id = :user_id";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id'=>$user_id,
            'discord_username'=>$discord_username,
            'discord_avatar'=>$discord_avatar,
        ]);
        echo 'updated successfully';
    } catch (Exception $e) {
        echo $e;
    }
}
function addUserEquippedGear($pdo,$record_id){
    $sql = "INSERT INTO userequippedgear (userID) VALUES (:id)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $record_id);
        $stmt->execute();
        
    } catch (Exception $e) {
        echo $e;
    }
}
function addUserHarvests($pdo,$record_id){
    $sql = "INSERT INTO harvestsunlocked (user_id) VALUES (:id)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $record_id);
        $stmt->execute();
    } catch (Exception $e) {
        echo $e;
    }
}
function addUserTheft($pdo,$record_id){
    $sql = "INSERT INTO theft (user_id) VALUES (:id)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $record_id);
        $stmt->execute();
    } catch (Exception $e) {
        echo $e;
    }
}
function addUserHunting($pdo,$record_id){
    $sql = "INSERT INTO hunting (user_id) VALUES (:id)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $record_id);
        $stmt->execute();
    } catch (Exception $e) {
        echo $e;
    }
}

function getUserFromDatabase($pdo,$discord_id){
    $sql = "SELECT * FROM users WHERE discord_id=:discord_id";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'discord_id'=>$discord_id,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;
    } catch (Exception $e) {
        echo $e;
    }
}

function getUserByUID($pdo, $record_id){
    $sql = "SELECT * FROM users WHERE id=:id";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id'=>$record_id,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;
    } catch (Exception $e) {
        echo $e;
    }
}

function getAllUsersFromDatabase($pdo) {
    $sql = "SELECT id,discord_id,discord_username,discord_avatar FROM users";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    } else {
        echo "Error fetching inventory:" . $pdo->error;
    }
}

function getHCOIN($pdo, $record_id) {
    $sql = "SELECT quantity FROM stockinventory WHERE stockinventory.user_id = :id and stockinventory.stock_id = 2";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $record_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } catch (Exception $e) {
        echo $e;
    }
}

function getInventory($pdo, $record_id) {

    $sql = "SELECT * FROM inventory JOIN items ON inventory.item_id = items.id LEFT JOIN gear ON inventory.item_id = gear.itemID LEFT JOIN gearsets on gear.gearSet = gearsets.id WHERE inventory.user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $record_id);
    if ($stmt->execute()) {
        echo "Fetched inventory successfully.";
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION["user_inventory"] = $rows;
        } else {
            $_SESSION["user_inventory"] = [];
            echo "No data found for user.";
        }
    } else {
        echo "Error fetching inventory:" . $pdo->error;
    }
}

function getStockInventory($pdo, $record_id) {

    $sql = "SELECT * FROM stockinventory JOIN stocks ON stockinventory.stock_id = stocks.id WHERE stockinventory.user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $record_id);
    if ($stmt->execute()) {
        echo "Fetched inventory successfully.";
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION["user_stocks"] = $rows;
        } else {
            $_SESSION["user_stocks"] = [];
            echo "No data found for user.";
        }
    } else {
        echo "Error fetching stock inventory:" . $pdo->error;
    }
}

function getJobInfo($pdo, $job_id) {
    $sql = "SELECT * FROM jobs WHERE jobs.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $job_id);
    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } else {
        echo "Error fetching job info:" . $pdo->error;
    }
}

function getItemInfo($pdo, $item_id) {
    $sql = "SELECT * FROM items WHERE items.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $item_id);
    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } else {
        echo "Error fetching item data:" . $pdo->error;
    }
}

function getStockInfo($pdo, $stock_id) {
    $sql = "SELECT * FROM stocks WHERE stocks.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $stock_id);
    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } else {
        echo "Error fetching item data:" . $pdo->error;
    }
}

function getStockHistory($pdo, $stock_id) {

    $sql = "SELECT * FROM stockhistory WHERE stockhistory.stock_id_history = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $stock_id);
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } else {
        }
    } else {
    }
}

function getUserHarvests($pdo, $record_id) {
    $sql = "SELECT * FROM harvestsunlocked WHERE harvestsunlocked.user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $record_id);
    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } else {
        echo "Error fetching user harvests:" . $pdo->error;
    }
}

function getUserTheft($pdo, $record_id){
    $sql = "SELECT * FROM theft WHERE theft.user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $record_id);
    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } else {
        echo "Error fetching user theft:" . $pdo->error;
    }
}

function setUserTheftPayout($pdo, $record_id, $payout) {

    if ($payout == 0) {
        $sql = "UPDATE theft SET cash_reward = 0, active_theft = 0, awaiting_collec = 1, successful = 0 WHERE user_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $record_id);
        $stmt->execute();
    } else {
        $sql = "UPDATE theft SET cash_reward = :pay, active_theft = 0, awaiting_collec = 1, successful = 1 WHERE user_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $record_id);
        $stmt->bindParam(":pay", $payout);
        $stmt->execute();
    }
    
}

function claimTheft($pdo) {
    extract($_SESSION['userData']);

    $userTheft = getUserTheft($pdo, $dbID);
    if ($userTheft['awaiting_collec'] == 1 && $userTheft['successful'] == 1) {
        auditAccount($pdo, $dbID, 2, $userTheft['cash_reward']);
        if ($userTheft['attempted_acc'] == 0) {
            auditAccount($pdo, $userTheft['attempted_uid'], 0, -$userTheft['cash_reward']);
        } else if ($userTheft['attempted_acc'] == 1) {
            auditAccount($pdo, $userTheft['attempted_uid'], 2, -$userTheft['cash_reward']);
        }
    }
        $sql = "UPDATE theft SET cash_reward = 0, active_theft = 0, awaiting_collec = 0, successful = 0, attempted_uid = -1, attempted_acc = -1 WHERE user_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $dbID);
        $stmt->execute();

}

function getUserHunting($pdo, $record_id){
    $sql = "SELECT * FROM hunting WHERE hunting.user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $record_id);
    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } else {
        echo "Error fetching user hunting:" . $pdo->error;
    }
}

function getMarket($pdo, $record_id){
    $sql = "SELECT * FROM items";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['market_items'] = $rows;
    } else {
        echo "Error fetching market items:" . $pdo->error;
    }

    $sql = "SELECT * FROM stocks";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['market_stocks'] = $rows;
    } else {
        echo "Error fetching market stocks:" . $pdo->error;
    }
}

function calculateStats($invArray, $equippedGear) {
    $validSlots = ['accessory', 'weapon', 'headwear', 'catalyst', 'bodywear', 'signature'];
    
    $equippedStats = [];

    foreach ($validSlots as $slot) {
        if (isset($equippedGear[$slot]) && $equippedGear[$slot] > 0) { // Ensure the slot is equipped
            // Search for the item in the inventory
            foreach ($invArray as $item) {
                if ($item['item_id'] == $equippedGear[$slot]) {
                    // Add the item's stats to the result
                    $equippedStats[$slot] = $item;
                    break; // Stop searching once the item is found
                }
            }
        }
    }

    $flatDamage = 0;
    $percentDamage = 0;

    $flatArmor = 0;
    $percentArmor = 0;

    $flatResistance = 0;
    $percentResistance = 0;

    foreach ($equippedStats as $item) {
        if (isset($item['damage'])) {
            $flatDamage += $item['damage'];
        }
        if (isset($item['damagePER'])) {
            $percentDamage += $item['damagePER'];
        }
        if (isset($item['armor'])) {
            $flatArmor += $item['armor'];
        }
        if (isset($item['armorPER'])) {
            $percentArmor += $item['armorPER'];
        }
        if (isset($item['resistance'])) {
            $flatResistance += $item['resistance'];
        }
        if (isset($item['resistancePER'])) {
            $percentResistance += $item['resistancePER'];
        }
    }

    $gearSetCounts = [];
    $setBonusesMet = [];

    // Step 1: Count pieces per gearSet
    foreach ($equippedStats as $item) {
        if (isset($item['gearSet'])) {
            $gearSet = $item['gearSet'];
            if (!isset($gearSetCounts[$gearSet])) {
                $gearSetCounts[$gearSet] = 0;
            }
            $gearSetCounts[$gearSet]++;
        }
    }

    // Step 2: Check if pieces meet or exceed the piecesRequired value
    foreach ($equippedStats as $item) {
        if (isset($item['gearSet'], $item['piecesRequired'])) {
            $gearSet = $item['gearSet'];
            $piecesRequired = $item['piecesRequired'];

            // Check if the count meets the requirement
            if ($gearSetCounts[$gearSet] >= $piecesRequired) {
                if (!isset($setBonusesMet[$gearSet])) {
                    $flatDamage += $item['setBonusDamage'];
                    $percentDamage += $item['setBonusDamagePER'];
                    $flatArmor += $item['setBonusArmor'];
                    $percentArmor += $item['setBonusArmorPER'];
                    $flatResistance += $item['setBonusResistance'];
                    $percentResistance += $item['setBonusResistancePER'];
                } 

                $setBonusesMet[$gearSet] = true;
            } else {
                $setBonusesMet[$gearSet] = false;
            }
        }
    }

    $statsArray = [$flatDamage, $percentDamage, $flatArmor, $percentArmor, $flatResistance, $percentResistance];

    return $statsArray;
}

function getEquippedItems($pdo, $record_id) {
    $sql = "SELECT * FROM userequippedgear WHERE userequippedgear.userID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $record_id);
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;

        } else {
            echo "No data found for user.";
        }
    } else {
        echo "Error fetching inventory:" . $pdo->error;
    }
}

function equipGear($pdo, $gear_id, $slot_column) {
    $allowed_columns = ['accessory', 'weapon', 'headwear', 'catalyst', 'bodywear', 'signature'];
    extract($_SESSION['userData']);

    if (!in_array($slot_column, $allowed_columns)) {
        die("Invalid column name");
    }
    if ( ! ctype_digit(strval($gear_id)) ) {
        die("nuh uh! you're not sneaky");
    }
    if ( ! ctype_digit(strval($gear_id)) ) {
        die("nuh uh!");
    }

    $sql = "UPDATE userequippedgear SET $slot_column = :ge WHERE userID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":ge", $gear_id);
    $stmt->bindParam(":id", $dbID);

    if ($stmt->execute()) {
        echo "Equipped gear successfully.";
    } else {
        echo "Error updating gear:" . $pdo->error;
    }
}

function transferFunds($pdo, $from_column, $to_column) {
    $allowed_columns = ['balChecking', 'balSavings', 'balOffshore', 'balHighYield'];
    extract($_SESSION['userData']);

        if (!in_array($from_column, $allowed_columns)) {
            die("Invalid column name");
        }
        if (!in_array($to_column, $allowed_columns)) {
            die("Invalid column name");
        }

        $funds_amount = $_POST['funds_amount'];

        $sql = "UPDATE users SET $from_column = $from_column - :fa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":fa", $funds_amount);
        $stmt->bindParam(":id", $dbID);
    
        if ($stmt->execute()) {
            if ($from_column === "balHighYield") {
                $timestamp = date('Y-m-d H:i:s');
                $sql = "UPDATE users SET lastHYW = :ts where id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":ts", $timestamp);
                $stmt->bindParam(":id", $dbID);
                if ($stmt->execute()) {
                    echo "Last high yield withdrawal updated successfully!";
                } else {
                    echo "Error updating HYW record: " . $pdo->error;
                }
            }
            echo "Data updated successfully! (1/2)";
            $sql = "UPDATE users SET $to_column = $to_column + :fa WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":fa", $funds_amount);
            $stmt->bindParam(":id", $dbID);
            if ($stmt->execute()) {
                echo "Data updated successfully! (2/2)";
            } else {
                echo "Error updating record (2/2): " . $pdo->error;
            }
        } else {
            echo "Error updating record (1/2): " . $pdo->error;
        }
}

function setNewJob($pdo, $job_id) {

    extract($_SESSION['userData']);

    $sql = "UPDATE users SET job_id = :ji WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":ji", $job_id);
    $stmt->bindParam(":id", $dbID);
    if ($stmt->execute()) {
        echo "Data updated successfully!";
    } else {
        echo "Error updating record: " . $pdo->error;
    }
}

function getHarvests($pdo) {
    $sql = "SELECT * FROM harvests";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } else {
        }
    } else {
    }
}

function getBounties($pdo) {
    $sql = "SELECT * FROM bounties";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } else {
        }
    } else {
    }
}

function setUserBounty($pdo, $record_id, $success, $capture_success) {
    if ($success == 0) {
        $sql = "UPDATE hunting SET awaiting_collec = 1, successful = 0, capture_success = 0 WHERE user_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $record_id);
        $stmt->execute();
    } else {
        if ($capture_success == 0) {
            $sql = "UPDATE hunting SET awaiting_collec = 1, successful = 1, capture_success = 0 WHERE user_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $record_id);
            $stmt->execute();
        } else {
            $sql = "UPDATE hunting SET awaiting_collec = 1, successful = 1, capture_success = 1 WHERE user_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $record_id);
            $stmt->execute();
        }
    }
}

function claimBounty($pdo) {

    extract($_SESSION['userData']);

    $userBounty = getUserHunting($pdo, $dbID);
    $bounties = getBounties($pdo);
    $thisBounty = $bounties[$userBounty['bounty_id']];

    if ($thisBounty['reward_type'] == 0 && $userBounty['successful'] == 1) {
        if ($userBounty['capture_success'] == 1) {
            $reward = 2*$thisBounty['reward_cash'];
            auditAccount($pdo, $dbID, 2, $reward);
        } else {
            auditAccount($pdo, $dbID, 2, $thisBounty['reward_cash']);
        }
    } else if ($thisBounty['reward_type'] == 1 && $userBounty['successful'] == 1) {
        if ($userBounty['capture_success'] == 1) {
            $reward_amount = 2*$thisBounty['item_quantity'];
            auditInventory($pdo, $dbID, $thisBounty['reward_item'], $reward_amount);
        } else {
            auditInventory($pdo, $dbID, $thisBounty['reward_item'], $thisBounty['item_quantity']);
        }
    }
    $sql = "UPDATE hunting SET bounty_id = 0, awaiting_collec = 0, successful = 0, capture_success = 0 WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $dbID);
    $stmt->execute();
}

function calculateItemsHeld($pdo, $harvest_id) {

    extract($_SESSION['userData']);

    $harvests = getHarvests($pdo);
    $user_harvests = getUserHarvests($pdo, $dbID);

    $currentHarvest = $harvests[$harvest_id - 1];
    
    $lastCollected = strtotime($user_harvests[$harvest_id . '_lastCollected']);
    $now = microtime(true);
    $daysSinceLastCollected = ($now - $lastCollected) / (24 * 60 * 60);
    $uncappedItemsHeld = $daysSinceLastCollected * $currentHarvest['item_quantity'];
    $itemsHeld = min(floor($uncappedItemsHeld), $currentHarvest['max_items']);

    return $itemsHeld;
}

function collectHarvest($pdo, $harvest_id) {

    extract($_SESSION['userData']);

    $itemsHeld = calculateItemsHeld($pdo, $harvest_id);
    $harvests = getHarvests($pdo);
    $currentHarvest = $harvests[$harvest_id - 1];
    $item_id = $currentHarvest['item'];

    if ($itemsHeld == 0) {
        die("No items to collect");
    }

    $validColumns = [
        "1" => "`1_lastCollected`",
        "2" => "`2_lastCollected`",
        "3" => "`3_lastCollected`",
        "4" => "`4_lastCollected`",
        "5" => "`5_lastCollected`"
    ];

    if (!isset($validColumns[$harvest_id])) {
        die("Invalid harvest id.");
    }

    $timestamp = date('Y-m-d H:i:s');
    $sql = "UPDATE harvestsunlocked SET {$validColumns[$harvest_id]} = :ts WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":ts", $timestamp);
    $stmt->bindParam(":id", $dbID);
    if ($stmt->execute()) {
        echo "Data updated successfully!";

        $sql = "
            INSERT INTO inventory (user_id, item_id, quantity) 
            VALUES (:user_id, :item_id, :quantity)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity);
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $dbID);
        $stmt->bindParam(":item_id", $item_id);
        $stmt->bindParam(":quantity", $itemsHeld);
        if ($stmt->execute()) {
            echo "Data updated successfully!";
        } else {
            echo "Error updating record: " . $pdo->error;
        }

    } else {
        echo "Error updating record: " . $pdo->error;
    }
    
}

function purchaseHarvest($pdo, $harvest_id) {
    extract($_SESSION['userData']);

    $harvests = getHarvests($pdo);
    $currentHarvest = $harvests[$harvest_id - 1];
    $cost = $currentHarvest['cost'];

    $user_data_db = getUserFromDatabase($pdo,$discord_id);
    $balChecking = $user_data_db['balChecking'];

    if ($cost > $balChecking) {
        die("Insufficient funds.");
    }

    $validColumns = [
        "1" => "`1_lastCollected`",
        "2" => "`2_lastCollected`",
        "3" => "`3_lastCollected`",
        "4" => "`4_lastCollected`",
        "5" => "`5_lastCollected`"
    ];
    if (!isset($validColumns[$harvest_id])) {
        die("Invalid harvest id.");
    }

    $mats = array();
    $mats[$currentHarvest['crafting_mat1']] = $currentHarvest['mat1_required'];
    $mats[$currentHarvest['crafting_mat2']] = $currentHarvest['mat2_required'];
    $mats[$currentHarvest['crafting_mat3']] = $currentHarvest['mat3_required'];
    foreach($mats as $currentMat => $currentMatRequired){
        if (!isset($currentMatRequired)) {
            continue;
        }
        $sql = "SELECT * FROM inventory WHERE user_id = :user_id and item_id = :item_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":user_id", $dbID);
        $stmt->bindParam(":item_id", $currentMat);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && $row['quantity'] >= $currentMatRequired) {
            $updateSql = "UPDATE inventory SET quantity = quantity - :matCost WHERE user_id = :user_id and item_id = :item_id";
            $stmt = $pdo->prepare($updateSql);
            $stmt->bindParam(":user_id", $dbID);
            $stmt->bindParam(":item_id", $currentMat);
            $stmt->bindParam(":matCost", $currentMatRequired);
            $stmt->execute();
        } else {
            die("Not enough materials.");
        }
    }
    $balSql = "UPDATE users SET balChecking = balChecking - :cost WHERE id = :id";
    $stmt = $pdo->prepare($balSql);
    $stmt->bindParam(":cost", $cost);
    $stmt->bindParam(":id", $dbID);
    $stmt->execute();

    $harvestSql = "UPDATE harvestsunlocked SET `$harvest_id` = 1 WHERE user_id = :id";
    $stmt = $pdo->prepare($harvestSql);
    $stmt->bindParam(":id", $dbID);
    $stmt->execute();
    $timestamp = date('Y-m-d H:i:s');
    $timestampSql = "UPDATE harvestsunlocked SET {$validColumns[$harvest_id]} = :ts WHERE user_id = :id";
    $stmt = $pdo->prepare($timestampSql);
    $stmt->bindParam(":ts", $timestamp);
    $stmt->bindParam(":id", $dbID);
    $stmt->execute();
}

function beginTheft($pdo, $user_selected, $account_selected) {
    extract($_SESSION['userData']);
    $timestamp = date('Y-m-d H:i:s');
    $sql = "UPDATE theft SET active_theft = 1, attempted_uid = :user_selected, attempted_acc = :account_selected, awaiting_collec = 0, successful = 0, start_time = :start_time WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":user_selected", $user_selected);
    $stmt->bindParam(":account_selected", $account_selected);
    $stmt->bindParam(":start_time", $timestamp);
    $stmt->bindParam(":id", $dbID);
    try {
        $stmt->execute();
        echo 'Theft begun succesfully';
    } catch (Exception $e) {
        echo $e;
    }
}

function cancelTheft($pdo) {
    extract($_SESSION['userData']);
    $sql = "UPDATE theft SET active_theft = 0, attempted_uid = -1, attempted_acc = -1, awaiting_collec = 0, successful = 0 WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $dbID);
    try {
        $stmt->execute();
        echo 'Theft cancelled succesfully';
    } catch (Exception $e) {
        echo $e;
    }
}

function beginBounty($pdo, $bounty_id) {

    extract($_SESSION['userData']);
    $timestamp = date('Y-m-d H:i:s');
    
    $equippedItems = getEquippedItems($pdo, $dbID);
    $stats = calculateStats($_SESSION['user_inventory'], $equippedItems);
    $totalDamage = ($stats[0] * (1 + ($stats[1] / 100)));
    $totalArmor = ($stats[2] * (1 + ($stats[3] / 100)));
    $totalResistance = ($stats[4] * (1 + ($stats[5] / 100)));

    $sql = "UPDATE hunting SET bounty_id = :bid, start_time = :ts, awaiting_collec = 0, dmg = :td, arm = :ta, res = :tr WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $dbID);
    $stmt->bindParam(":bid", $bounty_id);
    $stmt->bindParam(":ts", $timestamp);
    $stmt->bindParam(":td", $totalDamage);
    $stmt->bindParam(":ta", $totalArmor);
    $stmt->bindParam(":tr", $totalResistance);
    try {
        $stmt->execute();
        echo 'Hunt begun successfully';
    } catch (Exception $e) {
        echo $e;
    }

}

function cancelBounty($pdo) {

    extract($_SESSION['userData']);
    $sql = "UPDATE hunting SET bounty_id = 0, awaiting_collec = 0 WHERE user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $dbID);
    try {
        $stmt->execute();
        echo 'Hunt cancelled succesfully';
    } catch (Exception $e) {
        echo $e;
    }
}

function purchaseItem($pdo, $item_id, $quantity, $account) {

    extract($_SESSION['userData']);
    $stmt = $pdo->prepare("SELECT shop_quantity FROM items WHERE id = :item_id");
    $stmt->execute(['item_id' => $item_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentQuantity = (int)$row['shop_quantity'];

    $account = (int)$account;

    $itemRow = getItemInfo($pdo, $item_id);
    $cost = (int)$itemRow['value'];

    $finalCost = -($cost * $quantity);

    if ($quantity > 0) {
        if ($currentQuantity == -1) {
            $transSuccess = auditAccount($pdo, $dbID, $account, $finalCost);
            if ($transSuccess == true) {
                $auditSuccess = auditInventory($pdo, $dbID, $item_id, $quantity);
                if ($auditSuccess == true) {
                    echo "Transaction completed successfully";
                } else {
                    echo "Unable to audit user inventory.";
                }   
            } else {
                echo "User is too broke!";
            }
            
        } else if ($currentQuantity < abs($quantity)) {
            echo "Not enough items in store."; // Not enough to subtract
        } else {
            $transSuccess = auditAccount($pdo, $dbID, $account, $finalCost);
            if ($transSuccess == true) {
                $auditSuccess = auditInventory($pdo, $dbID, $item_id, $quantity);
                if ($auditSuccess == true) {
                    $finalQuantity = $currentQuantity - $quantity;

                    $stmt = $pdo->prepare("UPDATE items SET shop_quantity = :fq WHERE id = :item_id");
                    $stmt->execute(['fq' => $finalQuantity, 'item_id' => $item_id]);
                    echo "Transaction completed successfully";
                } else {
                    echo "Unable to audit user inventory.";
                }   
            } else {
                echo "User is too broke!";
            }
        }
    }
}

function sellItem($pdo, $item_id, $quantity) {
    extract($_SESSION['userData']);

    if(auditInventory($pdo, $dbID, $item_id, -$quantity) == true) {
        $itemInfo = getItemInfo($pdo, $item_id);
        $value = $itemInfo['value'];
        $total = $value * $quantity;

        if($itemInfo['evil'] == 0) {
            $account = 0;
        } else if ($itemInfo['evil'] == 1) {
            $account = 2;
        }
        auditAccount($pdo, $dbID, $account, $total);
        $shopQuantity = $itemInfo['shop_quantity'];
        
        if ($shopQuantity == -1) {
            echo "Transaction completed successfully.";
        } else {
            $finalQuantity = $shopQuantity + $quantity;
            $stmt = $pdo->prepare("UPDATE items SET shop_quantity = :fq WHERE id = :item_id");
            $stmt->execute(['fq' => $finalQuantity, 'item_id' => $item_id]);
            echo "Transaction completed successfully.";
        }
    } else {
        echo "Not enough items to sell!";
    }
    
}

function purchaseStock($pdo, $stock_id, $quantity) {
    extract($_SESSION['userData']);
    $stockRow = getStockInfo($pdo, $stock_id);
    $cost = $stockRow['current_value'];
    $finalCost = -($cost * $quantity);

    $transSuccess = auditAccount($pdo, $dbID, 0, $finalCost);
    if ($transSuccess == true) {
        $auditSuccess = auditStockInventory($pdo, $dbID, $stock_id, $quantity);
        if ($auditSuccess == true) {
            echo "Transaction completed successfully";
        } else {
            echo "Unable to audit user inventory.";
        }
    } else {
        echo "User is too broke!";
    }

}

function sellStock($pdo, $stock_id, $quantity) {
    extract($_SESSION['userData']);
    
    if (auditStockInventory($pdo, $dbID, $stock_id, -$quantity) == true) {
        $stockRow = getStockInfo($pdo, $stock_id);
        $value = $stockRow['current_value'];
        $total = ($value * $quantity);

        auditAccount($pdo, $dbID, 0, $total);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['purpose'] === 'transferFunds') {
        transferFunds($pdo, $_POST['account_from'], $_POST['account_to']);
    }  else if ($_POST['purpose'] === 'equipGear') {
        equipGear($pdo, $_POST['gear_id'], $_POST['slot_column']);
    } else if ($_POST['purpose'] === 'setNewJob') {
        setNewJob($pdo, $_POST['job_id']);
    } else if ($_POST['purpose'] === 'collectHarvest') {
        collectHarvest($pdo, $_POST['harvest_id']);
    } else if ($_POST['purpose'] === 'purchaseHarvest') {
        purchaseHarvest($pdo, $_POST['harvest_id']);
    } else if ($_POST['purpose'] === 'beginTheft') {
        beginTheft($pdo, $_POST['user_selected'], $_POST['account_selected']);
    } else if ($_POST['purpose'] === 'cancelTheft') {
        cancelTheft($pdo);
    } else if ($_POST['purpose'] === "startBounty") {
        beginBounty($pdo, $_POST['bounty_id']);
    } else if ($_POST['purpose'] === "cancelBounty") {
        cancelBounty($pdo);
    } else if ($_POST['purpose'] === "purchaseItem") {
        purchaseItem($pdo, $_POST['item_id'], $_POST['quantity'], $_POST['account']);
    } else if ($_POST['purpose'] === "purchaseStock") {
        purchaseStock($pdo, $_POST['stock_id'], $_POST['quantity']);
    } else if ($_POST['purpose'] === "claimBounty") {
        claimBounty($pdo);
    } else if ($_POST['purpose'] === "claimTheft") {
        claimTheft($pdo);
    } else if ($_POST['purpose'] === "sellItem") {
        sellItem($pdo, $_POST['item_id'], $_POST['quantity']);
    } else if ($_POST['purpose'] === "sellStock") {
        sellStock($pdo, $_POST['stock_id'], $_POST['quantity']);
    }
}

