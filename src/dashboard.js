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

function workClick() {
    window.location.href = "sub/work.php"
}
function accountsClick() {
    window.location.href = "sub/accounts.php";
}
function harvestClick() {
    window.location.href = "sub/harvest.php";
}
function theftClick() {
    window.location.href = "sub/theft.php";
}
function bountiesClick() {
    window.location.href = "sub/bounties.php";
}
function inventoryClick() {
    window.location.href = "sub/inventory.php";
}
function marketClick() {
    window.location.href = "sub/market.php";
}
function blackmarketClick() {
    var nowDate = new Date();

    if(nowDate.getDay() === 1){
    window.location.href = "sub/blackmarket.php";
    } else {
        displayNotBMYet();
    }
}
function leaderboardClick() {
    window.location.href = "sub/leaderboard.php";
}
function reAuthButton() {
    window.location.href = "init-oauth.php";
}
document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseleave", unHover);
    }
    document.getElementById("dashboardReauth").addEventListener("click", reAuthButton);
    document.getElementById("dashboardReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("dashboardLogout").addEventListener("mouseover", playHoverSound);

    document.getElementById("workButton").addEventListener("mouseover", workHover);
    document.getElementById("workButton").addEventListener("click", workClick);

    document.getElementById("accountsButton").addEventListener("mouseover", accountsHover);
    document.getElementById("accountsButton").addEventListener("click", accountsClick);

    document.getElementById("harvestButton").addEventListener("mouseover", harvestHover);
    document.getElementById("harvestButton").addEventListener("click", harvestClick);

    document.getElementById("theftButton").addEventListener("mouseover", theftHover);
    document.getElementById("theftButton").addEventListener("click", theftClick);

    document.getElementById("bountiesButton").addEventListener("mouseover", bountiesHover);
    document.getElementById("bountiesButton").addEventListener("click", bountiesClick);

    document.getElementById("grandHeistButton").addEventListener("mouseover", grandHeistHover);
    document.getElementById("grandHeistButton").addEventListener("click", displayComingSoon);

    document.getElementById("inventoryButton").addEventListener("mouseover", inventoryHover);
    document.getElementById("inventoryButton").addEventListener("click", inventoryClick);

    document.getElementById("marketButton").addEventListener("mouseover", marketHover);
    document.getElementById("marketButton").addEventListener("click", marketClick)

    document.getElementById("blackMarketButton").addEventListener("mouseover", blackMarketHover);
    document.getElementById("blackMarketButton").addEventListener("click", blackmarketClick);

    document.getElementById("leaderboardButton").addEventListener("mouseover", leaderboardHover);
    document.getElementById("leaderboardButton").addEventListener("click", leaderboardClick)

    document.getElementById("prestigeButton").addEventListener("mouseover", prestigeHover);
    document.getElementById("prestigeButton").addEventListener("click", displayComingSoon);

    document.getElementById("closeCS").addEventListener("click", closeCSPopup);
    document.getElementById("closeBMY").addEventListener("click", closeBMYPopup);

})




function workHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/work.png";
}
function accountsHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/accounts.png";
}
function harvestHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/harvest.png";
}
function theftHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/theft.png";
}
function bountiesHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/bounties.png";
}
function grandHeistHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/grandHeist.png";
}
function inventoryHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/inventory.png";
}
function marketHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/market.png";
}
function blackMarketHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/blackMarket.png";
}
function leaderboardHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/leaderboard.png";
}
function prestigeHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "../assets/ascii/prestige.png";
}
function unHover() {
    var asciiDisplay = document.getElementById("asciiDisplay");
    asciiDisplay.src = "";
}

var hours = 0;
var minutes = 0;
var seconds = 0;

function getNextMonday() {
    const now = new Date();
    const today = new Date(now);
    today.setMilliseconds(0);
    today.setSeconds(0);
    today.setMinutes(0);
    today.setHours(0);
  
    const nextMonday = new Date(today);
  
    do {
      nextMonday.setDate(nextMonday.getDate() + 1); // Adding 1 day
    } while (nextMonday.getDay() !== 1)
  
    return nextMonday;
  }

var nextMonday = getNextMonday();

var x = setInterval(function() {
    var nowDate = new Date();

    if(nowDate.getDay() === 1){
        document.getElementById("nextBlackMarket").innerHTML = "black market: [ now ]"
    } else {
        var now = new Date().getTime();
    
        var distance = nextMonday-now;

        hours = Math.floor(distance / (1000 * 60 * 60));
        minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("nextBlackMarket").innerHTML = "black market: [ " + hours + ":" + minutes + ":" + seconds + " ]";
    }
}, 1);

function displayComingSoon() {
    var popup = document.getElementById("comingSoonPopup")
    popup.style.display = "block";
}
function closeCSPopup() {
    playClickSound();
    var popup = document.getElementById("comingSoonPopup")
    popup.style.display = "none";
}
function displayNotBMYet() {
    var popup = document.getElementById("notBMYet")
    popup.style.display = "block";
}
function closeBMYPopup() {
    playClickSound();
    var popup = document.getElementById("notBMYet")
    popup.style.display = "none";
}