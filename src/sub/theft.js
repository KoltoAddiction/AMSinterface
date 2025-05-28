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
var theftInfo = document.getElementById("theftInfo");
var theftDataString = theftInfo.dataset.json;
var theftData = JSON.parse(theftDataString);

var userSelected = 0;
var accountSelected = -1;
var endTime;
var x = setInterval(function() {
    if (theftData.active_theft == 0) {
        clearInterval(x);
        return;
    }

    var now = new Date().getTime();

    var distance = endTime-now;

    var hours = Math.floor(distance / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    operationTime.innerHTML = "[ " + hours + ":" + minutes + ":" + seconds + " ]";
}, 1);

function updateTheftInfo() {
    var opMenuTitle = document.getElementById("opMenuTitle");
    var baseOdds = document.getElementById("baseOdds");
    var finalOdds = document.getElementById("finalOdds");
    var operationTime = document.getElementById("operationTime");
    var stealButton = document.getElementById("stealButton");

    if(theftData.active_theft == 0) {
        var userRows = document.querySelectorAll(".userRow");
        var checkingAccount = document.getElementById("checkingSelection");
        var offshoreAccount = document.getElementById("offshoreSelection");
        for (var i = 0, len = userRows.length; i < len; i++) {
            if(userRows[i].dataset.selected == "true"){
                userSelected = userRows[i].id.substring(4);
            }
        }
        if(userSelected != 0){
            if(checkingAccount.dataset.selAccount == "true"){
                accountSelected = 0;
                baseOdds.innerHTML = "[ 15% ]";
                finalOdds.innerHTML = "[ 15% ]";
                operationTime.innerHTML = '[ 16:00:00 ]'
                stealButton.addEventListener("click", function(){
                    beginTheft(userSelected, accountSelected);
                })
            } else if(offshoreAccount.dataset.selAccount == "true"){
                accountSelected = 1;
                baseOdds.innerHTML = "[ 60% ]";
                finalOdds.innerHTML = "[ 60% ]";
                operationTime.innerHTML = '[ 20:00:00 ]'
                stealButton.addEventListener("click", function(){
                    beginTheft(userSelected, accountSelected);
                })
            }
        }
    } else {
        opMenuTitle.innerHTML = "current operation";
        stealButton.value = "> cancel operation";
        stealButton.addEventListener("click", function(){
            cancelTheft();
        })
        if(theftData.attempted_acc == 0) {
            baseOdds.innerHTML = "[ 15% ]";
            finalOdds.innerHTML = "[ 15% ]";
            var startTime = new Date(theftData.start_time);
            endTime = startTime.getTime() + 57600000;
        } else if(theftData.attempted_acc == 1) {
            baseOdds.innerHTML = "[ 60% ]";
            finalOdds.innerHTML = "[ 60% ]";
            var startTime = new Date(theftData.start_time);
            endTime = startTime.getTime() + 72000000;
        }
    }
}


document.addEventListener("DOMContentLoaded", () => {
    updateTheftInfo();

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("theftReauth").addEventListener("click", reAuthButton);
    document.getElementById("theftReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("theftLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("theftBackButton").addEventListener("click", backButton);
    
    var userRows = document.querySelectorAll(".userRow");
    for (var i = 0, len = userRows.length; i < len; i++) {
        userRows[i].addEventListener("click", function(){
            playClickSound();
            for (var i = 0, len = userRows.length; i < len; i++) {
                userRows[i].style.backgroundColor = "";
                userRows[i].style.color = "";
                userRows[i].style.textShadow = "";
                userRows[i].dataset.selected = "false";
            }

            this.style.backgroundColor = "#f8fce5";
            this.style.color = "black";
            this.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
            this.dataset.selected = "true";
            updateTheftInfo();
        });
    }

    var checkingAccount = document.getElementById("checkingSelection");
    var offshoreAccount = document.getElementById("offshoreSelection");
    checkingAccount.addEventListener("click", function(){
        playClickSound();
        checkingAccount.style.backgroundColor = "#f8fce5";
        checkingAccount.style.color = "black";
        checkingAccount.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
        checkingAccount.dataset.selAccount = "true";

        offshoreAccount.style.backgroundColor = "";
        offshoreAccount.style.color = "";
        offshoreAccount.style.textShadow = "";
        offshoreAccount.dataset.selected = "false";
        updateTheftInfo();
    });
    offshoreAccount.addEventListener("click", function(){
        playClickSound();
        offshoreAccount.style.backgroundColor = "#f8fce5";
        offshoreAccount.style.color = "black";
        offshoreAccount.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
        offshoreAccount.dataset.selAccount = "true";

        checkingAccount.style.backgroundColor = "";
        checkingAccount.style.color = "";
        checkingAccount.style.textShadow = "";
        checkingAccount.dataset.selAccount = "false";
        updateTheftInfo();
    });

    document.getElementById("claimButton").addEventListener("click", claimTheft);
});

function beginTheft(userSelected, accountSelected) {
    // Define the server endpoint (PHP script)
    const url = "../db.php"; 

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "beginTheft",
        user_selected: userSelected,
        account_selected: accountSelected,
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

function cancelTheft() {
    // Define the server endpoint (PHP script)
    const url = "../db.php"; 

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "cancelTheft",
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

function claimTheft() {
    // Define the server endpoint (PHP script)
    const url = "../db.php"; 

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "claimTheft",
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