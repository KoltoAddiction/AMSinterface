<?php

require_once('db.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function calcTheft($pdo, $account, $target) {
    if($account == 0) {
        $odds = rand(0, 99);
        if ($odds < 15) {
            $targetData = getUserByUID($pdo, $target);
            $percentSteal = rand(25,50)/100;

            $stolen = $targetData['balChecking'] * $percentSteal;
            return $stolen;
        } else {
            return 0;
        }
    }
    if($account == 1) {
        $odds = rand(0, 99);
        if ($odds < 60) {
            $targetData = getUserByUID($pdo, $target);
            $percentSteal = rand(25,75)/100;

            $stolen = $targetData['balOffshore'] * $percentSteal;
            return $stolen;
        } else {
            return 0;
        }
    }
}

function calcBountyOdds($user_data, $bounty_data) {
    $base_odds = $bounty_data['rate'];
    $base_capture_odds = $bounty_data['capture_rate'];
    $base_time = $bounty_data['time'];

    $user_dmg = $user_data['dmg'];
    $user_arm = $user_data['arm'];
    $user_res = $user_data['res'];

    $bounty_dmg = $bounty_data['damage'];
    $bounty_arm = $bounty_data['armor'];
    $bounty_res = $bounty_data['resistance'];

    $final_odds = floor(((($user_dmg/$bounty_dmg) + ($user_arm/$bounty_arm) + ($user_res/$bounty_res))/3)*$base_odds);
    $final_capture_odds = floor(((($user_dmg/$bounty_dmg) + ($user_arm/$bounty_arm) + ($user_res/$bounty_res))/3)*$base_capture_odds);

    if (((($user_dmg/$bounty_dmg) + ($user_arm/$bounty_arm) + ($user_res/$bounty_res))/3) > 1) {
        $final_time = floor($base_time - ((($user_dmg/$bounty_dmg) + ($user_arm/$bounty_arm) + ($user_res/$bounty_res))/12) * $base_time);
    } else {
        $final_time = floor($base_time + ($base_time - ((($user_dmg/$bounty_dmg) + ($user_arm/$bounty_arm) + ($user_res/$bounty_res))/12) * $base_time));
    }
    if ($final_time == 0) {
        $final_time = 1;
    }

    return [$final_odds, $final_capture_odds, $final_time];
}

function calcBounty($odds, $capture_odds) {

    $rand = rand(0, 99);
    if($rand < $odds) {
        if ($rand < $capture_odds) {
            return 2;
        } else {
            return 1;
        }
    } else {
        return 0;
    }

}