<?php

class simpleCMS {
  public $db = 'drupalbook_cms';
  public $host = 'localhost';
  public $username = 'root';
  public $password = '';

  public function display_public() {

    $sql = 'SELECT * FROM messages';
    $db_link = $this->connectDB();
    $result = mysqli_query($db_link, $sql);

    if($result) {
      while($row = mysqli_fetch_array($result)) {
        ?>
  
        <div class="post" id="'messsage_id-'<?= $row["message_id"] ?>'">
          <span class="time">#<?= $row['message_id'] ?> от <?= $row['created'] ?></span>
          <h2><?= $row['title'] ?></h2>
          <p><?= $row['bodytext'] ?></p>
          
          <p><a href="index.php?admin=update&message_id=<?= $row['message_id']?>">Редактировать</a></p>
          <p><a href="index.php?admin=delete&message_id=<?= $row['message_id']?>">Удалить</a></p>
        </div>
  
        <?php
      }
      mysqli_close($db_link);
      ?>
        <p><a href="index.php?admin=add">Добавить сообщение</a></p>
      <?php
    } else {
      echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL . '<br>';
      echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL . '<br>';
      echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL . '<br>';
      exit;
    }
    return;
  }
  
  public function display_admin() {
    ?>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
    <label for="title">Имя:</label><br>
    <input type="text" name="title" id="title" maxlength="150">
    <div class="clear"></div>
    <label for="bodytext">Сообщение: </label><br>
    <textarea name="bodytext" id="bodytext" cols="30" rows="10"></textarea>
    <div class="clear"></div>
    <input type="submit" value="Добавить сообщение">
    </form>
    <p><a href="index.php">Вернуться на главную</a></p>
    <?php
  }

  public function display_update() {
    $link = $this->connectDB();
    $message_id = $_GET['message_id'];

    if(!empty($message_id)) {
      $sql = 'SELECT * FROM messages WHERE message_id='.$message_id;
      $result = mysqli_query($link, $sql);
      $message = mysqli_fetch_object($result);
      ?>

      <form action="index.php?admin=update" method="post">
        <label for="title">Имя:</label><br>
        <input type="text" name="title" id="title" maxlength="150" value="<?= $message->title ?>">
        <div class="clear"></div>
        <input type="hidden" name="message_id" id="message_id" value="<?= $message->message_id ?>">
        <label for="bodytext">Сообщение:</label><br>
        <input type="textarea" name="bodytext" id="bodytext"><?= $message->bodytext ?>
        <div class="clear"></div>
        <input type="submit" value="Сохранить">
      </form>
      <?php
    } else {
      if(!empty($_POST)) {
        $sql = 'UPDATE messages SET title'.$_POST["title"].', bodytext='.$_POST["bodytext"].' WHERE message_id='.$_POST["message_id"];
        mysqli_query($link, $sql);
        ?>

        <p>Сообщение изменено</p>
        <p><a href="index.php#message_id-"<?php $_POST['message_id'] ?> >Перейти к записи</a></p>

        <?php

      } else {
        ?>
          <p>Нет значения</p>
          <p><a href="index.php">Вернуться на главную</a></p>
        <?php
      }
      mysqli_close($link);
      return;
    }
  }

  public function display_delete($message_id) {
    
    $sql = 'delete from messages where message_id='.$message_id;
    $link = $this->connectDB();
    if(!mysqli_query($link, $sql)) {
      echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL . '<br>';
      echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL . '<br>';
      echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL . '<br>';
      exit;
    }
    mysqli_guery($link, $sql);
    mysqli_close($link);
    exit;
  }

  public function connectDB() {
    $link = mysqli_connect(
      $this->host,
      $this->username,
      $this->password,
      $this->db
    );
    if(!$link) {
      echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL."<br>";
      echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL."<br>";
      echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL."<br>";
      exit;
    }
    echo '<br>';
    
    mysqli_select_db($link, $this->db) or die('Ошибка поиска БД: '.mysqli_error(link)); //подсоединяем БД
    
    return $link;
  }

  public function sql_write($p) {
    $db_link = $this->connectDB();
    $sql = 'INSERT INTO messages ( title, bodytext, created ) VALUES ( "'.$p["title"].'", "'.$p["bodytext"].'", "'.time().'")';
    mysqli_query($db_link, $sql);    
    mysqli_close($db_link);
  }

  public function sql_update($p) {
    $db_link = $this->connectDB();
    $sql = 'UPDATE messages SET title = "'.$p["title"].'", bodytext = "'.$p['bodytext'].'" WHERE message_id = "'.$p['message_id'].'";' ;
    mysqli_query($db_link, $sql);
    mysqli_close($db_link);
  }
}
?>
