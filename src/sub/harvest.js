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

function backButton() {
    window.location.href = "../dashboard.php"
}
function reAuthButton() {
    window.location.href = "../init-oauth.php";
}


document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("harvestReauth").addEventListener("click", reAuthButton);
    document.getElementById("harvestReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("harvestLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("harvestBackButton").addEventListener("click", backButton);

    var collectButtons = document.querySelectorAll(".collectButton");
    for(var i = 0, len = collectButtons.length; i < len; i++) {
        let thisHarvestID = document.querySelectorAll(".collectButton")[i].id.substring(7);
        document.querySelectorAll(".collectButton")[i].addEventListener("click", function(){
            collectHarvest(thisHarvestID);
        })
    }

    var purchaseButtons = document.querySelectorAll(".purchaseButton");
    for(var i = 0, len = purchaseButtons.length; i < len; i++) {
        let thisHarvestID = document.querySelectorAll(".purchaseButton")[i].id.substring(8);
        document.querySelectorAll(".purchaseButton")[i].addEventListener("click", function(){
            console.log(thisHarvestID);
            purchaseHarvest(thisHarvestID);
        })
    }

});

function collectHarvest(harvestID) {
    // Define the server endpoint (PHP script)
    const url = "../db.php"; 

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "collectHarvest",
        harvest_id: harvestID,
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

function purchaseHarvest(harvestID) {
    // Define the server endpoint (PHP script)
    const url = "../db.php"; 

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "purchaseHarvest",
        harvest_id: harvestID,
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