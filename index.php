<?php 
session_start();
ini_set('display_errors', 1); 
?>
<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- <link rel="manifest" href="site.webmanifest"> -->
  <link rel="apple-touch-icon" href="icon.png">
  <!-- Place favicon.ico in the root directory -->

  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/main.css">

  <meta name="theme-color" content="#fafafa">
  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">


</head>

<body>
  <!--[if IE]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
<?php
  if(isset($_POST['join'])) {
    
    require("db/users.php");
    $objUser = new users;
    $objUser->setEmail($_POST['email']);
    $objUser->setName($_POST['uname']);
    $objUser->setLoginStatus(1);
    $objUser->setLastLogin(date('Y-m-d h:i:s'));
    $userData = $objUser->getUserByEmail();
    
    if(is_array($userData) && count($userData)>0) {
      $objUser->setId($userData['id']);
      if($objUser->updateLoginStatus()) {
        echo 'User Login...';
        $_SESSION['user'][$userData['id']] = $userData;
        header("location: chatroom.php");
      } else {
        echo 'Failed to login..';
      }
    } else {
      if($objUser->save()) {
        $lastId = $objUser->conn->lastInsertId();
        $objUser->setId($lastId);
        $_SESSION['user'][$userData['id']] = (array) $objUser;
        echo 'User Registered...';
        header("location: chatroom.php");
      } else {
        echo 'Failed';
      }
    }
  }

?>

<div class="container" style="padding-top: 5%;">
  <div class="row text-muted">
    <div class="col-lg-6 col-lg-offset-3">
      <form action="" id="join-room-frm" role="form" method="post">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="uname" class="form-control" id="uname" name="uname" placeholder="Enter Name">
        </div>
        <div class="form-group">
          <label for="email">Email address:</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
        </div>

        <button type="submit" class="btn btn-success btn-block" name="join" id="join">Join Chatroom</button>
      </form>
    </div>
  </div>
</div>

















  <script src="js/vendor/modernizr-3.8.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.4.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>

  <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
  <script>
    window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
    ga('create', 'UA-XXXXX-Y', 'auto'); ga('set','transport','beacon'); ga('send', 'pageview')
  </script>
  <script src="https://www.google-analytics.com/analytics.js" async></script>
  <!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>

</html>
