<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>=== drupalBook ===</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div id="page-wrap">
    <?php
      include_once('class/simpleCMS.php');
      $obj = new simpleCMS;
      $db_connection = $obj->connectDB();

      // var_dump($db_connection);

      mysqli_close($db_connection);

      switch ($_GET['admin']) {
        case 'add':
          if(!$_POST) {
            print $obj->display_admin();
          }
        break;
        case 'update':
          print $obj->display_update();
        break;
        case 'delete':
          if($_GET['message_id']) {
            $obj->display_delete($_GET['message_id']);
            // print $obj->display_public();
            echo '<p>Message successfully deleted</p>';
            echo '<a href="index.php">Go to main page</a>';
          } else {
            print '<p>не выбран message_id</p>';
            print $obj->display_public();
          }
        break;
      default:
        if(!$_POST) {
          print $obj->display_public();
        }
      }
    
      

      /**
       * проверяем, есть ли что-то в переменной $_POST
       * для вывода на страницу
       */
      if($_POST) {
        // var_dump($_POST);
        if($_GET['admin'] == 'update') {
          $obj->sql_update($_POST);
        } else {
          $obj->sql_write($_POST);
        }
        print $obj->display_public();
      }
    ?>
  </div>
  
</body>
</html>