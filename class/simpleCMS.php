<?php

class simpleCMS {
  public $db = 'db_simplecms';
  public $host = 'localhost';
  public $username = 'user1';
  public $password = 'Vkshmuk:0707';

  public function display_public() {

    $sql = 'SELECT * FROM messages LEFT JOIN files ON messages.file_id = files.file_id ORDER BY message_id DESC';
    $db_link = $this->connectDB();
    $result = mysqli_query($db_link, $sql);

    if($result) {
      while($row = mysqli_fetch_array($result)) {
        ?>
  
        <div class="post" id="'messsage_id-'<?= $row["message_id"] ?>'">
          <span class="time">#<?= $row['message_id'] ?> от <?= date('d-D-M-Y', $row['created']) ?></span>
          <h2><?= $row['title'] ?></h2>
          <p><?= $row['bodytext'] ?></p>
          <?php if(!empty($row['filename'])) { ?>
            <p>Приложенный файл: <a href=<?= $row['filepath'] ?> target="_blank"><?= $row['filename'] ?></a></p>
          <?php } ?>
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
    <input type="file" name="filename">
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
        <input type="file" name="filename">
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
    if($_FILES['filename']['zize'] > 1024*3*1024) {
      echo('<p>File size is more than 3mb</p>');
    }
    if(is_uploaded_file($_FILES['filename']['tmp_name'])) {
      move_uploaded_file($_FILES['filename']['tmp_name'], 'files/'.$_FILES['filename']['name']);
    } else {
      echo ('<p>Cannot upload file</p>');
    }

    $db_link = $this->connectDB();

    $sql = 'INSERT INTO files(filename, filepath, filemime, filesize, timestamp) VALUES ("'.$_FILES['filename']['name'].'", "files/'.$_FILES['filename']['name'].'", "'.$_FILES['filename']['type'].'", "'.$_FILES['filename']['size'].'", "'.time().'")';
    mysqli_query($db_link, $sql);
    
    $sql = 'INSERT INTO messages ( title, bodytext, created, file_id ) VALUES ( "'.$p["title"].'", "'.$p["bodytext"].'", "'.time().'", "'.mysqli_insert_id($db_link).'")';
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