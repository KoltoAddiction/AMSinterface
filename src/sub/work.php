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
$job_data = getJobInfo($pdo, $user_data_db['job_id']);
$jobTag = $job_data['job_tag'];
$item1_data = getItemInfo($pdo, $job_data['item1']);
$item2_data = getItemInfo($pdo, $job_data['item2']);
$item3_data = getItemInfo($pdo, $job_data['item3']);
$option1_data = getJobInfo($pdo, $job_data['option1']);
$option2_data = getJobInfo($pdo, $job_data['option2']);

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

setcookie("lastPage", "work");

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../../dist/style.css" rel="stylesheet">
        <link href="../../dist/work.css" rel="stylesheet">
        <script type="module" src="work.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='workScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="workContent">
                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="workBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="workLogout"></a>
                    </div>
                </div>

                <div class="menu" id="workMenu">
                    <div id="workMenuHeader">
                        <h2 class="jobHeaderInfo"><?php 
                        $pathsArray = ["Path of the Predestined", "Path of the Believer", "Path of the Mercenary", "Path of the Omniscient"];
                        if(substr($jobTag, -1) == "A" || substr($jobTag, -1) == "E") {
                            echo $pathsArray[0]; 
                        } else if(substr($jobTag, -1) == "B" || substr($jobTag, -1) == "F") {
                            echo $pathsArray[1];
                        } else if(substr($jobTag, -1) == "C" || substr($jobTag, -1) == "G") {
                            echo $pathsArray[2];
                        } else if(substr($jobTag, -1) == "D" || substr($jobTag, -1) == "H") {
                            echo $pathsArray[3];
                        }
                        ?></h2>
                        <h2 class="jobHeaderInfo"><?php echo $job_data['name']; ?></h2>
                        <input class="jobHeaderInfo" type="button" value="> current offers" id="positionsButton">
                    </div>
                    <div id="workInfo">
                        <div class="workInfoColumn" id="incomeColumn">
                        <h2 class="jobInfo">job tag:</h2>
                        <h2 class="jobInfo">[ <?php echo $job_data['job_tag']; ?> ]</h2>
                            <h2 class="jobInfo">daily income:</h2>
                            <h2 class="jobInfo">[ $<?php echo $job_data['income']; ?> ]</h2>
                        </div>
                        <div class="workInfoColumn">
                        <h2 class="jobInfo">daily materials:</h2>
                        <div class="subColumn"><?php
                            if(!$item1_data == []) {
                                echo "
                                <div>
                                    <h3 class='spacingJobSuperSubInfo'>/item 1</h3>
                                    <h2 class='jobSubInfo'>[ " . $item1_data['name'] . " ]</h2>
                                    <h2 class='jobSubInfo'>quantity: [ " . $job_data['item1_quantity'] . " ]</h2>
                                </div>
                                ";
                            } else {
                                echo "
                                <div>
                                    <h2 class='jobSubInfo'>none</h2>
                                </div>
                                ";
                            }
                            if(!$item2_data == []) {
                                echo "
                                <div>
                                    <h3 class='spacingJobSuperSubInfo'>/item 2</h3>
                                    <h2 class='jobSubInfo'>[ " . $item2_data['name'] . " ]</h2>
                                    <h2 class='jobSubInfo'>quantity: [ " . $job_data['item2_quantity'] . " ]</h2>
                                </div>
                                ";
                            }
                            if(!$item3_data == []) {
                                echo "
                                <div>
                                    <h3 class='spacingJobSuperSubInfo'>/item 3</h3>
                                    <h2 class='jobSubInfo'>[ " . $item3_data['name'] . " ]</h2>
                                    <h2 class='jobSubInfo'>quantity: [ " . $job_data['item3_quantity'] . " ]</h2>
                                </div>
                                ";
                            }
                        
                        ?></div>
                        </div>
                        <div class="workInfoColumn" id="descriptionColumn">
                            <h2 class='jobInfo'>job description:</h2>
                            <div class="subColumn">
                                <h2 class='jobDescription'><?php echo $job_data['description']; ?></h2>
                            </div>
                        </div>
                    </div>

                    <div id="currentOffersPopup" class="modal">
                        <div class="modal-content">
                            <span id="closeCurrentOffers" class="closeModal">&times;</span>
                            <h2 class="popupTitle">/job offers</h2>
                            <div id="offersMenu"> 
                                <?php
                                    if(empty($option1_data) && empty($option2_data)) {
                                        echo "
                                        <h2 id='endOfPathMessage'>Congratulations! You've reached the end of your career path. Perhaps, someday, you'll advance to even greater heights! </h2>
                                        ";
                                    } else {
                                        $levelsArray = ["1000" => "0", "2010" => "10", "3020" => "20", "4035" => "35", "5050" => "50", "6075" => "75", "7100" => "100", "8125" => "125", "9150" => "150"];
                                        $prestigesArray = ["10P1" => "1", "11P2" => "2", "12P3" => "3", "13P4" => "4", "14P5" => "5"];
                
                                        $requiredLevel = 150;
                                        $requiredPrestige = 5;
                                        $usePrestige = false;
                                        
                                        $numberTag = substr($jobTag, 1, 4);
                                        $nextTag = substr($option1_data['job_tag'], 1, 4);
                                        if(array_key_exists($numberTag, $levelsArray)) {
                                            if($numberTag === $nextTag) {
                                                $requiredLevel = $levelsArray[$numberTag] + 7;
                                            } else if (array_key_exists($nextTag, $prestigesArray) && !array_key_exists($numberTag, $prestigesArray)) {
                                                $requiredPrestige = 1;
                                                $usePrestige = true;
                                            } else {
                                                $requiredLevel = $levelsArray[$nextTag];
                                            }
                                        } else if (array_key_exists($numberTag, $prestigesArray)) {
                                            $requiredPrestige = $prestigesArray[$nextTag];
                                            $usePrestige = true;
                                        }

                                        if($user_data_db['prestigeTier'] < $requiredPrestige && $usePrestige == true) {
                                            echo "
                                                <div class='jobOfferSolo'>
                                                    <div class='soloColumn'>
                                                        <h2 class='jobInfo'>unknown</h2>
                                                        <h3 class='jobSuperSubInfo'>[ unknown ]</h3>
                                                        <h2 class='jobSubInfo'>$/day: [ ??? ]</h2>
                                                        <h3 class='spacingJobSuperSubInfo'>items:</h3>
                                                        <h2 class='jobSubInfo'>[ unknown ]</h2>
                                                        <h2 class='jobSubInfo'>[ unknown ]</h2>
                                                        <h2 class='jobSubInfo'>[ unknown ]</h2>
                                                    </div>
                                                    <div class='soloColumn'>
                                                        <h2 class='jobInfo'>no offers!</h2>
                                                        <h2 class='jobDescription'>reach prestige tier [ " . $requiredPrestige . " ] to see new work opportunities.</h2>
                                                    </div>
                                                </div>
                                            ";
                                        } else if($level < $requiredLevel && $usePrestige == false) {
                                            echo "
                                            <div class='jobOfferSolo'>
                                                <div class='soloColumn'>
                                                    <h2 class='jobInfo'>unknown</h2>
                                                    <h3 class='jobSuperSubInfo'>[ unknown ]</h3>
                                                    <h2 class='jobSubInfo'>$/day: [ ??? ]</h2>
                                                    <h3 class='spacingJobSuperSubInfo'>items:</h3>
                                                    <h2 class='jobSubInfo'>[ unknown ]</h2>
                                                    <h2 class='jobSubInfo'>[ unknown ]</h2>
                                                    <h2 class='jobSubInfo'>[ unknown ]</h2>
                                                </div>
                                                <div class='soloColumn'>
                                                    <h2 class='jobInfo'>no offers!</h2>
                                                    <h2 class='jobDescription'>reach level [ " . $requiredLevel . " ] to see new work opportunities.</h2>
                                                </div>
                                            </div>
                                        ";
                                        } else if (($level >= $requiredLevel && $usePrestige == false) || ($user_data_db['prestigeTier'] >= $requiredPrestige && $usePrestige == true)) {
                                            
                                            function getItemData($pdo, $itemKey, $optionData, $defaultName = "N/A") {
                                                $itemData = getItemInfo($pdo, $optionData[$itemKey]);
                                                return !empty($itemData) ? $itemData : ['name' => $defaultName];
                                            }
                                            
                                            // Define item keys and their default values
                                            $itemKeys = [
                                                'item1' => 'N/A',
                                                'item2' => 'N/A',
                                                'item3' => '/none'
                                            ];

                                            if(!empty($option1_data) && !empty($option2_data)) {
                                                
                                                // Process option 1
                                                foreach ($itemKeys as $key => $default) {
                                                    ${"option1_{$key}_data"} = getItemData($pdo, $key, $option1_data, $default);
                                                }
                                                
                                                // Process option 2
                                                foreach ($itemKeys as $key => $default) {
                                                    ${"option2_{$key}_data"} = getItemData($pdo, $key, $option2_data, $default);
                                                }

                                                echo "
                                                    <div class='jobOfferDuo'>
                                                            <h2 class='jobInfo'>" . $option1_data['name'] . "</h2>
                                                            <h3 class='jobSuperSubInfo'>[ " . $option1_data['job_tag'] . " ]</h3>
                                                            <h2 class='jobSubInfo'>$/day: [ $" . $option1_data['income'] . " ]</h2>
                                                            <h3 class='spacingJobSuperSubInfo'>items:</h3>
                                                            <h2 class='jobSubInfo'>[ " . $option1_item1_data['name'] . " ]</h2>
                                                            <h2 class='jobSubInfo'>[ " . $option1_item2_data['name'] . " ]</h2>
                                                            <h2 class='jobSubInfo'>[ " . $option1_item3_data['name'] . " ]</h2>
                                                            <h2 class='jobInfo'>offer details:</h2>
                                                            <h2 class='jobDescription'>" . $option1_data['offer_text'] . "</h2>
                                                            <input type='button' class='acceptButton' id='" . $option1_data['id'] . "' value='> accept job'>
                                                    </div>
                                                            <div class='jobOfferDuo'>
                                                            <h2 class='jobInfo'>" . $option2_data['name'] . "</h2>
                                                            <h3 class='jobSuperSubInfo'>[ " . $option2_data['job_tag'] . " ]</h3>
                                                            <h2 class='jobSubInfo'>$/day: [ $" . $option2_data['income'] . " ]</h2>
                                                            <h3 class='spacingJobSuperSubInfo'>items:</h3>
                                                            <h2 class='jobSubInfo'>[ " . $option2_item1_data['name'] . " ]</h2>
                                                            <h2 class='jobSubInfo'>[ " . $option2_item2_data['name'] . " ]</h2>
                                                            <h2 class='jobSubInfo'>[ " . $option2_item3_data['name'] . " ]</h2>
                                                            <h2 class='jobInfo'>offer details:</h2>
                                                            <h2 class='jobDescription'>" . $option2_data['offer_text'] . "</h2>
                                                            <input type='button' class='acceptButton' id='" . $option2_data['id'] . "' value='> accept job'>
                                                    </div>
                                                ";

                                            } else if (!empty($option1_data)) {

                                                
                                                // Process option 1
                                                foreach ($itemKeys as $key => $default) {
                                                    ${"option1_{$key}_data"} = getItemData($pdo, $key, $option1_data, $default);
                                                }
                                                
                                                echo "
                                                    <div class='jobOfferSolo'>
                                                        <div class='soloColumn'>
                                                            <h2 class='jobInfo'>" . $option1_data['name'] . "</h2>
                                                            <h3 class='jobSuperSubInfo'>[ " . $option1_data['job_tag'] . " ]</h3>
                                                            <h2 class='jobSubInfo'>$/day: [ $" . $option1_data['income'] . " ]</h2>
                                                            <h3 class='spacingJobSuperSubInfo'>items:</h3>
                                                            <h2 class='jobSubInfo'>[ " . $option1_item1_data['name'] . " ]</h2>
                                                            <h2 class='jobSubInfo'>[ " . $option1_item2_data['name'] . " ]</h2>
                                                            <h2 class='jobSubInfo'>[ " . $option1_item3_data['name'] . " ]</h2>
                                                            <input type='button' class='acceptButton' id='" . $option1_data['id'] . "' value='> accept job'>
                                                        </div>
                                                        <div class='soloColumn'>
                                                            <h2 class='jobInfo'>offer details:</h2>
                                                            <h2 class='jobDescription'>" . $option1_data['offer_text'] . "</h2>
                                                        </div>
                                                    </div>
                                                ";
                                            }
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="workReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>