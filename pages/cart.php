<h3>Заказ</h3>

<?php
echo '<form action="index.php?page=2" method="post">';

// проверка текущего имени пользователя
$ruser = 'cart';

// формируем корзину и суммарную стоимость товаров
$total = 0;
foreach ($_COOKIE as $k => $v) {
    //echo $k . '-----' . $v.'<br>';
    $pos = strpos($k, "_");
    // делаем проверку по имени пользователя
    if(substr($k, 0, $pos) === $ruser) {
        // получить номер товара
        $id = substr($k, $pos+1);
        //echo $id.'<br>';
        // получения данных о товаре по id
        $item = Item::fromDb($id);
        // var_dump($item);
        // считаем общую стоимость всех товаров в корзине
        $total += $item->pricesale;
        // отрисовываем товар
        $item->drawForCart();
    }
}

echo '<hr>';
echo "<span class='ml-5 text-primary'>Итого: $total руб.</span>";
echo '<button type="submit" class="btn btn-success btn-lg ml-5 mb-3 float-right" name="suborder" onclick=eraseCookie("'.$ruser.'")>Отправить заказ</button>';
echo '</form>';

//Оработчик длоя оформдения заказов
if(isset($_POST['suborder'])){
    $id_result=[];
    foreach ($_COOKIE as $k => $v) {
     
        $pos = strpos($k, "_");
       
        if(substr($k, 0, $pos) === $ruser) {
            $id = substr($k, $pos+1);
            $item = Item::fromDb($id);
            array_push($id_result, $item->sale()); // метод для оформления заказа

                    
        }
    }
    $item->SMTP($id_result);
}
?>

<script>
    function eraseCookie(ruser) {
        $.removeCookie(ruser, { path: '/'});
    }
</script>