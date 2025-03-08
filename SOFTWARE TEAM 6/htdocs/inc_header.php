<?php if (!isset($title)) $title = "ROCU"; ?>

<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <h1><?php echo $title; ?></h1>