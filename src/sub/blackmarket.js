var currentItemID;
var currentGearID;

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

function switchMenuHighlight(selectedMenu, deselect1) {
    selectedMenu.className = "invMenuButtonSelected";
    deselect1.className = "invMenuButton";
}

function switchMenu(selectedMenu, deselect1) {
    selectedMenu.style.display = "flex";
    deselect1.style.display = "none";
}

function viewItem(itemID) {
    let item = document.getElementById(itemID);
    let jsonDataString = item.dataset.json;
    let jsonData = JSON.parse(jsonDataString);
    let noSelItemInfo = document.getElementById("noSelItemInfo");
    let selItemInfo = document.getElementById("selItemInfo");
    noSelItemInfo.style.display = "none";
    selItemInfo.style.display = "flex";
    currentItemID = jsonData.id;

    const rarities = ["Common", "Rare", "Epic", "Legendary", "Unique"]
    let itemRarity = parseInt(jsonData.rarity, 10);
    document.getElementById("itemImage").src = "../../assets/items/" + jsonData.id + ".png";
    document.getElementById("itemCollection").innerHTML = jsonData.collection + " Collection";
    document.getElementById("itemName").innerHTML = jsonData.name;
    if (parseInt(jsonData.quantity, 10) > 1) {
        document.getElementById("amountOwned").innerHTML = jsonData.quantity;
    } else {
        document.getElementById("amountOwned").innerHTML = "";
    }
    document.getElementById("itemRarity").innerHTML = rarities[itemRarity] + " Item";
    document.getElementById("itemValue").innerHTML = "Approx. Value: $" + jsonData.value;
    document.getElementById("itemDescription").innerHTML = jsonData.description;
    
}

function viewGear(gearID) {
    let item = document.getElementById(gearID);
    let jsonDataString = item.dataset.json;
    let jsonData = JSON.parse(jsonDataString);
    let noSelItemInfo = document.getElementById("noSelGearInfo");
    let selItemInfo = document.getElementById("selGearInfo");
    noSelItemInfo.style.display = "none";
    selItemInfo.style.display = "flex";
    currentGearID = jsonData.id;

    const rarities = ["Common", "Rare", "Epic", "Legendary", "Unique"]
    let itemRarity = parseInt(jsonData.rarity, 10);
    document.getElementById("gearImage").src = "../../assets/items/" + jsonData.id + ".png";
    document.getElementById("gearCollection").innerHTML = jsonData.collection + " Collection";
    document.getElementById("gearName").innerHTML = jsonData.name;
    if (parseInt(jsonData.quantity, 10) > 1) {
        document.getElementById("amountOwned").innerHTML = jsonData.quantity;
    } else {
        document.getElementById("amountOwned").innerHTML = "";
    }
    document.getElementById("gearRarity").innerHTML = rarities[itemRarity] + " Item";
    document.getElementById("gearValue").innerHTML = "Approx. Value: $" + jsonData.value;
    document.getElementById("gearDescription").innerHTML = jsonData.description;
    
}

function getCookie(cname) {
    let name = cname + '=';
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("blackmarketReauth").addEventListener("click", reAuthButton);
    document.getElementById("blackmarketReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("blackmarketLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("blackmarketBackButton").addEventListener("click", backButton);
    
    const itemsButton = document.getElementById("itemsButton");
    const gearButton = document.getElementById("gearButton");

    const itemsMenu = document.getElementById("itemMenu");
    const gearMenu = document.getElementById("gearMenu");

    var lastBlackmarketTab = getCookie("lastBlackmarketTab");
    if (lastBlackmarketTab == "gear") {
        switchMenuHighlight(gearButton, itemsButton);
        switchMenu(gearMenu, itemsMenu);
    } else {
        switchMenuHighlight(itemsButton, gearButton);
        switchMenu(itemsMenu, gearMenu);
        document.cookie = "lastBlackmarketTab=items";
    }

    itemsButton.addEventListener("click", function () {
        switchMenuHighlight(itemsButton, gearButton);
        switchMenu(itemsMenu, gearMenu);
        document.cookie = "lastBlackmarketTab=items";
    });
    gearButton.addEventListener("click", function () {
        switchMenuHighlight(gearButton, itemsButton);
        switchMenu(gearMenu, itemsMenu);
        document.cookie = "lastBlackmarketTab=gear";
    });

    var items = document.querySelectorAll(".itemIcon");
    for (var i = 0, len = items.length; i < len; i++) {
        document.querySelectorAll(".itemIcon")[i].addEventListener("click", function () {
            let itemID = this.id;
            playClickSound();
            viewItem(itemID);
            for (var i = 0, len = items.length; i < len; i++) {
                items[i].style.backgroundColor = "";
            }
            let allPNGs = document.getElementsByClassName("itemPNG");
            for (var i = 0, len = allPNGs.length; i < len; i++) {
                allPNGs[i].style.filter = "";
            }
            let allQuantities = document.getElementsByClassName("itemQuantity");
            for (var i = 0, len = allQuantities.length; i < len; i++) {
                allQuantities[i].style.color = "";
                allQuantities[i].style.textShadow = "";
            }
            let thisPNG = this.getElementsByClassName("itemPNG")[0];
            if (this.getElementsByClassName("itemQuantity").length > 0) {
                let thisQuantity = this.getElementsByClassName("itemQuantity")[0];
                thisQuantity.style.color = "black";
                thisQuantity.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
            }  
            thisPNG.style.filter = "saturate(944%) brightness(12.7%)";
            this.style.backgroundColor = "#fceee5";

        });
    }

    var gears = document.querySelectorAll(".gearIcon");
    for (var i = 0, len = gears.length; i < len; i++) {
        document.querySelectorAll(".gearIcon")[i].addEventListener("click", function () {
            let gearID = this.id;
            playClickSound();
            viewGear(gearID);
            for (var i = 0, len = gears.length; i < len; i++) {
                gears[i].style.backgroundColor = "";
            }
            let allPNGs = document.getElementsByClassName("gearPNG");
            for (var i = 0, len = allPNGs.length; i < len; i++) {
                allPNGs[i].style.filter = "";
            }
            let allQuantities = document.getElementsByClassName("gearQuantity");
            for (var i = 0, len = allQuantities.length; i < len; i++) {
                allQuantities[i].style.color = "";
                allQuantities[i].style.textShadow = "";
            }
            let thisPNG = this.getElementsByClassName("gearPNG")[0];
            if (this.getElementsByClassName("gearQuantity").length > 0) {
                let thisQuantity = this.getElementsByClassName("gearQuantity")[0];
                thisQuantity.style.color = "black";
                thisQuantity.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
            }  
            thisPNG.style.filter = "saturate(944%) brightness(12.7%)";
            this.style.backgroundColor = "#ffffff";

        });
    }

    document.getElementById("purchaseButton").addEventListener("click", function(){

        let quantityField = document.getElementById("quanToBuy");
        var quantity = quantityField.value;

        purchaseItem(currentItemID, quantity);
    });
    document.getElementById("gearPurchaseButton").addEventListener("click", function(){

        let quantityField = document.getElementById("gearQuanToBuy");
        var quantity = quantityField.value;

        purchaseItem(currentGearID, quantity);
    });

})

function purchaseItem(itemID, quantity) {
        const url = "../db.php";

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "purchaseItem",
        item_id: itemID,
        quantity: quantity,
        account: 2,
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