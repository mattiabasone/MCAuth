<html>
<head>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
</head>
<body>
<div class="container" style="margin-top: 35px;">
<div class="row">
<?php
if (isset($_POST['login']) AND isset($_POST['password'])) {
    include("MCAuth.class.php");
    $MCAuth = new MCAuth();
    echo '<div class="col-md-6 col-md-offset-3">';
    if ($MCAuth->authenticate($_POST['login'], $_POST['password']) == TRUE) {
        echo '<pre>';
        print_r($MCAuth->account);
        echo '</pre>';
    } else {
        echo '<pre>';
        print_r($MCAuth->autherr);
        echo '</pre>';
    }
    echo '</div>';
} else {
    ?>
        <div class="col-md-4 col-md-offset-4">
        <form action="#" method="post">
            <legend>MCAuth tester</legend>
            <input class="form-control" type="text" name="login" placeholder="username/email"><br />
            <input class="form-control" type="password" name="password" placeholder="password"><br />
            <button class="btn btn-default btn-block" type="submit">Test</button>
        </form>
        </div>
    <?php
}
?>

</div>
</div>
</body>
</html>