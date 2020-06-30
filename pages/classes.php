<?php

class Tools {
    static function connect($host="localhost:3306", $user="root", $pass="123456", $dbname="shop") {
      //  static function connect($host="localhost", $user="gkb19_root", $pass="xwcq5wku", $dbname="gkb19_shop") {
        // PDO(PHP data object) - механизм взаимодействия с СУБД(система управления базами данных).
        // PDO - позволяет облегчить рутинные задачи при выполнении запросов и содержит защитные механизмы
        // при работе с СУБД

        // формировка строки для создания PDO
        // определим DSN (Data Source Name) — сведения для подключения к базе, представленные в виде строки.
        $cs = 'mysql:host='.$host.';dbname='.$dbname.';charset=utf8';

        // массив опций для создания PDO
        $options = array(
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES UTF8'
        );

        try {
            // пробуем создать PDO
            $pdo  = new PDO($cs, $user, $pass, $options);
            return $pdo;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    static function login($login, $pass ) {
       
     
   
      $name = trim(htmlspecialchars($login));
      $pass = trim(htmlspecialchars($pass));
      if($name=="" || $pass == "") {
          echo '<h3 style="color:red;">Fill all fields</h3>';
          return false;
      }
      if(strlen($name) < 3 || strlen($name) > 30 || strlen($pass) < 3 || strlen($pass) > 30) {
          echo "<h3 class='text-danger>От 3 до 30 символов</h3>";
          return false;
      }
    
        try {
            $pdo = Tools::connect();
            $ps=$pdo->prepare("SELECT * FROM customers WHERE login=?");
           $ps->execute([$login]);
          $row = $ps->fetch();
         // $customer = new Customer($row['login'], $row['pass'],  $row['imagepath'], $row['id']);
           if($name == $row['login'] and $pass==$row['pass']){
            $_SESSION['ruser']  = $name;
            return [true];
        }
        else {
            
               return false;
        }
      
        }catch(PDOException $e) {
            echo $e->getMessage();

            return false;
        }
    
    }

    


    static function register($login, $pass, $path) {
        $login = trim($login);
        $pass = trim($pass);
        $imagepath = $path;

        if($login == '' || $pass == '') {
            echo '<h3 class="text-danger">Fill all fields</h3>';
            return false;
        }

        if(mb_strlen($login) < 3 || mb_strlen($login) > 30 || mb_strlen($pass) < 3 || mb_strlen($pass) > 30) {
            echo '<h3 class="text-danger">Incorrect length</h3>';
            return false;
        }

        Tools::connect();
        // создаем экземпляр класса Customer и передает в его конструктор логин, пароль, и путь изображения
        $customer = new Customer($login, $pass, $imagepath);
        // после того как мы передали их в конструктор внутри него значения будут записаны в свойства класса
        // и мы можем вызвать метод занесения этих данных в таблицу customers, через метод intoDb()
        $customer->intoDb();
        return true;
    }


  

}


class Customer {
    public $id;
    public $login;
    public $pass;
    public $roleid;
    public $discount;
    public $total;
    public $imagepath;

    function __construct($login, $pass, $imagepath, $id=0) {
        $this->login = $login;
        $this->pass = $pass;
        $this->imagepath = $imagepath;
        $this->id = $id;

        $this->total = 0;
        $this->discount = 0;
        $this->roleid = 2;
    }


    


    // ORM(Object-Relational Mapping) - объектно реляционное отображение. Это механизм работы сущности в связи с БД.

    // внести покупателя в таблицу
    function intoDb() {
        try {
            $pdo = Tools::connect();
            // выполнение запроса через PDO на внесение данных
            $ps = $pdo->prepare("INSERT INTO customers(login, pass, roleid, discount, total, imagepath) VALUES (:login, :pass, :roleid, :discount, :total, :imagepath)");
            // разименовывание массива. Мы преобразуем объект класса $this в массив
            $ar = (array) $this;
            // :id, :login, :pass, :roleid, :discount, :total, :imagepath

            array_shift($ar); // удаляем первый элемент массива, т.е. id
            // :login, :pass, :roleid, :discount, :total, :imagepath
            // выполним запрос без id
            $ps->execute($ar);
        } catch(PDOException $e) {
            echo $e->getMessage();
//            var_dump($ar);
            return false;
        }
    }

    // получаем данные о созданном пользователе из таблицы
    static function fromDb($id) {
        $customer = null;
        try {
            $pdo = Tools::connect();
            $ps=$pdo->prepare("SELECT * FROM customers WHERE id=?");
            // выполняем выбор всех данных о пользователе по $id получаемому в качестве параметра в ф-ю fromDb
            // и заносим его в массив, ибо метод execute этого требует. При выполнеии execute $id будет подставлен
            // вместо символа ? при подготовке(метод prepare)
            $res = $ps->execute(array($id));
            // перебираем данные о полученном пользователе и заносим его в ассоциативный массив $row
            $row = $res->fetch();
            $customer = new Customer($row['login'], $row['pass'], $row['imagepath'], $row['id']);
            return $customer;
        }catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }



}


class Item {
    public $id;
    public $itemname;
    public $catid;
    public $pricein;
    public $pricesale;
    public $info;
    public $rate;
    public $imagepath;
    public $action;

    function __construct($itemname, $catid, $pricein, $pricesale, $info, $imagepath, $rate=0, $action=0, $id=0) {
        $this->id = $id;
        $this->itemname = $itemname;
        $this->catid = $catid;
        $this->pricein = $pricein;
        $this->pricesale = $pricesale;
        $this->info = $info;
        $this->rate = $rate;
        $this->imagepath = $imagepath;
        $this->action = $action;
    }

    function intoDb() {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("INSERT INTO items(itemname, catid, pricein, pricesale, info, imagepath, rate, action) 
                                          VALUES (:itemname, :catid, :pricein, :pricesale, :info, :imagepath, :rate, :action)");
            $ar = (array) $this;
            array_shift($ar);
            $ps->execute($ar);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    static function fromDb($id) {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM items WHERE id=?");
            $ps->execute([$id]);
            $row = $ps->fetch();
            $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'],
                $row['imagepath'], $row['rate'], $row['action'], $row['id']);
            return $item;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }


    // получение товаров
    static function getItems($catid = 0) {
        try {
            $pdo = Tools::connect();
            if($catid == 0) {
                // выбираем все товары
                $ps=$pdo->prepare("SELECT * FROM items");
                $ps->execute();
            } else {
                // выбираем товары определенной категории
                $ps=$pdo->prepare("SELECT * FROM items WHERE catid=?");
                $ps->execute([$catid]);
            }

            while($row = $ps->fetch()) {
                // создаем экземпляр класса Item передавая их в конструктор класса
                $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'],
                    $row['imagepath'], $row['rate'], $row['action'], $row['id']);
                // ассоциативный массив, который хранит все экземпляры класса товаров, т.е. данные о товарах из таблицы
                $items[] = $item;
            }
            // возвращаем все товары в точку вызова (страница catalog.php)
            return $items;

        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // метод для отрисовки карточки товара
    function drawItem() {

            
             echo '<div class="col-sm-6 col-md-3 card m-2">';
            echo '<div class="card-body">';
            // верхушка товара
           // echo '<div class="mt-1 bg-dark">';
            echo '<div class="card-title">';
            echo "<a href='pages/item_info.php?name=".$this->id."' class='ml-2 float-left' target='_blank'>".$this->itemname."</a>";
            echo "<span class='mr-2 float-right'>".$this->rate." rate </span>";
            echo '</div>';

            // изображение товара
            echo "<div class='mt-1 card-img-top'>";
          //  echo "<div class='mt-1 '>";
            echo "<img src='".$this->imagepath."' class='img-fluid item-card__img mt-3 ' alt='item' >";
            echo '</div>';

            // цена товара
            echo "<div class='mt-1 text-center text-danger item-card__price card-text'>";
            echo "<span>".$this->pricesale." руб.</span>";
            echo '</div>';

            // описание товара
            echo "<p class='card-text item__info'>";
            echo "<span>".$this->info."</span>";
            echo '</p>';

            // кнопка добавления в корзину
            echo "<div class='mt-1 text-justify bg-primary item-card__basket'>";
         //   $ruser = '';
            // проверка на зарегистрированного пользователя
           if(!isset($_SESSION['ruser']) || ($_SESSION['ruser'] ==null)){
               $ruser = 'cart_'.$this->id;
          } else {
                $ruser = $_SESSION['ruser'] . "_" . $this->id;
            }

            $ruser = 'cart_'.$this->id;

            echo "<button class='btn btn-success btn-lg ' onclick=createCookie('".$ruser."','".$this->id."')>Добавить в корзину </button>";
            echo '</div>';
            echo '</div>';
        echo '</div>';
    }


    // метод для отрисовки выбранных товаров в корзине
    public function drawForCart() {
        
        echo "<div class='card ml-5 mt-1 col-7'>";
        echo "<div class='row m-2'>";
        echo "<span class='col-1'>$this->id</span>";
        echo "<img src='".$this->imagepath."' class='col-3 img-fluid'>";
        echo "<span class='col-5'>$this->itemname</span>";
        echo "<span class='col-2'>$this->pricesale</span>";
        $ruser="cart_".$this->id;
        echo "<button class='btn btn-danger col-1 float-left' onclick=eraseCookie('".$ruser."')>x</button>";
        echo "</div>";
        echo "</div>";
    }

    //метод для оформдения заказа
    function sale() {
        try {
            $pdo = Tools::connect();
            $ruser='cart';
          if($_SESSION['ruser']!==NULL) {
              $ruser = $_SESSION['ruser'];
          }
          $upd= "UPDATE customers SET total=total+? WHERE login=?";
          $ps = $pdo->prepare($upd);
          $ps->execute([$this->pricesale, $ruser]);
            //создаем данные о покупки товара с занисением в таблицу sales
             $ins = "INSERT INTO sales(customername, itemname, pricein, pricesale, datesale) VALUES (?, ?, ?, ?, ?)";
             $ps = $pdo->prepare($ins);
             $ps->execute([$ruser, $this->itemname, $this->pricein, $this->pricesale, @date("Y/m/d H:i:s")]);
                return $this->id;
                } catch (PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

//    function SMTP($id_result){
//        //подключаем модуль PHPMaller
//        require_once("PHPMailer/PHPMailerAutoload.php");
//        require_once("private_data.php");
//        $mail = new PHPMailer;
//        $mail->CharSet="UTF-8";
//
//        //
//        $mail->isSmTP();
//        $mail->SMTPAuth=true;
//        $mail->Host='ssl://smtp.mail.ru';
//        $mail->Port = 465;
//        $mail->Username=MAIL;
//        $mail->Password=PASS;
//    //от кого приходит письмо
//
//    $mail->setFrom('pt.82@mail.ru', 'SHOP PETR');
//
//    //кому 
//    $mail->addAddress('pt.82@mail.ru', 'ADMIN');
//
//    //тема письма
//    $mail->Subject = 'Новый заказ на сайте PETR';
//
//    //тело письма
//    $body = "<table cellspacing='0' cellpading='0' border='2' width='800' style='background-color:green!important'>";
//    $i=0;
//    foreach ($id_result as $id) {
//
//        $item =self::fromDb($id);
//        array_push($arrItem, $item->image);
//        $mail->AddEmbeddedImage($item->imagepath, 'item'.++$i);
//        $body .= "<tr><th>$item->itemname</th><td>$item->pricesale</td><td>$item->info</td><td><img src='cid:item'></td></tr>";
//    }
//    $body .= '</table>';
//
//    $mail->msgHTML($body);
//    $mail->send();
//
//    //csv
//
////    try{
////        $csv = new CSV("private/exel_pr.csv");
////        $csv->setCSV($arrItem);
////
////    }catch (Exeption $e) {
////        echo "Error:" . $e->getMessage();
////    }
////    }
////    class CSV {
////        private $csv_file=null;
////        public function __construct($csv_file) {
////            $this->csv_file = $csv_file;
////        }
////        function setCSV($arrItem){
////            //открываем csv файл для дозаписи
////            $file = fopen($this->csv_file, 'a+') 
////            foreach (arrItem as $item) {
////               fputcsv($file, [$item]);
////            }
////        }
////        fclose($file);
////    }
//}
//}

function SMTP($id_result) {
    // подключить моудль PHPMailer
    require_once("PHPMailer/PHPMailerAutoload.php");
    require_once("private/private_data.php");

    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";

    // настраивавем SMTP(почтовый протокол передачи данных)
    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = 'ssl://smtp.mail.ru';
    $mail->Port = 465;
    $mail->Username = MAIL;
    $mail->Password = PASS;

    // от кого
    $mail->setFrom('pt.82@mail.ru', 'SHOP internet');

    // кому
    $mail->addAddress('pt.82@mail.ru', 'ADMIN');

    // тема письма
    $mail->Subject = 'Новый заказ на сайте SHOP internet';

    // Тело письма
    $body = "<table cellspacing='0' cellpadding='0' border='2' width='800' style='background-color: orange!important'>";

     $i = 0;
    foreach ($id_result as $id) {
        $item = self::fromDb($id);
        $cid=$item->imagepath;
        $mail->AddEmbeddedImage($item->imagepath, $cid);
       
        
        $body .= "<tr>
                    <th>$item->itemname</th>
                    <td>$item->pricesale</td>
                    <td>$item->info</td>
                    '<td><img src='cid:$cid' alt='item' height='100'></td>'
                  </tr>";
    }
    $body .= '</table>';

    $mail->msgHTML($body);
    $mail->send();
}
}