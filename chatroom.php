<?php session_start(); 
ini_set('display_errors', 1); 
if(!isset($_SESSION["user"])) { 
  header("location:index.php"); 
} 
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


  <link rel="stylesheet" href="css/bootstrap.min.css">

  <meta name="theme-color" content="#fafafa">
  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" />

<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
 
</head>

<body>


<div class="container" style="padding-top: 5%;">
  <div class="row text-muted">
    <div class="col-lg-4 ">
      <?php
                // print_r($_SESSION);
        require("db/users.php");
        require("db/chatrooms.php");
        $objChatroom = new chatrooms;
        $chatrooms = $objChatroom->getAllChatrooms();

        $objUser = new users;
        $users = $objUser->getAllUsers();
      ?>
      <table class="table table-striped">
        <caption></caption>
        <thead>
          <tr>

            <td>
                <?php
                    foreach ($_SESSION["user"] as $key => $user) {
                      $userId = $key;
                      echo '<input type="hidden" name="userId" id="userId" value="'.$key.'">';
                      echo "<div>" . $user["name"]. "</div>";
                      echo "<div>" . $user["email"]. "</div>";
                    }
                ?>
            </td>
            <td align="right" colspan="2">
                <input type="button" class="btn btn-warning" id="leave-chat" name="leave-chat" value="Leave">
            </td>
          </tr>
            <th colspan="3" >Users</th>
        </thead>
        <tbody>
          <tr>
            <td>
                <?php
                    foreach ($users as $key => $user) {
                        $color = 'color:red;';
                      if($user['login_status']==1) {
                        $color = "color:rgb(0,255,0);";
                      }

                      if(!isset($_SESSION['user'][$user['id']])) {
                        echo "<tr><td>" . $user["name"]. "</td>";
                        echo "<td> <span style='".$color."' >@</span> </td>";
                        echo "<td>" . $user["last_login"]. "</td></tr>";
                      }
                    }
                ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <div class="col-lg-8">
        <div id="messages">
          <table id="chats" class="table table-striped">
            <thead>
              <tr>
                <th colspan="4" scope="col"><strong>Chat Room</strong></th>
              </tr>
            </thead>
            <tbody>
              <!-- <tr>
                <td valign="top"><div><strong>From</strong></div><div>Message</div></td>
                <td align="right" valign="top" > Message Time</td>
              </tr> -->
              <?php
                foreach ($chatrooms as $key => $chatroom) {
                  if($userId == $chatroom['userid']) {
                    $from = "Me";
                  } else {
                    $from = $chatroom['name'];
                  }
                  echo '<tr><td valign="top"><div><strong>'. $from .'</strong></div><div>'. $chatroom['msg'] .'</div></td><td align="right" valign="top" > '. date('d/m/Y h:i:s A',strtotime($chatroom['created_on'])) .'</td></tr>';
                }

              ?>
            </tbody>
          </table>
        </div>
          
        <form id="chat-room-frm" method="post" action="">
          <div class="form-group">
                      <textarea class="form-control" id="msg" name="msg" placeholder="Enter Message"></textarea>
                  </div>
                  <div class="form-group">
                      <input type="button" value="Send" class="btn btn-success btn-block" id="send" name="send">
                  </div>
          </form>
      </div>

  </div>
</div>



 
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

  <!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


<script type="text/javascript">
  $(document).ready(function() {
    var conn = new WebSocket('ws://localhost:8080');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
        var data = JSON.parse(e.data);
        var row = '<tr><td valign="top"><div><strong>'+ data.from +'</strong></div><div>'+ data.msg +'</div></td><td align="right" valign="top" > '+ data.dt +'</td></tr>';
        $('#chats > tbody').prepend(row);
        $('#msg').val('');
    };

    conn.onclose = function(e) {
      console.log("Connection Closed");
    }

    $("#send").click(function() {
      /* Act on the event */
      var userId = $("#userId").val();
      var msg = $("#msg").val();
      var data = {
        userId: userId,
        msg: msg
      };

      conn.send(JSON.stringify(data));
    });

    $("#leave-chat").click(function(event) {
      var userId = $("#userId").val();
      $.ajax({
        url: 'action.php',
        method: 'post',
        data: "userId="+userId+"&action=leave",
      })
      .done(function(result) {
        console.log("success ");
        console.log(result);
        conn.close();
        var data = JSON.parse(result);
        if(data.status == 1) {
          location = 'index.php';
        } else {
          console.log(data.msg);
        }
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
      
    });

  });
</script>
</body>

</html>
