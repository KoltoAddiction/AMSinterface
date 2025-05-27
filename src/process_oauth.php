<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

error_reporting(E_ALL);
session_start();
include('db.php');
require_once('/var/config.php');

$_SESSION['current_version'] = "0.0.13";

if(!isset($_GET['code'])){
    echo 'no code';
    exit();
}

$discord_code = filter_input(INPUT_GET, 'code', FILTER_DEFAULT);
if (!$discord_code) {
    echo "Invalid code";
    exit();
}

$payload = [
    'code'=>$discord_code,
    'client_id'=>CLIENT_ID,
    'client_secret'=>CLIENT_SECRET,
    'grant_type'=>'authorization_code',
    'redirect_uri'=>REDIRECT,
    'score'=>'identify%20guilds',
];

print_r($payload);

$payload_string = http_build_query($payload);
$discord_token_url = "https://discord.com/api/oauth2/token";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $discord_token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    error_log('cURL error: ' . curl_error($ch));
    die("An error occurred.");
}

$result = json_decode($result,true);
$access_token = $result['access_token'];

$discord_users_url = "https://discord.com/api/users/@me";
$header = array("Authorization: Bearer $access_token", "Content-Type: application/x-www-form-urlencoded");

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_URL, $discord_users_url);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    error_log('cURL error: ' . curl_error($ch));
    die("An error occurred.");
}

$userData = json_decode($result,true);
$userDID = $userData['id'];

$guildObject = getGuildObject($access_token, '694954781470359623');
if (!$guildObject || isset($guildObject['message'])) {
    header("location: error.php");
    exit();
}

$guild_roles = $guildObject['roles'];

$user_data_db = getUserFromDatabase($pdo,$userDID);
$user_data_id = $user_data_db["id"];

if(!$user_data_db){
    addUserToDatabase($pdo,$userData['id'],$userData['username'],$userData['avatar']);
    $user_data_db = getUserFromDatabase($pdo,$userDID);
    $user_data_id = $user_data_db["id"];
} else {
    updateUserToDatabase($pdo,$user_data_id,$userData['username'],$userData['avatar']);
    $user_data_db = getUserFromDatabase($pdo,$userDID);
}

$user_equipped_gear = getEquippedItems($pdo, $user_data_id);
$user_hcoin = getHCOIN($pdo, $user_data_id);
$user_harvests = getUserHarvests($pdo, $user_data_id);
$user_theft = getUserTheft($pdo, $user_data_id);
$user_hunting = getUserHunting($pdo, $user_data_id);

if(!$user_equipped_gear){
    addUserEquippedGear($pdo, $user_data_id);
    $user_equipped_gear = getEquippedItems($pdo, $user_data_id);
}
if(!$user_harvests){
    addUserHarvests($pdo, $user_data_id);
    $user_harvests = getUserHarvests($pdo, $user_data_id);
}
if(!$user_theft){
    addUserTheft($pdo, $user_data_id);
    $user_theft = getUserTheft($pdo, $user_data_id);
}
if(!$user_hunting){
    addUserHunting($pdo, $user_data_id);
    $user_hunting = getUserHunting($pdo, $user_data_id);
}

$ca = curl_init();
curl_setopt($ca, CURLOPT_HTTPHEADER, array("Authorization: " . AMARI));
curl_setopt($ca, CURLOPT_URL, "https://amaribot.com/api/v1/guild/694954781470359623/member/$userDID");
curl_setopt($ca, CURLOPT_RETURNTRANSFER, true);
$amariResult = curl_exec($ca);

if (curl_errno($ca)) {
    error_log('cURL error: ' . curl_error($ca));
    die("An error occurred.");
}

$amariUserData = json_decode($amariResult, true);

$_SESSION['logged_in'] = true;
$_SESSION['userData'] = [
    'dbID'=>$user_data_id,
    'discord_id'=>$userData['id'],
    'name'=>$userData['username'],
    'avatar'=>$userData['avatar'],
    'roles'=>$guild_roles,
    'level'=>$amariUserData['level'],
    'exp'=>$amariUserData['exp'],
    'weeklyExp'=>$amariUserData['weeklyExp'],
];



header("location: dashboard.php");
exit();

function getGuildObject($access_token, $guild_id){
        //requires the following scope: guilds.members.read
        $discord_api_url = "https://discordapp.com/api";
        $header = array("Authorization: Bearer $access_token","Content-Type: application/x-www-form-urlencoded");
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_URL, $discord_api_url.'/users/@me/guilds/'.$guild_id.'/member');
        curl_setopt($ch,CURLOPT_POST, false);
        //curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result,true);
        return $result;
}

?>