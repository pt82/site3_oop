



 

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php?page=1">Каталог <span class="sr-only">(current)</span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="index.php?page=2">Корзина <span class="sr-only">(current)</span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="index.php?page=3">Регистрация <span class="sr-only">(current)</span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="index.php?page=4">Добавить товар <span class="sr-only">(current)</span></a>
      </li>
      </ul>

      <div class="form-inline my-2 my-lg-0">
         <?php
      if(isset($_SESSION['ruser'])){
    echo '<form action="index.php';
    if(isset($_GET['page'])) echo '?page='.$_GET['page'];
    echo '" method="post" class="input-group">';
    echo '<h4 class = "lead">Hello, '.$_SESSION['ruser'].'</h4>';
    echo '<input type="submit" name="exit" value="Выйти" class="btn btn-dafault btn-sm">';
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
        echo "<meta http-equiv=\"refresh\" content=\"2;index.php?page=1\">"; 
        }
    } else {

    echo '<form action="index.php';
    if(isset($_GET['page'])) echo '?page='.$_GET['page'];
    echo '" method="post" class="input-group">';
    echo '<input type="text" name="login" >';
    echo '<input type="password" name="pass" >';
    echo '<input type="submit" name="press" value="Войти" class="btn btn-dafault" >';
    echo '</form>';
    }
    }
?>
</div>





  
  





  </div>
