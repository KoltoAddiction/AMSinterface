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

setcookie("lastPage", "harvest");

$harvests = getHarvests($pdo);
$user_harvests = getUserHarvests($pdo, $db_id);

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../../dist/style.css" rel="stylesheet">
        <link href="../../dist/harvest.css" rel="stylesheet">
        <script type="module" src="harvest.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='harvestScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="harvestContent">
                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="harvestBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="harvestLogout"></a>
                    </div>
                </div>

                <div class="menu" id="harvestMenu">
                    <?php 
                        foreach($harvests as $currentHarvest) {
                            $currentHarvestID = $currentHarvest['id'];
                             if($level < $currentHarvest['required_level']) {
                                echo "
                                <div class='harvestThing' style='justify-content:center;'>
                                    <h2 class='largeThingText' style='padding-top:20px;'>Reach level [" . $currentHarvest['required_level'] . "] in order to unlock this harvest.</h2>
                                </div>
                                "; 
                             } else {
                                if ($user_harvests[$currentHarvestID] == 1) {
                                    $currentItemsHeld = calculateItemsHeld($pdo, $currentHarvestID);
                                    $itemProduced = getItemInfo($pdo, $currentHarvest['item']);
                                    echo "
                                    <div class='harvestThing'>
                                        <h2 class='largeThingText'>[ ACTIVE ]</h2>
                                        <h2 class='medThingText'>[ " . $currentHarvest['name'] . " ]</h2>
                                        <h2 class='smallThingText'>producing:</h2>
                                        <h2 class='medThingText'>[ " . $itemProduced['name'] . " ]</h2>
                                        <h2 class='smallThingText'>production rate:</h2>
                                        <h2 class='medThingText'>[ " . $currentHarvest['item_quantity'] . "/day ]</h2>
                                        <h2 class='smallThingText'>items held:</h2>
                                        <h2 class='medThingText'>[ " . $currentItemsHeld . "/" . $currentHarvest['max_items'] . " ]</h2>
                                        <input type='button' class='collectButton' id='collect" . $currentHarvest['id'] . "' value='> collect items'>
                                    </div>
                                    "; 
                                } else {
                                    $craftingMat1 = getItemInfo($pdo, $currentHarvest['crafting_mat1']);
                                    $craftingMat2 = getItemInfo($pdo, $currentHarvest['crafting_mat2']);
                                    $craftingMat3 = getItemInfo($pdo, $currentHarvest['crafting_mat3']);
                                    if ($currentHarvest['crafting_mat2'] == 0) {
                                        echo "
                                        <div class='harvestThing'>
                                            <h2 class='largeThingText'>[ AVAILABLE ]</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['name'] . " ]</h2>
                                            <h2 class='smallThingText'>cost:</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['cost'] . " ]</h2>
                                            <h2 class='smallThingText'>materials required:</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['mat1_required'] . " " . $craftingMat1['name'] . " ]</h2>
                                            <input type='button' class='purchaseButton' id='purchase" . $currentHarvest['id'] . "' value='> purchase harvest'>
                                        </div>
                                        "; 
                                    } else if ($currentHarvest['crafting_mat3'] == 0) {
                                        echo "
                                        <div class='harvestThing'>
                                            <h2 class='largeThingText'>[ AVAILABLE ]</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['name'] . " ]</h2>
                                            <h2 class='smallThingText'>cost:</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['cost'] . " ]</h2>
                                            <h2 class='smallThingText'>materials required:</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['mat1_required'] . " " . $craftingMat1['name'] . " ]</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['mat2_required'] . " " . $craftingMat2['name'] . " ]</h2>
                                            <input type='button' class='purchaseButton' id='purchase" . $currentHarvest['id'] . "' value='> purchase harvest'>
                                        </div>
                                        "; 
                                    } else {
                                        echo "
                                        <div class='harvestThing'>
                                            <h2 class='largeThingText'>[ AVAILABLE ]</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['name'] . " ]</h2>
                                            <h2 class='smallThingText'>cost:</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['cost'] . " ]</h2>
                                            <h2 class='smallThingText'>materials required:</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['mat1_required'] . " " . $craftingMat1['name'] . " ]</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['mat2_required'] . " " . $craftingMat2['name'] . " ]</h2>
                                            <h2 class='medThingText'>[ " . $currentHarvest['mat3_required'] . " " . $craftingMat3['name'] . " ]</h2>
                                            <input type='button' class='purchaseButton' id='purchase" . $currentHarvest['id'] . "' value='> purchase harvest'>
                                        </div>
                                        "; 
                                    }
                                    
                                }

                            }
                        }
                    ?>

                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="harvestReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>