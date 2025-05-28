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


document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("exampleReauth").addEventListener("click", reAuthButton);
    document.getElementById("exampleReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("exampleLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("exampleBackButton").addEventListener("click", backButton);

});