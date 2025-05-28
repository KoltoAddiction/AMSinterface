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

function abbrNum(number, decPlaces) {
    // 2 decimal places => 100, 3 => 1000, etc
    decPlaces = Math.pow(10, decPlaces);
  
    // Enumerate number abbreviations
    var abbrev = ["k", "m", "b", "t"];
  
    // Go through the array backwards, so we do the largest first
    for (var i = abbrev.length - 1; i >= 0; i--) {
  
      // Convert array index to "1000", "1000000", etc
      var size = Math.pow(10, (i + 1) * 3);
  
      // If the number is bigger or equal do the abbreviation
      if (size <= number) {
        // Here, we multiply by decPlaces, round, and then divide by decPlaces.
        // This gives us nice rounding to a particular decimal place.
        number = Math.round(number * decPlaces / size) / decPlaces;
  
        // Handle special case where we round up to the next abbreviation
        if ((number == 1000) && (i < abbrev.length - 1)) {
          number = 1;
          i++;
        }
  
        // Add the letter for the abbreviation
        number += abbrev[i];
  
        // We are done... stop
        break;
      }
    }
  
    return number;
}

function lockHighYieldAcc() {
    let chooseHighYield = document.getElementById("chooseHighYield");
    let highYieldAccountTitle = document.getElementById("highYieldAccountTitle");
    let HYBalStatement = document.getElementById("HYbalStatement");
    let HYBal = document.getElementById("HYbal");
    let HYTransfer = document.getElementById("highyieldTransferBut");
    chooseHighYield.style.display = "none";
    highYieldAccountTitle.innerHTML = "[ locked ]"
    HYBalStatement.innerHTML = "unlocks at lvl:";
    HYBal.innerHTML = "[ 69 ]";
    HYTransfer.value = "[ locked ]";
    HYTransfer.style.pointerEvents = "none";
}
function lockOffshoreAcc() {
    let chooseOffshore = document.getElementById("chooseOffshore");
    let offshoreAccountTitle = document.getElementById("offshoreAccountTitle");
    let OBalStatement = document.getElementById("ObalStatement");
    let OBal = document.getElementById("Obal");
    let OTransfer = document.getElementById("offshoreTransferBut");
    chooseOffshore.style.display = "none";
    offshoreAccountTitle.innerHTML = "[ locked ]";
    OBalStatement.innerHTML = "unlocks at lvl:";
    OBal.innerHTML = "[ 25 ]";
    OTransfer.value = "[ locked ]";
    OTransfer.style.pointerEvents = "none";
}

var transferringFrom = "";

document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("accountsReauth").addEventListener("click", reAuthButton);
    document.getElementById("accountsReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("accountsLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("accountsBackButton").addEventListener("click", backButton);

    const allowedAccountsSTR = document.getElementById("allowedAccounts").textContent;
    const allowedAccounts = parseInt(allowedAccountsSTR, 10);
    if (allowedAccounts == 1) {
        lockHighYieldAcc();
    } else if (allowedAccounts == 0) {
        lockHighYieldAcc();
        lockOffshoreAcc();
    }

    var transferPopup = document.getElementById("transferPopup");
    var closeTransferButton = document.getElementById("closeTransferPopup");
    closeTransferButton.addEventListener("click", function(){
        transferPopup.style.display = "none";
    });

    var transferSuccess = document.getElementById("refreshNotification");

    const checkingTransfer = document.getElementById("checkingTransferBut");
    const savingsTransfer = document.getElementById("savingsTransferBut");

    var SE = document.getElementById("savingsErrorPopup");
    var closeSE = document.getElementById("closeSE");
    closeSE.addEventListener("click", function(){
        SE.style.display = "none";
    });

    const offshoreTransfer = document.getElementById("offshoreTransferBut");
    const highyieldTransfer = document.getElementById("highyieldTransferBut");

    var HYEP = document.getElementById("highYieldErrorPopup");
    var closeHYEP = document.getElementById("closeHYEP");
    closeHYEP.addEventListener("click", function(){
        HYEP.style.display = "none";
    });
    
    checkingTransfer.addEventListener("click", function(){
        transferPopup.style.display = "block";
        transferringFrom = "balChecking";
        $("#accountsMenu").load(" #accountsMenu > *");
    });
    savingsTransfer.addEventListener("click", function(){
        transferPopup.style.display = "block";
        transferringFrom = "balSavings";
    });
    offshoreTransfer.addEventListener("click", function(){
        transferPopup.style.display = "block";
        transferringFrom = "balOffshore";
    });
    highyieldTransfer.addEventListener("click", function(){
        const lastHYW = document.getElementById("lastHYW").textContent;
        console.log(lastHYW);
        const currentTime = Math.floor(Date.now() / 1000);
        var difference = currentTime-lastHYW;
        console.log(difference);
        if(difference < 604800) {
            HYEP.style.display = "block";
        } else {
            transferPopup.style.display = "block";
            transferringFrom = "balHighYield";
        }
    });

    const submitTransfer = document.getElementById("submitTransfer");
    submitTransfer.addEventListener("click", function() {
        
        var transferAmount = document.getElementById("transferAmount").value;
        var transferNumber = parseInt(transferAmount, 10);
        if(isNaN(transferNumber)) {
            alert("Invalid input! Please enter an integer.");
        } else {
            var transferTarget = document.querySelector('input[name="accountChosen"]:checked').value;
            if(transferTarget === "balSavings") {
                var savingsBalance = parseInt(document.getElementById("balSavings").textContent, 10);
                var newSavings = savingsBalance + transferNumber;
                if(newSavings > 100000000) {
                    SE.style.display = "block";
                } else {
                    transferPopup.style.display = "none";
                    console.log("Sending information to database...");
                    transferFunds(transferringFrom, transferTarget, transferNumber);
                    transferSuccess.style.display = "block";
                }
            } else {
                transferPopup.style.display = "none";
                console.log("Sending information to database...");
                transferFunds(transferringFrom, transferTarget, transferNumber);
            }

        }
    });
});

function transferFunds(accountFrom, accountTo, fundsAmount) {
    // Define the server endpoint (PHP script)
    const url = "../db.php";

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "transferFunds",
        table_id: "users",
        account_from: accountFrom,
        account_to: accountTo,
        funds_amount: fundsAmount,
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