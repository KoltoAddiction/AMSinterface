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
function setNewJob( newJobId) {
    // Define the server endpoint (PHP script)
    const url = "../db.php";

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "setNewJob",
        job_id: newJobId
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


document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("workReauth").addEventListener("click", reAuthButton);
    document.getElementById("workReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("workLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("workBackButton").addEventListener("click", backButton);

    var offersPopup = document.getElementById("currentOffersPopup");
    var closeOffersButton = document.getElementById("closeCurrentOffers");
    closeOffersButton.addEventListener("click", function(){
        offersPopup.style.display = "none";
    });

    var currentOffersButton = document.getElementById("positionsButton");
    currentOffersButton.addEventListener("click", function() {
        offersPopup.style.display = "block";
    });

    var acceptButtons = document.querySelectorAll(".acceptButton");
    for (var i = 0, len = acceptButtons.length; i < len; i++) {
        acceptButtons[i].addEventListener("click", function() {
            setNewJob(this.id);
        });
    }

});