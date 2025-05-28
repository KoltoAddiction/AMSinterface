<?php 

setcookie("lastPage", "index");

?>

<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="dist/style.css" rel="stylesheet">
        <link href="dist/index.css" rel="stylesheet">
        <link rel="icon" href="assets/favicon.png">
        <script>
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
        </script>
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='indexScreen'>
            <img src="assets/scanlines.png" id="scan" class="noselect">
            <img src="assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="indexContent">

                <div class="menu" id="indexMenu">
                    <div id="loginbox">
                            <span class="indexh1">[ asylum management service ]</span>
                            <a href="src/init-oauth.php" id="loginbutton">
                                <img id="discordlogo" src="assets/discordlogo.png"></img>
                                <input type="button" id="loginbuttoninput" value="[ login with discord ]"></input>
                            </a>
                    </div>
                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
            
            </div>
        </div>
    </body>
</html>