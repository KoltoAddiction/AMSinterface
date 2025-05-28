var jsonHistoryData = [];
var chartSetUp = false;
var canvas;
var ctx;

var currentItemID;
var currentGearID;
var currentStockID;

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

function switchMenuHighlight(selectedMenu, deselect1, deselect2) {
    selectedMenu.className = "invMenuButtonSelected";
    deselect1.className = "invMenuButton";
    deselect2.className = "invMenuButton";
}

function switchMenu(selectedMenu, deselect1, deselect2) {
    selectedMenu.style.display = "flex";
    deselect1.style.display = "none";
    deselect2.style.display = "none";
}

function viewItem(itemID) {
    let item = document.getElementById(itemID);
    let jsonDataString = item.dataset.json;
    let jsonData = JSON.parse(jsonDataString);
    let noSelItemInfo = document.getElementById("noSelItemInfo");
    let selItemInfo = document.getElementById("selItemInfo");
    noSelItemInfo.style.display = "none";
    selItemInfo.style.display = "flex";

    currentItemID = jsonData.item_id;

    const rarities = ["Common", "Rare", "Epic", "Legendary", "Unique"]
    let itemRarity = parseInt(jsonData.rarity, 10);
    document.getElementById("itemImage").src = "../../assets/items/" + jsonData.item_id + ".png";
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

function equipGear(gearID, slotNumber) {
    // Define the server endpoint (PHP script)
    const url = "../db.php";

    let gearTypes = ["accessory", "weapon", "headwear", "catalyst", "bodywear", "signature"]
    const slotColumn = gearTypes[slotNumber]
    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "equipGear",
        gear_id: gearID,
        slot_column: slotColumn
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
        location.reload();
    })
    .catch(error => {
        console.error("Error:", error);
    });    
}

function viewGear(itemID) {
    let item = document.getElementById(itemID);
    let jsonDataString = item.dataset.json;
    let jsonData = JSON.parse(jsonDataString);
    let noGearSelectedNotice = document.getElementById("noGearSelectedNotice");
    let selectedGearInfo = document.getElementById("selectedGearInfo");
    noGearSelectedNotice.style.display = "none";
    selectedGearInfo.style.display = "flex";

    currentGearID = jsonData.item_id;

    const rarities = ["Common", "Rare", "Epic", "Legendary", "Unique"]
    const gearTypes = ["Accessory", "Weapon", "Headwear", "Catalyst", "Bodywear", "Signature"]
    let itemRarity = parseInt(jsonData.rarity, 10);
    let gearType = parseInt(jsonData.gearType, 10);
    document.getElementById("gearImage").src = "../../assets/items/" + jsonData.item_id + ".png";
    document.getElementById("gearCollection").innerHTML = jsonData.collection + " Collection";
    document.getElementById("gearName").innerHTML = jsonData.name;
    if (parseInt(jsonData.quantity, 10) > 1) {
        document.getElementById("gearAmountOwned").innerHTML = jsonData.quantity;
    } else {
        document.getElementById("gearAmountOwned").innerHTML = "";
    }
    document.getElementById("gearRarity").innerHTML = rarities[itemRarity] + " " + gearTypes[gearType];
    document.getElementById("gearValue").innerHTML = "Approx. Value: $" + jsonData.value;
    document.getElementById("gearDescription").innerHTML = jsonData.description;

    document.getElementById("dmgFlat").innerHTML = jsonData.damage;
    document.getElementById("armFlat").innerHTML = jsonData.armor;
    document.getElementById("resFlat").innerHTML = jsonData.resistance;
    document.getElementById("dmgPer").innerHTML = jsonData.damagePER;
    document.getElementById("armPer").innerHTML = jsonData.armorPER;
    document.getElementById("resPer").innerHTML = jsonData.resistancePER;

    document.getElementById("gearSetName").innerHTML = jsonData.gearSetName + " Set";
    document.getElementById("requiredPieces").innerHTML = "Pieces Required: " + jsonData.piecesRequired;
    document.getElementById("dmgFlatSet").innerHTML = jsonData.setBonusDamage;
    document.getElementById("armFlatSet").innerHTML = jsonData.setBonusArmor;
    document.getElementById("resFlatSet").innerHTML = jsonData.setBonusResistance;
    document.getElementById("dmgPerSet").innerHTML = jsonData.setBonusDamagePER;
    document.getElementById("armPerSet").innerHTML = jsonData.setBonusArmorPER;
    document.getElementById("resPerSet").innerHTML = jsonData.setBonusResistancePER;

    document.getElementById("equipButton").dataset.json = jsonDataString;
    
}

function formatTimestamp(timestamp, range) {
    const date = new Date(timestamp);
    switch (range) {
        case 'day': // Show time only
            return date.toLocaleTimeString([], { month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        case 'week': // Show day and time
            return date.toLocaleDateString([], { month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        case 'month': // Show date
            return date.toLocaleDateString([], { month: 'numeric', day: 'numeric' });
        case 'year': // Show month and day
            return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
        case 'all': // Show month and year
            return date.toLocaleDateString([], { day: 'numeric', month: 'short', year: 'numeric' });
        default:
            return timestamp; // Fallback to original
    }
}

function setupHighDPICanvas(canvas) {
    const dpr = window.devicePixelRatio || 1; // Get device pixel ratio
    const rect = canvas.getBoundingClientRect(); // Get the size of the canvas as displayed
    console.log(rect);

    // Set the actual canvas size to be higher resolution
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;

    // Scale the drawing context to match the new resolution
    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);

    // Set the style dimensions back to the original size
    canvas.style.width = `${rect.width}px`;
    canvas.style.height = `${rect.height}px`;

    console.log(canvas.width, canvas.height); // Check if the canvas size is set correctly

    chartSetUp = true;

    return ctx;
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

function filterStockHistory(stockHistory, range) {
    const now = new Date();
    let startDate;

    switch (range) {
        case 'day':
            startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
            break;
        case 'week':
            startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 7);
            break;
        case 'month':
            startDate = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
            break;
        case 'year':
            startDate = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate());
            break;
        case 'all':
        default:
            startDate = new Date(-8640000000000000); // Earliest possible date
            break;
    }

    return stockHistory.filter(item => new Date(item.recorded_at) >= startDate);
}

function drawStockGraph(stockHistory, selectedRange, ctx) {
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);  // Clear the canvas

    if (stockHistory) {
        const priceData = stockHistory.map(item => ({
            recorded_at: item.recorded_at,
            value: item.value
        }));

        priceData.sort((a, b) => new Date(a.recorded_at) - new Date(b.recorded_at));

        const timestamps = priceData.map(item => item.recorded_at);
        const prices = priceData.map(item => item.value);

        // Adjust padding for each side of the graph
        const leftPadding = 100;  // More padding for left side (y-axis labels)
        const rightPadding = 60; // Less padding on right
        const topPadding = 50;   // Smaller top padding
        const bottomPadding = 80; // Bottom padding for x-axis labels

        const width = ctx.canvas.width / window.devicePixelRatio - leftPadding - rightPadding;
        const height = ctx.canvas.height / window.devicePixelRatio - topPadding - bottomPadding;

        const maxPrice = Math.max(...prices);
        const minPrice = Math.min(...prices);
        const xStep = width / (timestamps.length - 1);
        const yScale = height / (maxPrice - minPrice);

        // Draw axes
        ctx.beginPath();
        ctx.moveTo(leftPadding, topPadding);
        ctx.lineTo(leftPadding, ctx.canvas.height - bottomPadding);
        ctx.lineTo(ctx.canvas.width - rightPadding, ctx.canvas.height - bottomPadding);
        ctx.strokeStyle = '#fce5fa';
        ctx.stroke();

        ctx.font = "24px VT323";

        // Plot points
        ctx.beginPath();
        prices.forEach((price, i) => {
            const x = leftPadding + i * xStep;
            const y = ctx.canvas.height - bottomPadding - (price - minPrice) * yScale;

            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        ctx.strokeStyle = '#fce5fa';
        ctx.stroke();

        // Add labels for the x-axis (timestamps)
        ctx.fillStyle = '#fce5fa';
        ctx.textAlign = 'center';
        timestamps.forEach((timestamp, i) => {
            let x = leftPadding + i * xStep;
            let y = ctx.canvas.height - bottomPadding + 30;
        
            let formattedTimestamp = formatTimestamp(timestamp, selectedRange);
            
            ctx.save();

            // Translate to the point where the text will be drawn
            ctx.translate(x, y);
        
            // Rotate the canvas context slightly (e.g., -45 degrees)
            ctx.rotate(-Math.PI / 4); // -45 degrees in radians
        
            // Draw the text
            ctx.fillText(formattedTimestamp, 0, 0);
        
            // Restore the canvas state to avoid affecting other drawings
            ctx.restore();
        });

        // Add price markers on the y-axis
        ctx.textAlign = 'right';
        for (let price = minPrice; price <= maxPrice; price += (maxPrice - minPrice) / 5) {
            const y = ctx.canvas.height - bottomPadding - (price - minPrice) * yScale;
            ctx.fillText(price.toFixed(2), leftPadding - 10, y + 5); // Adjust for y-axis marker placement
        }
    }
}

function viewStock(stockData, stockHistory, ctx) {

    let stockDataTicker = document.getElementById("stockDataTicker");
    let stockDataName = document.getElementById("stockDataName");
    let stockDataValue = document.getElementById("stockDataValue");
    let stockDataCategory = document.getElementById("stockDataCategory");

    currentStockID = stockData.id;

    stockDataTicker.innerHTML = "(" + stockData.ticker + ")";
    stockDataName.innerHTML = stockData.name;
    stockDataValue.innerHTML = "$" + stockData.current_value;
    stockDataCategory.innerHTML = stockData.category;

    drawStockGraph(stockHistory, "all", ctx);
}

function updateGraph(selectedRange) {
    const filteredData = filterStockHistory(jsonHistoryData, selectedRange); // Assume stockHistory is globally available
    drawStockGraph(filteredData, selectedRange, ctx);
}

document.addEventListener("DOMContentLoaded", () => {

    var buttons = document.querySelectorAll("input[type=button]");
    for (var i = 0, len = buttons.length; i < len; i++) {
        document.querySelectorAll("input[type=button]")[i].addEventListener("click", playClickSound);
        document.querySelectorAll("input[type=button]")[i].addEventListener("mouseover", playHoverSound);
    }
    document.getElementById("inventoryReauth").addEventListener("click", reAuthButton);
    document.getElementById("inventoryReauth").addEventListener("mouseover", playHoverSound);
    document.getElementById("inventoryLogout").addEventListener("mouseover", playHoverSound);
    document.getElementById("inventoryBackButton").addEventListener("click", backButton);

    document.getElementById("sellButton").addEventListener("click", function(){
        let quantityField = document.getElementById("quanToSell");
        var quantity = quantityField.value;
        console.log(currentItemID);

        sellItem(currentItemID, quantity);
    });
    document.getElementById("gearSellButton").addEventListener("click", function(){
        sellItem(currentGearID, 1);
    });
    document.getElementById("stockSellButton").addEventListener("click", function(){
        let quantityField = document.getElementById("stockQuanToSell");
        var quantity = quantityField.value;

        sellStock(currentStockID, quantity);
    });
    
    const itemsButton = document.getElementById("itemsButton");
    const gearButton = document.getElementById("gearButton");
    const stocksButton = document.getElementById("stocksButton");

    const itemsMenu = document.getElementById("itemMenu");
    const gearMenu = document.getElementById("gearMenu");
    const stocksMenu = document.getElementById("stocksMenu");

    var lastInvTab = getCookie("lastInvTab");
    if (lastInvTab == "stocks") {
        switchMenuHighlight(stocksButton, gearButton, itemsButton);
        switchMenu(stocksMenu, gearMenu, itemsMenu);
    } else if (lastInvTab == "gear") {
        switchMenuHighlight(gearButton, itemsButton, stocksButton);
        switchMenu(gearMenu, itemsMenu, stocksMenu);
    } else {
        switchMenuHighlight(itemsButton, gearButton, stocksButton);
        switchMenu(itemsMenu, gearMenu, stocksMenu);
        document.cookie = "lastInvTab=items";
    }

    itemsButton.addEventListener("click", function () {
        switchMenuHighlight(itemsButton, gearButton, stocksButton);
        switchMenu(itemsMenu, gearMenu, stocksMenu);
        document.cookie = "lastInvTab=items";
    });
    gearButton.addEventListener("click", function () {
        switchMenuHighlight(gearButton, itemsButton, stocksButton);
        switchMenu(gearMenu, itemsMenu, stocksMenu);
        document.cookie = "lastInvTab=gear";
    });
    stocksButton.addEventListener("click", function () {
        switchMenuHighlight(stocksButton, gearButton, itemsButton);
        switchMenu(stocksMenu, gearMenu, itemsMenu);
        document.cookie = "lastInvTab=stocks";
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
            this.style.backgroundColor = "#fce5fa";

        });
    }

    var gearIcons = document.querySelectorAll(".gearIcon");
    for (var i = 0, len = gearIcons.length; i < len; i++) {
        document.querySelectorAll(".gearIcon")[i].addEventListener("click", function () {
            playClickSound();
            let allGearIcons = document.querySelectorAll(".gearIcon");
            for (var i = 0, len = allGearIcons.length; i < len; i++) {
                allGearIcons[i].style.borderStyle = "none";
            }
            let noSlotSelectedNotice = document.getElementById("noSlotSelectedNotice");
            let allGearLists = document.querySelectorAll(".gearTypeList");
            for (var i = 0, len = allGearLists.length; i < len; i++) {
                allGearLists[i].style.display = "none";
            }
            let gearList = document.getElementById("gearList");
            this.style.borderStyle = "solid";
            gearList.style.display = "flex";
            noSlotSelectedNotice.style.display = "none";
            
            let findGearList = this.id + "List";
            let selGearList = document.getElementById(findGearList);
            selGearList.style.display = "flex";
        });
    }

    var gearRows = document.querySelectorAll(".gearRow");
    for (var i = 0, len = gearRows.length; i < len; i++) {
        gearRows[i].addEventListener("click", function () {
            playClickSound();
            let gearID = this.id;
            viewGear(this.id);

            for (var i = 0, len = gearRows.length; i < len; i++) {
                gearRows[i].style.backgroundColor = "";
            }
            let allPNGs = document.getElementsByClassName("gearPNG");
            for (var i = 0, len = allPNGs.length; i < len; i++) {
                allPNGs[i].style.filter = "";
            }
            let allNames = document.getElementsByClassName("gearName");
            for (var i = 0, len = allNames.length; i < len; i++) {
                allNames[i].style.color = "";
                allNames[i].style.textShadow = "";
            }
            let allSets = document.getElementsByClassName("gearSet");
            for (var i = 0, len = allSets.length; i < len; i++) {
                allSets[i].style.color = "";
                allSets[i].style.textShadow = "";
            }
            
            let thisPNG = this.getElementsByClassName("gearPNG")[0];
            thisPNG.style.filter = "saturate(944%) brightness(12.7%)";
            let thisName = this.getElementsByClassName("gearName")[0];
            thisName.style.color = "black";
            thisName.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
            let thisSet = this.getElementsByClassName("gearSet")[0];
            thisSet.style.color = "black";
            thisSet.style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
            this.style.backgroundColor = "#fce5fa";
            
        });
    }

    var equipButton = document.getElementById("equipButton");
    equipButton.addEventListener("click", function () {
        let jsonDataString = this.dataset.json;
        let jsonData = JSON.parse(jsonDataString);
        let gearType = parseInt(jsonData.gearType, 10);
        equipGear(jsonData.itemID, gearType);
    });

    var stockRows = document.querySelectorAll(".stockRow");
    for (var i = 0, len = stockRows.length; i < len; i++) {
        stockRows[i].addEventListener("click", function () {
            playClickSound();
            let jsonStockDataString = this.dataset.json;
            let jsonStockData = JSON.parse(jsonStockDataString);
            let jsonHistoryDataString = this.getElementsByClassName("stockValue")[0].dataset.json;
            jsonHistoryData = JSON.parse(jsonHistoryDataString);

            for (var i = 0, len = stockRows.length; i < len; i++) {
                stockRows[i].style.backgroundColor = "";
            }
            let allPNGs = document.getElementsByClassName("stockPNG");
            for (var i = 0, len = allPNGs.length; i < len; i++) {
                allPNGs[i].style.filter = "";
            }
            let allInfo = document.querySelectorAll(".stockInfoRow,.stockValueRow");
            for (var i = 0, len = allInfo.length; i < len; i++) {
                allInfo[i].style.color = "";
                allInfo[i].style.textShadow = "";
            }
            
            let thisPNG = this.getElementsByClassName("stockPNG")[0];
            thisPNG.style.filter = "saturate(944%) brightness(12.7%)";
            let thisInfo = this.querySelectorAll(".stockInfoRow,.stockValueRow");
            for (var i = 0, len = thisInfo.length; i < len; i++) {
                thisInfo[i].style.color = "black";
                thisInfo[i].style.textShadow = "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black";
            }
            this.style.backgroundColor = "#fce5fa";

            let noStockSelectedNotice = document.getElementById("noStockSelectedNotice");
            let stockData = document.getElementById("stockData");
            noStockSelectedNotice.style.display = "none";
            stockData.style.display = "flex";

            if (chartSetUp == false) {
                canvas = document.getElementById('stockChart');
                ctx = setupHighDPICanvas(canvas);
                viewStock(jsonStockData, jsonHistoryData, ctx);
            } else {
                viewStock(jsonStockData, jsonHistoryData, ctx);
            }
            
            
        });
    }

    var radios = document.getElementsByClassName("timelineInput");
    for (var i = 0, len = radios.length; i < len; i++) {
        radios[i].addEventListener("change", function () {

            playClickSound();
            updateGraph(this.value);

        });
    }

})

function sellItem(itemID, quantity) {
        const url = "../db.php";

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "sellItem",
        item_id: itemID,
        quantity: quantity,
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

function sellStock(stockID, quantity) {
        const url = "../db.php";

    // Prepare the data to send
    const data = new URLSearchParams({
        purpose: "sellStock",
        stock_id: stockID,
        quantity: quantity,
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