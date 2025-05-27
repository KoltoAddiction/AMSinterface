<?php

include('db.php');
include('update_user.php');

updateUser($pdo);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!$_SESSION['logged_in']){
    header('Location: error.php');
    exit();
}
extract($_SESSION['userData']);

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

// Find user rank and corresponding image.
$role = 'survivor';
if(in_array('909868244196786206', $roles)){
    $role = "echoIncarnate";
} else if(in_array('909868010536308767', $roles)) {
    $role = "absoluteEcho";
} else if(in_array('824669550704459867', $roles)) {
    $role = "fable";
} else if(in_array('772450315089346582', $roles)) {
    $role = "champion";
} else if(in_array('862462213344198677', $roles)) {
    $role = "superior";
} else if(in_array('732212614716981350', $roles)) {
    $role = "harbinger";
} else if(in_array('839506600850227200', $roles)) {
    $role = "zealot";
} else if(in_array('814236595389595648', $roles)) {
    $role = "nice";
} else if(in_array('732204665319718972', $roles)) {
    $role = "fanatic";
} else if(in_array('732203052802506772', $roles)) {
    $role = "devotee";
} else if(in_array('694979412268941413', $roles)) {
    $role = "acolyte";
} else if(in_array('694979041270300733', $roles)) {
    $role = "initiate";
} else {
    $role = "survivor";
}

$degaussSRC = "../assets/hover/click.mp3";
$screenID = "dashboardScreen";

if (isset($_COOKIE['lastPage'])) {
    if ($_COOKIE['lastPage'] == "index") {
        $degaussSRC = "../assets/degauss.mp3";
        $screenID = "dashboardScreenFromIndex";
    }
}

setcookie("lastPage", "dashboard");

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];
$balChecking = $user_data_db['balChecking'];
$balSavings = $user_data_db['balSavings'];
$balOffshore = $user_data_db['balOffshore'];
$balHighYield = $user_data_db['balHighYield'];
$legacyLevel = $user_data_db['legacylvl'];
$prestigeTier = $user_data_db['prestigeTier'];
$specialWord = $user_data_db['specialWord'];
$userJobId = $user_data_db['job_id'];
$user_hCoin = getHCOIN($pdo,$db_id);
$userJobInfo = getJobInfo($pdo, $userJobId)

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../dist/style.css" rel="stylesheet">
        <link href="../dist/dashboard.css" rel="stylesheet">
        <script src="dashboard.js"></script>
        <link rel="icon" href="../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div id="dbID" style="display: none;">
            <?php
                echo htmlspecialchars($db_id); // put as the div content
            ?>
        </div>
        <div class='screen' id='<?php echo $screenID; ?>'>
            <img src="../assets/scanlines.png" id="scan" class="noselect">
            <img src="../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id='dashboardContent'>

                <div id="comingSoonPopup" class="modal">
                        <span id="closeCS" class="closeModal">&times;</span>
                        <h2 class="popupTitle">CALM DOWN!!</h2>
                        <h3 class="errorMessage">this feature is Coming Soon&trade; ;)</h3>
                </div>

                <div id="notBMYet" class="modal">
                        <span id="closeBMY" class="closeModal">&times;</span>
                        <h2 class="popupTitle">unavailable</h2>
                        <h3 class="errorMessage">The black market is only accessible on Mondays.</h3>
                </div>

                <div class="header">
                    <h1>Asylum Management Service v<?php echo $_SESSION['current_version'];?></h1>
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="logout.php"><img class="logout" src="../assets/door.png" onmouseover="playHoverSound()" id="dashboardLogout"></a>
                    </div>

                </div>

                <div id="menu">
                    <input type="button" value="> work" class="menubutton" id="workButton">
                    <input type="button" value="> accounts" class="menubutton" id="accountsButton">
                    <?php if (in_array('694979041270300733', $roles)) {
                        echo '
                            <input type="button" value="> harvest" class="menubutton" id="harvestButton">
                        ';
                    }  else {
                        echo '
                            <input type="button" value="> locked" class="menubutton">
                        ';
                    }
                    ?>
                    <?php if (in_array('694979412268941413', $roles)) {
                        echo '
                            <input type="button" value="> theft" class="menubutton" id="theftButton">
                        ';
                    }  else {
                        echo '
                            <input type="button" value="> locked" class="menubutton">
                        ';
                    }
                    ?>
                    <?php if (in_array('732203052802506772', $roles)) {
                        echo '
                            <input type="button" value="> bounties" class="menubutton" id="bountiesButton">
                        ';
                    }  else {
                        echo '
                            <input type="button" value="> locked" class="menubutton">
                        ';
                    }
                    ?>
                    <?php if (in_array('814236595389595648', $roles)) {
                        echo '
                            <input type="button" value="> grand heist" class="menubutton" id="grandHeistButton">
                        ';
                    }  else {
                        echo '
                            <input type="button" value="> locked" class="menubutton">
                        ';
                    }
                    ?>

                    <input type="button" value="> inventory" class="menubutton" id="inventoryButton">

                    <?php if (in_array('694979041270300733', $roles)) {
                        echo '
                            <input type="button" value="> market" class="menubutton" id="marketButton">
                        ';
                    } else {
                        echo '
                            <input type="button" value="> locked" class="menubutton">
                        ';
                    }
                    ?>
                    <?php if (in_array('732204665319718972', $roles)) {
                        echo '
                            <input type="button" value="> black market" class="menubutton" id="blackMarketButton">
                        ';
                    }else {
                        echo '
                            <input type="button" value="> locked" class="menubutton">
                        ';
                    }
                    ?>
                    
                    <input type="button" value="> leaderboard" class="menubutton" id="leaderboardButton">

                    <?php if (in_array('732212614716981350', $roles)) {
                        echo '
                            <input type="button" value="> prestige" class="menubutton" id="prestigeButton">
                        ';
                    }else {
                        echo '
                            <input type="button" value="> locked" class="menubutton">
                        ';
                    }
                    ?>
                    
                </div>

                <div id="display0">
                    <img id="asciiDisplay" src="">
                </div>

                <div id="userinfo">
                    <img id="rankicon" src="../assets/ranks/<?php echo $role;?>">
                    <h1 id="rankname">[ <?php echo $role;?> ]</h1>
                    <h3 id="level">lvl: <?php echo $level;?></h3>
                    <progress id="levelprogress" max="1240055" value="<?php echo $exp;?>"></progress>
                    <h1 id="legacylevel", class="info">legacy lvl: [ <?php echo $legacyLevel;?> ]</h1>
                    <h1 id="prestige", class="info">prestige tier: [ <?php echo $prestigeTier;?> ]</h1>
                    <h1 id="weeklyExp", class="info">weekly exp: [ <?php echo $weeklyExp;?>xp ]</hi>
                    <h1 id="stipend", class="info">job tag: [ <?php echo $userJobInfo['job_tag'];?> ]</h1>
                    <h1 id="balChecking", class="info">checking: [ $<?php echo $balChecking;?> ]</h1>
                    <h1 id="balSavings", class="info">savings: [ $<?php echo $balSavings;?> ]</h1>
                    <h1 id="balOffshore", class="info">offshore: [ $<?php echo $balOffshore;?> ]</h1>
                    <h1 id="balHighYield", class="info">high-yield: [ $<?php echo $balHighYield;?> ]</h1>
                    <h1 id="nextBlackMarket", class="info">black market: [ ??? ]</h1>
                    <h1 id="balHCoin", class="info">H-coin: [ <?php
                        if(isset($user_hCoin['quantity'])) {
                            echo $user_hCoin['quantity'];
                        } else {
                            echo "0";
                        }
                    ?> ]</h1>
                    <h1 id="specialWord", class="info"> status: [ <?php echo $specialWord;?> ]</h1>
                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="dashboardReauth" align="center" onmouseover="playHoverSound()" onclick="reAuthButton()">> re-authenticate (dev)</div>


            <audio id="degauss" src="<?php echo $degaussSRC; ?>" preload="auto" autoplay>
                Your browser isn't cool enough to join the audio party. You should get on that.
            </audio>
            <audio id="hum" src="../assets/hum.mp3" preload="auto" autoplay loop></audio>
            <audio id="clicksound" src="../assets/hover/click.mp3" preload="auto"></audio>
            <audio id="hoversound" src="../assets/hover/hover.mp3" preload="auto"></audio>
            </div>
        </div>
    </body>
</html>
