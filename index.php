<?php
    include 'core/init.php';
    //if user is already logged in
    if($userObj->isLoggedIn()) {
        $userObj->redirect('home.php');
    }

    if($_SERVER['REQUEST_METHOD'] === "POST") {
        if(isset($_POST)) {
            $email = trim(stripcslashes(htmlentities($_POST['email'])));
            $password = $_POST['password'];

            if(!empty($email) && !empty($password)) {
                // validate
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid Email format!";
                }
                else {
                    if($user = $userObj->emailExist($email)) {
                        if($user = $userObj->emailExist($email)) {
                            if(password_verify($password, $user->password)) {
                                // login user in
                                //session_id_regenerate();
                                session_regenerate_id();
                                // login the user
                                $_SESSION['userID'] = $user->userID;
                                // redirect the user
                                $userObj->redirect('home.php');
                            } else {
                                $error = "Incorrect email or password";
                            }
                        }
                    }
                }
            } else {
                // display error
                $error = "Please enter your email and password to login";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WebRTC</title>
    </head>
    <body>
        <form action="index.php" method="post">
            <p style="color: red;">
                <?php
                    if(isset($error)) {
                        echo $error;
                    }
                ?>
            </p>

            <label>Email</label>
            <input type="email" name="email" required>
            <br>
            <label>Password</label>
            <input type="password" name="password" required>
            <br>
            <input type="submit" value="Submit">
        </form>
    </body>
</html>

<?php
    echo 'hello world';
?>

<script>
    var conn = new WebSocket('ws://localhost:9000');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
    };
</script>