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
    document.getElementById("leaderboardReauth").addEventListener("click", reAuthButton);
    document.getElementById("leaderboardReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("leaderboardLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("leaderboardBackButton").addEventListener("click", backButton);

});