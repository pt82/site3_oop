<?php
if (!isset($_POST['addbtn'])) {
    ?>
    <form action="index.php?page=4" method="post" enctype="multipart/form-data" class="mt-2">
    <div class="form-group row">

        <label for="catid" class="col-sm-3  text-light h4">Выберите категорию:</label>
        <div class="col-sm-5">
            <select name="catid" class="form-control" id="catid">
                <?php
                $pdo = Tools::connect();
                $list = $pdo->query("SELECT * FROM categories");
                while ($row = $list->fetch()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['category'] . "</option>";
                }
                ?>
            </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-sm-3 text-light h4"> Наименование товара </label>
            <div class="col-sm-5" >
                <input type="text" name="name" id="name" class="form-control">
              </div>
              </div>
        <div class="form-group row">
        <label for="pricein" class="col-sm-3 text-light h4"> Цена закупки </label>
            <div class="col-sm-5">
                <input type="number" name="pricein" class="form-control" id="pricein">
         </div>
        </div>

        <div class="form-group row">
        <label for="pricesale" class="col-sm-3 text-light h4"> Цена продажи </label>
            <div class="col-sm-5">
            <input type="text" name="pricesale" class="form-control" id="pricesale">
         </div>
        </div>        


        


        <div class="form-group row">
            <label for="info" class="col-sm-3 text-light h4">Описание товара </label>
            <div class="col-sm-5">
                <textarea class="form-control" name="info" ></textarea>
            </div>
        </div>

        <div class="form-group row">
            <label for="imagepath" class="col-sm-3 text-light h4">Добавить фото </label>
            <div class="col-sm-5">
                <input type="file" name="imagepath" class="btn btn-success">
                </div>
                <div class="col-sm-3 ">
                <button type="submit" class="btn btn-primary w-100" name="addbtn">Добавить товар</button>
                </div>
        </div>

        
    </form>
    <?php
} else {
    if (is_uploaded_file($_FILES['imagepath']['tmp_name'])) {
        $path = "images/goods/" . $_FILES['imagepath']['name'];
        move_uploaded_file($_FILES['imagepath']['tmp_name'], $path);
    }

    $name = trim($_POST['name']);
    $info = trim($_POST['info']);
    $catid = $_POST['catid'];
    $pricein = $_POST['pricein'];
    $pricesale = $_POST['pricesale'];

    // передаем значения в конструктор класса Item
    $item = new Item($name, $catid, $pricein, $pricesale, $info, $path);
    $item->intoDb();
}
?>