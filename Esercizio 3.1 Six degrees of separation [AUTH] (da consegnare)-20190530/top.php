<!-- <GABOR,GALAZZO,20024195> -->
<!DOCTYPE html>
<html lang="en">
<!-- Metodologie per il web - A.A. 2018/2019 - Esercizio 3: Six degrees of separation - Kevin Bacon -->
<head>
    <title>My Movie Database (MyMDb)</title>
    <meta charset="utf-8">

    <!-- Links to provided files.  Do not edit or remove these links -->
    <link href="http://www.cs.washington.edu/education/courses/cse190m/12sp/homework/5/favicon.png" type="image/png"
          rel="shortcut icon">
    <script src="http://www.cs.washington.edu/education/courses/cse190m/12sp/homework/5/provided.js"></script>

    <!-- Link to your CSS file that you should edit -->
    <link href="bacon.css" type="text/css" rel="stylesheet">
</head>

<body>
<div id="frame">
    <div id="banner">
        <a href="index.php"><img src="http://www.cs.washington.edu/education/courses/cse190m/12sp/homework/5/mymdb.png"
                                 alt="banner logo"></a>
        My Movie Database
        <?php if (isset($_SESSION['user'])): ?>
            <span class="user-info">
                Logged as <?= $_SESSION['user']['username'] ?>, <a href="login.php?logout=1">Logout</a>
            </span>
        <?php endif; ?>
    </div>

    <div id="main">
        <!-- your HTML output follows -->
