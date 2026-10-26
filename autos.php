<?php
session_start();

if (!isset($_GET['name']) || $_GET['name'] === '') {
    die("Name parameter missing");
}

$name = $_GET['name'];

if (!isset($_SESSION['who']) || $_SESSION['who'] !== $name) {
    die("Please log in first");
}

require_once 'pdo.php';

$message = '';
$error = '';

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

if (isset($_POST['make']) &&
    isset($_POST['year']) &&
    isset($_POST['mileage'])) {

    $make = trim($_POST['make']);
    $year = trim($_POST['year']);
    $mileage = trim($_POST['mileage']);

    if ($make === '') {
        $error = 'Make is required';
    } elseif (
        !is_numeric($year) ||
        !is_numeric($mileage) ||
        filter_var($year, FILTER_VALIDATE_INT) === false ||
        filter_var($mileage, FILTER_VALIDATE_INT) === false
    ) {
        $error = 'Mileage and year must be numeric';
    } else {

        $stmt = $pdo->prepare(
            'INSERT INTO autos (make, year, mileage)
             VALUES (:mk, :yr, :mi)'
        );

        $stmt->execute([
            ':mk' => $make,
            ':yr' => $year,
            ':mi' => $mileage
        ]);

        $message = 'Record inserted';
    }
}

$stmt = $pdo->query(
    'SELECT auto_id, make, year, mileage
     FROM autos
     ORDER BY auto_id'
);

$autos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>36c17fa8 - MEET VANALIYA - Autos Database</title>
</head>
<body>

<h1>Welcome to the Autos Database</h1>

<p>
    Welcome:
    <?= htmlentities($name) ?>
</p>

<?php if ($error !== ''): ?>
    <p style="color:red;">
        <?= htmlentities($error) ?>
    </p>
<?php endif; ?>

<?php if ($message !== ''): ?>
    <p style="color:green;">
        <?= htmlentities($message) ?>
    </p>
<?php endif; ?>

<h2>Add Automobile</h2>

<form method="POST"
      action="autos.php?name=<?= urlencode($name) ?>">

    <p>
        Make:
        <input type="text" name="make">
    </p>

    <p>
        Year:
        <input type="text" name="year">
    </p>

    <p>
        Mileage:
        <input type="text" name="mileage">
    </p>

    <p>
        <input type="submit" value="Add">
    </p>

</form>

<h2>Automobiles</h2>

<?php if (count($autos) > 0): ?>

    <table border="1" cellpadding="8">

        <tr>
            <th>Make</th>
            <th>Year</th>
            <th>Mileage</th>
        </tr>

        <?php foreach ($autos as $auto): ?>

            <tr>
                <td><?= htmlentities($auto['make']) ?></td>
                <td><?= htmlentities((string)$auto['year']) ?></td>
                <td><?= htmlentities((string)$auto['mileage']) ?></td>
            </tr>

        <?php endforeach; ?>

    </table>

<?php else: ?>

    <p>No automobiles found.</p>

<?php endif; ?>

<br>

<form method="POST">
    <input type="submit" name="logout" value="Logout">
</form>

</body>
</html>