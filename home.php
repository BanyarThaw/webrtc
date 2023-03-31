<?php
    include 'core/init.php';
    // if user is not logged in
    if(!$userObj->isLoggedIn()) {
        $userObj->redirect('index.php');
    }

    $user = $userObj->userData();
    $userObj->updateSession();

    echo $user->name;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WebRTC</title>
        <style>
            .hidden {
                display: none;
            }
        </style>
        <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
        <script type="text/javascript">
            const conn = new WebSocket('ws://localhost:9000/?token=<?php echo $userObj->sessionID; ?>');
        </script>
    </head>
    <body>
        <!-- Popup Box -->
        <div id="callBox" class="hidden">
            <p>user is calling</p>
            <img id="profileImage" src="">
            <p id="username"></p>
            <a href="#" id="declineBtn">reject user</a>
            <a href="#" id="answerBtn">accept user</a>
        </div>
        <h1>Alert Message</h1>
        <div id="alertBox">
            <div>
                <span id="alertImage"></span>
                <span id="alertName"></span>
                <span id="alertMessage"></span>
            </div>
        </div>
        <image src=<?php echo $user->profileImage; ?>></image>
        <?php
            $userObj->getUsers();
        ?>
        <script src="assets/js/webrtc.js"></script>
        <script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/main.js"></script> 
        <!-- <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script> -->
    </body>
</html>