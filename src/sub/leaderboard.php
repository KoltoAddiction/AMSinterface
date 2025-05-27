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

setcookie("lastPage", "leaderboard");

$allowed_columns = ['id', 'discord_username', 'prestigeTier', 'balChecking', 'balSavings', 'balOffshore', 'balHighYield'];
$allowed_orders = ['asc', 'desc'];

$_GET['sort'] = $_GET['sort'] ?? 'id';
$sort_column = $_GET['sort'];
$sort_order = $_GET['order'] ?? 'asc';

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}
if (!in_array($sort_order, $allowed_orders)) {
    $sort_order = 'asc'; // Default to 'asc' if invalid order
}
$new_order = ($sort_column === $_GET['sort']) ? ($sort_order === 'asc' ? 'desc' : 'asc') : (($sort_column === 'id') ? 'asc' : 'desc');

$sql = "SELECT * FROM users ORDER BY $sort_column $sort_order";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo $e;
}

function createSortableHeader($column, $label, $current_sort, $current_order) {
    // Determine the new order based on user input
    $new_order = ($column === $current_sort) ? ($current_order === 'asc' ? 'desc' : 'asc') : 'desc';
    // Display sorting arrow
    $arrow = ($column === $current_sort) ? ($current_order === 'asc' ? '▲' : '▼') : '';
    return "<a href='?sort=$column&order=$new_order'>$label $arrow</a>";
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
        <link href="../../dist/leaderboard.css" rel="stylesheet">
        <script type="module" src="leaderboard.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='leaderboardScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="leaderboardContent">
                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="leaderboardBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="leaderboardLogout"></a>
                    </div>
                </div>

                <div class="menu" id="leaderboardMenu">
                    
                    <table id="leaderboardTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?= createSortableHeader('discord_username', 'username', $sort_column, $sort_order); ?></th>
                                <th><?= createSortableHeader('prestigeTier', 'prestige', $sort_column, $sort_order); ?></th>
                                <th><?= createSortableHeader('balChecking', 'checking', $sort_column, $sort_order); ?></th>
                                <th><?= createSortableHeader('balSavings', 'savings', $sort_column, $sort_order); ?></th>
                                <th><?= createSortableHeader('balOffshore', 'offshore', $sort_column, $sort_order); ?></th>
                                <th><?= createSortableHeader('balHighYield', 'high yield', $sort_column, $sort_order); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_users as $user): ?>
                            <tr class="userRow">
                                    <td><?php echo "<img class='discordAvatar' src='https://cdn.discordapp.com/avatars/".$user['discord_id']."/".$user['discord_avatar'].".jpg'/>"; ?></td>
                                    <td><?= htmlspecialchars($user['discord_username']); ?></td>
                                    <td><?= htmlspecialchars($user['prestigeTier']); ?></td>
                                    <td><?= htmlspecialchars($user['balChecking']); ?></td>
                                    <td><?= htmlspecialchars($user['balSavings']); ?></td>
                                    <td><?= htmlspecialchars($user['balOffshore']); ?></td>
                                    <td><?= htmlspecialchars($user['balHighYield']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="leaderboardReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>