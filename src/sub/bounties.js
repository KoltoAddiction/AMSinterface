function playClickSound() {
    var clickSound = document.getElementById("clicksound");
    clickSound.currentTime = 0;
    clickSound.play();
}
function playHoverSound() {
    var hoverSound = document.getElementById("hoversound");
    hoverSound.currentTime = 0;
    hoverSound.play();
}

function scalePage() {
const designWidth = 1920;
const designHeight = 966;
const screen = document.getElementsByClassName("screen")[0];

let scaleX = window.innerWidth / designWidth;
let scaleY = window.innerHeight / designHeight;
let scale = Math.min(scaleX, scaleY);

screen.style.transform = `translate(-50%, -50%) scale(${scale})`;
}

window.addEventListener('resize', scalePage);
window.addEventListener('load', scalePage);

function backButton() {
    window.location.href = "../dashboard.php"
}
function reAuthButton() {
    window.location.href = "../init-oauth.php";
}
var jsonData;

function selectBounty(selection) {
    var bountyRows = document.querySelectorAll(".bountyRow");
    for (var i = 0, len = bountyRows.length; i < len; i++) {
        bountyRows[i].style.color = "#fce5e5";
        bountyRows[i].style.backgroundColor = "";
        bountyRows[i].style.textShadow = "1px 1px 2px #fcbaba, 0 0 1em #fcbaba, 0 0 0.2em #fcbaba";
    }
    selection.style.color = "black";
    selection.style.backgroundColor = "#fce5e5";
    selection.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";

    document.getElementById("noneSelNotice").style.display = "none";
    document.getElementById("selBountyInfo").style.display = "flex";

    const difficultyArray = ["Amateur", "Experienced", "Professional", "Mastery", "Unique", "Maximum"];
    const typeArray = ["Elimination", "Flexible", "Retrieval"];
    const payoutArray = ["Cash", "Mixed"];

    var bName = document.getElementById("selBountyName");
    var bTag = document.getElementById("selBountyTag");
    var bType = document.getElementById("selBountyType");
    var bDiff = document.getElementById("selBountyDiff");
    var bPayType = document.getElementById("selBountyPayType");
    var bCash = document.getElementById("selBountyCash");
    var bItem = document.getElementById("selBountyItem");
    var bItemQuan = document.getElementById("selBountyItemQuan");
    var bDesc = document.getElementById("selBountyDesc");

    var bDmg = document.getElementById("selBountyDmg");
    var bArm = document.getElementById("selBountyArm");
    var bRes = document.getElementById("selBountyRes");
    var bBO = document.getElementById("selBountyBO");
    var bBT = document.getElementById("selBountyBT");
    var bBRO = document.getElementById("selBountyBRO");


    var jsonDataString = selection.dataset.json;
    jsonData = JSON.parse(jsonDataString);
    let jsonType = parseInt(jsonData.type, 10);
    let jsonDiff = parseInt(jsonData.difficulty, 10);
    let jsonPayout = parseInt(jsonData.reward_type, 10);

    var jsonItemDataString = selection.dataset.itemjson;
    var jsonItemData = JSON.parse(jsonItemDataString);
    if (jsonItemData.id != "0") {
        bItem.innerHTML = "[ " + jsonItemData.name + " ]";
        bItemQuan.innerHTML = "[ " + jsonData.item_quantity + " ]";

    } else {
        bItem.innerHTML = "[ N/A ]";
        bItemQuan.innerHTML = "[ N/A ]";

    }

    if (jsonType == 1) {
        bBRO.innerHTML = "[ " + jsonData.capture_rate + "% ]";
    } else {
        bBRO.innerHTML = "[ N/A ]";
    }
    
    bName.innerHTML = "[ " + jsonData.name + " ]";
    bTag.innerHTML = "Callsign: " + jsonData.tag;
    bType.innerHTML = "MO: " + typeArray[jsonType];
    bDiff.innerHTML = difficultyArray[jsonDiff] + " Bounty";
    bPayType.innerHTML = payoutArray[jsonPayout] + " Payout";
    bCash.innerHTML = "[ $" + jsonData.reward_cash + " ]";
    bDesc.innerHTML = jsonData.description;

    bDmg.innerHTML = "dmg: [ "+ jsonData.damage + " ]";
    bArm.innerHTML = "arm: [ "+ jsonData.armor + " ]";
    bRes.innerHTML = "res: [ "+ jsonData.resistance + " ]";
    bBO.innerHTML = "[ " + jsonData.rate + "% ]";
    bBT.innerHTML = "[ " + jsonData.time + ":00:00 ]";
    

    calculateUserRates(jsonData, jsonType);
    document.getElementById("huntButton").addEventListener("click", startBounty);
}

function displayCurrentBounty(selection) {
    document.getElementById("selBountyInfo").style.display = "flex";

    const difficultyArray = ["Amateur", "Experienced", "Professional", "Mastery", "Unique", "Maximum"];
    const typeArray = ["Elimination", "Flexible", "Retrieval"];
    const payoutArray = ["Cash", "Mixed"];

    var bName = document.getElementById("selBountyName");
    var bTag = document.getElementById("selBountyTag");
    var bType = document.getElementById("selBountyType");
    var bDiff = document.getElementById("selBountyDiff");
    var bPayType = document.getElementById("selBountyPayType");
    var bCash = document.getElementById("selBountyCash");
    var bItem = document.getElementById("selBountyItem");
    var bItemQuan = document.getElementById("selBountyItemQuan");
    var bDesc = document.getElementById("selBountyDesc");


    var jsonDataString = selection.dataset.json;
    jsonData = JSON.parse(jsonDataString);
    let jsonType = parseInt(jsonData.type, 10);
    let jsonDiff = parseInt(jsonData.difficulty, 10);
    let jsonPayout = parseInt(jsonData.reward_type, 10);

    var jsonItemDataString = selection.dataset.itemjson;
    var jsonItemData = JSON.parse(jsonItemDataString);
    if (jsonItemData.id != "0") {
        bItem.innerHTML = "[ " + jsonItemData.name + " ]";
        bItemQuan.innerHTML = "[ " + jsonData.item_quantity + " ]";

    } else {
        bItem.innerHTML = "[ N/A ]";
        bItemQuan.innerHTML = "[ N/A ]";

    }

    bName.innerHTML = "[ " + jsonData.name + " ]";
    bTag.innerHTML = "Callsign: " + jsonData.tag;
    bType.innerHTML = "MO: " + typeArray[jsonType];
    bDiff.innerHTML = difficultyArray[jsonDiff] + " Bounty";
    bPayType.innerHTML = payoutArray[jsonPayout] + " Payout";
    bCash.innerHTML = "[ $" + jsonData.reward_cash + " ]";
    bDesc.innerHTML = jsonData.description;
    
    var cancelMenu = document.getElementById("cancelMenu");

    var userDamage = cancelMenu.dataset.dmg;
    var userArmor = cancelMenu.dataset.arm;
    var userResistance = cancelMenu.dataset.res;

    var startDate = new Date(cancelMenu.dataset.start_time);
    var startTime = startDate.getTime();

    if((((userDamage/jsonData.damage) + (userArmor/jsonData.armor) + (userResistance/jsonData.resistance))/3) > 1) {
        var finalTime = startTime + 3600000*(Math.floor(jsonData.time - ((((userDamage/jsonData.damage) + (userArmor/jsonData.armor) + (userResistance/jsonData.resistance))/12)*jsonData.time)));
    } else {
        var finalTime = startTime + 3600000*(Math.floor(jsonData.time + (jsonData.time - ((((userDamage/jsonData.damage) + (userArmor/jsonData.armor) + (userResistance/jsonData.resistance))/12)*jsonData.time))));
    }

    var operationTimer = document.getElementById("remainingTimer");

    var x = setInterval(function() {
    if (selBountyInfoTag == "selected") {
        clearInterval(x);
        return;
    }

    var now = new Date().getTime();

    var distance = finalTime-now;

    var hours = Math.floor(distance / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    operationTimer.innerHTML = "[ " + hours + ":" + minutes + ":" + seconds + " ]";
}, 1);
}

function calculateUserRates(statsData, bountyType) {
    var bFO = document.getElementById("selBountyFO");
    var bFT = document.getElementById("selBountyFT");
    var bFRO = document.getElementById("selBountyFRO");

    var userDamage = document.getElementById("dmgTotal").innerHTML;
    var userArmor = document.getElementById("armTotal").innerHTML;
    var userResistance = document.getElementById("resTotal").innerHTML;

    let dmgOdds = (userDamage/statsData.damage)

    var finalOdds = Math.floor((((userDamage/statsData.damage) + (userArmor/statsData.armor) + (userResistance/statsData.resistance))/3)*statsData.rate);
    var finalROdds = Math.floor((((userDamage/statsData.damage) + (userArmor/statsData.armor) + (userResistance/statsData.resistance))/3)*statsData.capture_rate);
    
    if((((userDamage/statsData.damage) + (userArmor/statsData.armor) + (userResistance/statsData.resistance))/3) > 1) {
        var finalTime = Math.floor(statsData.time - ((((userDamage/statsData.damage) + (userArmor/statsData.armor) + (userResistance/statsData.resistance))/12)*statsData.time));
    } else {
        var finalTime = Math.floor(statsData.time + (statsData.time - ((((userDamage/statsData.damage) + (userArmor/statsData.armor) + (userResistance/statsData.resistance))/12)*statsData.time)));
    }
    if(finalTime == 0){
        finalTime == 1;
    }


    bFO.innerHTML = "[ " + finalOdds + "% ]";
    bFT.innerHTML = "[ " + finalTime + ":00:00 ]";
    
    if (bountyType == 1){
        bFRO.innerHTML = "[ " + finalROdds + "% ]";
    } else {
        bFRO.innerHTML = "[ N/A ]";
    }
}

function startBounty() {
    
    // Define the server endpoint (PHP script)
    const url = "../db.php";

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "startBounty",
        bounty_id: jsonData.id,
    });

    // Send the AJAX request using Fetch API
    fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: data.toString(),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server error: ${response.status}`);
        }
        return response.text();
    })
    .then(result => {
        console.log("Server response:", result);
        window.location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
    });
}

function cancelBounty() {

        // Define the server endpoint (PHP script)
        const url = "../db.php";

        // Prepare the data to send
        const data = new URLSearchParams({
            purpose: "cancelBounty",
        });
    
        // Send the AJAX request using Fetch API
        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: data.toString(),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            return response.text();
        })
        .then(result => {
            console.log("Server response:", result);
            window.location.reload();
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function claimBounty() {
    // Define the server endpoint (PHP script)
    const url = "../db.php";

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "claimBounty",
    });

    // Send the AJAX request using Fetch API
    fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: data.toString(),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Server error: ${response.status}`);
        }
        return response.text();
    })
    .then(result => {
        console.log("Server response:", result);
        window.location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
    });
}

var selBountyInfoTag;

document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("bountiesReauth").addEventListener("click", reAuthButton);
    document.getElementById("bountiesReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("bountiesLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("bountiesBackButton").addEventListener("click", backButton);

    selBountyInfoTag = document.getElementById("selBountyInfo").dataset.tag;

    if (selBountyInfoTag == "selected") {

        var bountyRows = document.querySelectorAll(".bountyRow");
        for (var i = 0, len = bountyRows.length; i < len; i++) {
            bountyRows[i].addEventListener("click", function(){
                selectBounty(this);
            })
        }
    } else if (selBountyInfoTag == "current") {

        document.getElementById("startMenu").style.display = "none";
        document.getElementById("cancelMenu").style.display = "flex";

        var selBountyInfo = document.getElementById("selBountyInfo");
        displayCurrentBounty(selBountyInfo);
    }


    document.getElementById("claimButton").addEventListener("click", claimBounty);
    document.getElementById("cancelButton").addEventListener("click", cancelBounty);
});