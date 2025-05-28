<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('db.php');
require_once('calculate_odds.php');

function updateUser($pdo) {

    extract($_SESSION['userData']);

    setUserUpdateTime($pdo, $dbID);

    $dbData = getUserByUID($pdo, $dbID);

    $last_updated = strtotime($dbData['last_update']);
    $now = time();

    $dis_s = $now - $last_updated;
    $dis_m = ($dis_s/60);
    $dis_h = ($dis_s/3600);
    $dis_d = ($dis_s/86400);
    $dis_y = ($dis_s/31536000);

    // Work        

    $job_id = $dbData['job_id'];
    $job_info = getJobInfo($pdo, $job_id);

    $income = (int)$job_info['income'];

    $deservedIncome = floor($dis_d*$income);
    auditAccount($pdo, $dbID, 0, $deservedIncome);

    $progress1 = 0;
    $progress2 = 0;
    $progress3 = 0;

    if($job_info['item1']) {
        $progress1 = (float) $dbData['item1_progress'];
        $progress1 += $dis_d * $job_info['item1_quantity'];

        $deserved1 = floor($progress1);
        $progress1 -= $deserved1;

        if ($deserved1 > 0) auditInventory($pdo, $dbID, $job_info['item1'], $deserved1);
    }
    if($job_info['item2']) {
        $progress2 = (float) $dbData['item2_progress'];
        $progress2 += $dis_d * $job_info['item2_quantity'];

        $deserved2 = floor($progress2);
        $progress2 -= $deserved2;

        if ($deserved2 > 0) auditInventory($pdo, $dbID, $job_info['item2'], $deserved2);
    }
    if($job_info['item3']) {
        $progress3 = (float) $dbData['item3_progress'];
        $progress3 += $dis_d * $job_info['item3_quantity'];

        $deserved3 = floor($progress3);
        $progress3 -= $deserved3;

        if ($deserved3 > 0) auditInventory($pdo, $dbID, $job_info['item3'], $deserved3);
    }

    updateJobItemProgress($pdo, $dbID, $progress1, $progress2, $progress3);

    // Accounts

    $balHighYield = $dbData['balHighYield'];
    $interest = ($balHighYield * exp($dis_y)) - $balHighYield;
    auditAccount($pdo, $dbID, 3, $interest);

}

function updateUserTheft($pdo) {

    extract($_SESSION['userData']);

    $theftData = getUserTheft($pdo, $dbID);
    if ($theftData['active_theft'] == 1) {
        $theftBegin = strtotime($theftData['start_time']);
        $now = time();

        $distance = $now - $theftBegin;
        if ($theftData['attempted_acc'] == 0 && $distance >= 57600) {
            $calc = calcTheft($pdo, 0, $theftData['attempted_uid']);
            setUserTheftPayout($pdo, $dbID, $calc);
            if ($calc == 0) {
                return 0;
            }
            return 1;
        } else if ($theftData['attempted_acc'] == 1 && $distance >= 72000) {
            $calc = calcTheft($pdo, 1, $theftData['attempted_uid']);
            setUserTheftPayout($pdo, $dbID, $calc);
            if ($calc == 0) {
                return 0;
            }
            return 1;
        } else {
            return -1;
        }
    } else if ($theftData['successful'] == 1 && $theftData['awaiting_collec'] == 1) {
        return 1;
    } else if ($theftData['successful'] == 0 && $theftData['awaiting_collec'] == 1){
        return 0;
    } else {
        return -1;
    }

}

function updateUserBounty($pdo) {

    extract($_SESSION['userData']);
    $bounties = getBounties($pdo);

    $userBounty = getUserHunting($pdo, $dbID);
    $bountyData = $bounties[$userBounty['bounty_id']];

    if ($userBounty['bounty_id'] == 0) {
        return 0;
    } 
    if ($userBounty['awaiting_collec'] == 1 && $userBounty['successful'] == 0) {
        return 1;
    } else if ($userBounty['awaiting_collec'] == 1 && $userBounty['successful'] == 1) {
        if ($userBounty['capture_success'] == 1) {
            return 3;
        } else {
            return 2;
        }
        
    }

    $odds = calcBountyOdds($userBounty, $bountyData);

    $timeAddition = 3600*$odds[2];
    $startTime = strtotime($userBounty['start_time']);
    $now = time();
    $distance = $now-$startTime;

    if ($distance >= $timeAddition) {

        $result = calcBounty($odds[0], $odds[1]);
        if ($result == 0) {

            setUserBounty($pdo, $dbID, 0, 0);
            return 1;
        } else if ($result == 1) {

            setUserBounty($pdo, $dbID, 1, 0);
            return 2;
        } else if ($result == 2) {

            setUserBounty($pdo, $dbID, 1, 1);
            return 3;
        }

    } else {
        return 0;
    }
}