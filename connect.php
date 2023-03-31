<?php
    include 'core/init.php';
    // if user is not logged in
    if(!$userObj->isLoggedIn()) {
        $userObj->redirect('index.php');
    }
    
    $userObj->updateSession();
    if(isset($_GET['username']) && !empty($_GET['username'])) {
        $profileData = $userObj->getUserByUsername($_GET['username']);
        $user = $userObj->userData();

        //var_dump($user->sessionID);
        // var_dump($userObj->getUserBySession($user->sessionID));
        if(!$profileData) {
            $userObj->redirect('home.php');
        } else if ($profileData->username === $user->username) {
            $userObj->redirect('home.php');
        }
    }

    echo '<img src="'.BASE_URL.$user->profileImage.'">';
    echo "username is :";
    //$name = urldecode($_GET['username']);
    //echo $name;
    echo $user->name;

    echo "<br><br><br><br><br>";
    echo "User List";
    echo "<br>";
    echo "====================";
    echo "<br>";
    $userObj->getUsers();

    echo "<br><br><br><br><br><br><br><br>";
    echo '<img src="'.BASE_URL.$profileData->profileImage.'">';
    /* profile section */
    echo "<h2>";
        echo $profileData->name;
    echo "</h2>";
    echo "<p>";
        echo "Do you want to make a Call?";
    echo "</p>";
    echo "<button id='callBtn' data-user='$profileData->userID'>Call</button>";

    /* video call */
    echo "<div id='video'>";
        echo "<video id='remoteVideo' width='320' height='240' autoplay>";
            //echo "<source src='' type='video/mp4'>";
        echo "</video>";
        echo "<video id='localVideo' width='320' height='240' autoplay>";
            // echo "<source src='' type='video/mp4'>";
        echo "</video>";
        echo "<video id='testing' width='320' height='240' controls>";
            echo "<source src='videos/video.mp4' type='video/mp4'>";
        echo "</video>";
        echo "<div>";
            echo "<div>";
                echo "<span id='callTimer'></span>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
    echo "<button id='hangupBtn'>";
        echo "hangup";
    echo "</button>";
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
    <script type="text/javascript">
        const conn = new WebSocket('ws://localhost:9000/?token=<?php echo $userObj->sessionID; ?>');
    </script>
</head>
<body>
    <p>Hello, WebRTC</p>
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
</body>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.7.0/timer.jquery.js"></script> -->
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/timer.jquery.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/js/main.js"></script> 
<script src="assets/js/webrtc.js"></script>
<!-- <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script> -->
</html>