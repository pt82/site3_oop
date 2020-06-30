<?php

if(isset($_SESSION['ruser'])){
    echo '<form action="index.php';
    if(isset($_GET['page'])) echo '?page='.$_GET['page'];
    echo '" method="post" class="input-group">';
    echo '<h4 class = "lead">Hello, '.$_SESSION['ruser'].'</h4>';
    echo "<br>";
    echo '<input type="submit" name="exit" value="logout" class="btn btn-dafault btn-sm">';
    echo '</form>';
   if(isset($_POST['exit'])){
       unset($_SESSION['ruser']);
       $_SESSION['ruser']=null;
      echo '<script> window.location.reload()</script>';
   }
    }
    else
    {
    
    if(isset($_POST['press'])) 
    {
      if(Tools::login($_POST['login'], $_POST['pass']))
       {
        echo '<script> window.location.reload()</script>';
       }
       else
        {
        echo "<h3 class='text-danger'>имя и пароль не совпадают</h3>"; 
        }
    } else {

    echo '<form action="index.php';
    if(isset($_GET['page'])) echo '?page='.$_GET['page'];
    echo '" method="post" class="input-group">';
    echo '<input type="text" name="login" >';
    echo '<input type="password" name="pass" >';
    echo '<input type="submit" name="press" value="Login" class="btn btn-dafault" >';
    echo '</form>';
    }
    }
?>