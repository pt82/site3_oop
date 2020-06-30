<h3>Catalog page</h3>

<form action="index.php?page=1" method="post">
<div class="form-group row">

<div class="col-sm-4">
        <select name="catid" class="form-control" onchange="getItemsCat(this.value)">
            <option value="0">Select category:</option>
            <?php
                $pdo=Tools::connect();
                $ps=$pdo->prepare("SELECT * FROM categories");
                $ps->execute();
                // добавляем все категории в option'ы
                while($row=$ps->fetch()) {
                    echo "<option value=".$row['id'].">".$row['category']."</option>";
                }
            ?>
        </select>
    </div>
    </div>

    <?php
    // получаем все товары из метода getItems()
    echo '<div id="result" class="row">';
    $items = Item::getItems(); // получаем массив экземпляров товаров
    foreach ($items as $item) {
//         var_dump($item);
        // вызываем метод отрисовки карточки товара для текущего экземпляра товара
        $item->drawItem();
    }
    echo '</div>';
    ?>
</form>

<script>
    function getItemsCat(cat) {
        if(window.XMLHttpRequest) {
            ao = new XMLHttpRequest();
        } else {
            ao = new ActiveXObject('Microsoft.XMLHTTP');
        }

        ao.onreadystatechange = function () {
            if(ao.readyState === 4 && ao.status === 200) {
                document.getElementById('result').innerHTML = ao.responseText;
            }
        };

        ao.open('POST', 'pages/lists.php', true);
        ao.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ao.send("cat="+cat);
    }


    // создаем функцию занесения товара в куки
    function createCookie(ruser, id) {
        $.cookie(ruser, id, { expires: 2, path: '/' });
    }
</script>
