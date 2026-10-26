<?php
session_start();

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';

$error = '';

if (isset($_POST['who']) && isset($_POST['pass'])) {

    $who = trim($_POST['who']);
    $password = $_POST['pass'];

    if ($who === '' || $password === '') {
        $error = 'Email and password are required';
    } elseif (strpos($who, '@') === false) {
        $error = 'Email must have an at-sign (@)';
    } else {

        $check = hash('md5', $salt . $password);

        if ($check === $stored_hash) {

            error_log("Login success " . $who);

            $_SESSION['who'] = $who;

            header("Location: autos.php?name=" . urlencode($who));
            exit();

        } else {

            error_log("Login fail " . $who . " " . $check);

            $error = 'Incorrect password';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>361c7fa8 - MEET VANALIYA - Autos Database</title>
</head>
<body>

<h1>Login to Autos Database</h1>

<?php
if ($error !== '') {
    echo '<p style="color:red;">'
        . htmlentities($error)
        . '</p>';
}
?>

<form method="POST">

    <p>
        Email:
        <input type="text" name="who">
    </p>

    <p>
        Password:
        <input type="password" name="pass">
    </p>

    <p>
        <input type="submit" value="Log In">
        <a href="index.php">Cancel</a>
    </p>

</form>

</body>
</html>