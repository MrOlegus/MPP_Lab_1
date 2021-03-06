
<?php>
    require './modules/MySql.php';
    require './modules/User.php';
    require './modules/Content.php';
    
    CreateUserSession();
    
    $link = MySqlConnect();
    AddVisiting($link, "play.php");
?>

<p class = mainText>
    Здесь вы можете посмотреть отзывы наших пользователей, а также оставить свой собственый. Мы Вам доверяем, поэтому публикуем отзывы без проверки. Пожалуйста, пишите грамотно.
</p>

<form class="reviewForm">
    <p class="mainText">Напишите свой отзыв здесь:</p>
    <textarea id="reviewArea" type="text" class="reviewArea" name="review" rows="5" cols="100"></textarea>
    
    <p class="radioMarks">
        <span><input id="radio1" class="radioMark" name="mark" type="radio" value="1">1</span>
        <span><input id="radio2" class="radioMark" name="mark" type="radio" value="2">2</span>
        <span><input id="radio3" class="radioMark" name="mark" type="radio" value="3">3</span>
        <span><input id="radio4" class="radioMark" name="mark" type="radio" value="4">4</span>
        <span><input id="radio5" class="radioMark" name="mark" type="radio" value="5" checked>5</span>
    </p>

    <input id="maxSize" type="hidden" name="MAX_FILE_SIZE" value="30000000" />
    <p class="mainText">Прикрепить картинку:
        <input id="reviewFile" name="reviewPic" type="file" accept="image/*"/>
    </p>
    
    <p><input class="sendButton" type="button" value="Отправить" onclick="btnReviewOnClick()"></p>
</form>

<?php
    if (isset($_POST['review']))
    {
        if (isset($_FILES['reviewPic']))
        {
            $uploadDir = './pics/review_pics/';
            $tmpFileName = basename($_FILES['reviewPic']['name']);
            $uploadFile = $uploadDir . date('Y-m-d_H-i-s_') . $tmpFileName;

            if (!move_uploaded_file($_FILES['reviewPic']['tmp_name'], $uploadFile))
                $uploadFile = null;
        }
        
        $id = -1;
        if (isset($_SESSION['login']))
        {
            $name=$_SESSION['login'];
            $authorResource =  mysqli_query($link, "SELECT * FROM `Users` WHERE `login`=\"" . $name . "\"");
            $id = mysqli_fetch_assoc($authorResource)["ID"];
        }
        mysqli_query($link,
        "INSERT INTO `Reviews`(`AuthorID`, `Time`, `Text`, `Mark`, `picPath`) VALUES (" . $id . ", \"" . date("Y-m-d H:i:s") . "\", \""
        . mysqli_real_escape_string($link, $_POST['review']) . "\", "
        . mysqli_real_escape_string($link, $_POST['mark']) . ", \""
        . $uploadFile . "\")");
    }
?>

<h1>Отзывы</h1>

<?php
    if (isset($_POST['sort']) && ($_POST['sort'] == "Time")) $checked1 = "checked";
    else $checked1 = "";
    if (isset($_POST['sort']) && ($_POST['sort'] == "Mark")) $checked2 = "checked";
    else $checked2 = "";
?>

<form class="sortForm">
    <p class="mainText">Сортировать по:
        <span><input id="radioTime" class="radioMark" name="sort" type="radio" value="Time" <?php echo $checked1 ?> >дате</span>
        <span><input id="radioMark" class="radioMark" name="sort" type="radio" value="Mark" <?php echo $checked2 ?> >оценке</span>
        <input id="btnSort" class="sendSortButton" type="button" value="Показать" onclick="btnSortOnClick()">
    </p>
</form>

<?php
    if (isset($_POST['sort'])) $sortOrder = $_POST["sort"];
    else $sortOrder = "Time";
    PrintReviews($link, $sortOrder);
?>
