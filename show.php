<?php
/**
 * Страница сообщений для проекта Стена сообщений (The Wall of Messages)
 * Проект разработан в порядке выполнения тестового задания на вакансию веб-разработчик в компании Light IT
 *
 * Copyright (C) 2017 Alexander Malygin
 *
 */
error_reporting( E_ERROR | E_WARNING );
require('thewall-model.php');

global $uid;
$uid = $_REQUEST['uid'];    // ID пользователя в базе, параметр передаётся при переходе со страницы авторизации
$uname = GetUserName($uid); // Имя пользователя, если он задан и есть в базе
if (!$uname) unset($uid);   // Если нет такого - играем без пользователя
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0//EN">
<HTML>
  <HEAD>
    <META HTTP-EQUIV="Content-Language" CONTENT="ru">
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf8">
    <META NAME=Generator CONTENT="FAR 3.0">
    <META NAME=Author CONTENT="Александр Малыгин">
    <META NAME=Description CONTENT="Тестовое задание на вакансию веб-разработчик. Страница стены сообщений"
   <TITLE>The Wall of Messages</TITLE>
   <STYLE TYPE="text/css">
     body {text-align: justify; font-style: normal; font-family: Arial, Verdana, Helvetica, sans-serif; font-size: 12pt;
           background-color: white; }
     table {text-align: left; border: 1px solid grey}
     td,th {padding-left: 5px; padding-right: 5px; border: 1px solid silver}
     /* специальные маркеры для списка, вместо стандартных */ 
     li { list-style: none; }
     li::before { padding-right: 1em; }
     li.msg0::before { content: '\25ba'; }
     li.msg1::before { content: '\25bc'; }
     li.cmnt::before { content: '\25a4'; }
   </STYLE>
</HEAD>
<script language=jscript>
  // переход к редактору по кнопке Comment
  function GoAnswer(id, hint) {
    scroll(0,0);
    document.msginput.msgpid.value=id;
    document.msginput.msgtext.focus();
    document.getElementById("inplabeltext").innerHTML="Comment to: "+hint;
  }

  // обеспечение перехода к новому сообщению после его добавления
  document.addEventListener("DOMContentLoaded", function() {
    var mid=document.msginput.msgid.value; // здесь PHP код положил имя якоря
    if (mid) document.location.hash = mid;
  } )

</script>
<BODY>

<table width=100% border=0><tr bgcolor="Black">
  <td align=center><font color="White" size="12" weight="bold" face="Times New Roman">THE WALL</font></td>
</tr></table>

<?php
if ($uid): // пользователь вошёл в систему

echo "Current user: $uname";

$txt = $_REQUEST['msgtext']; // текст нового сообщения
$pid = $_REQUEST['msgpid'];  // null для первичных сообщений, ParentID для комментариев
if (isset($txt)) $mid = AddMessage($pid, $uid, $txt); // свежее сообщение добавлено в базу
?>

<!-- форма ввода нового сообщения  -->
<form name="msginput" method="post">
  <p name='inplabel' id='input'><div id='inplabeltext'>Message</div><br> <!-- метка для редактора -->
  <textarea name="msgtext" cols=80 rows=5 placeholder="Input your message" maxlength=1024 required></textarea>
  <input type='hidden' name='msgpid'> <!--  скрытый параметр для формы ввода, ParentID в дереве -->
  <input type='hidden' name='msgid' value='<?=$mid?>'> <!-- скрытый параметр для JavaScript, имя якоря нового сообщения -->
  <input type="submit" value="Send">
  <input type="reset"  value="Clear"></p>
</form>

<?php else: // пользователь не вошёл в систему  ?>

<p>To add and comment on messages please <a href='auth'>log in</a></p>

<?php endif; ?>

<hr>

<?php 

// собственно вывод иерархической структуры сообщений, данной нам в ощущения
function ShowTree($tree, $level=0) { 
global $uid;

  if (count($tree)) {
    if ($level) echo("<ul>"); // первый уровень - без отступа
    foreach($tree as $row) {  // цикл по детям узла

      $id = $row["id"];
      $dt = $row["createstamp"];
      $cap = "#$id $dt <b>{$row['name']}</b>"; // метка для сообщения и для редактора при ответе
      $com = $row['comments'];                 // массив комментариев
      // разные маркеры для разных случаев
      if ($level) $liclass = "cmnt";           // комментарий
      else if (count($com)) $liclass = "msg1"; // первичное сообщение, есть комментарии
      else $liclass = "msg0";                  // первичное сообщение, нет комментариев

      echo("<a name='$id'></a>");              // якорь для сообщения
      echo("<li class=$liclass>$cap");
      echo("<p><pre>{$row['text']}</pre>");    // текст сообщения
      // кнопка ответа, переходит к редактору
      if ($uid) echo("<button name='answer' value=$id onClick=\"GoAnswer($id, '$cap')\">Comment</button>");
      echo("</p></li>");
      ShowTree($com, $level+1);                // всё то же самое для детей данного узла
    }
    if ($level) echo("</ul>");
  }
}

// построение дерева из БД, параметр работает, но пока не актуален (в данной версии)
$tree = BuildTree($_REQUEST['id']);
// вывод дерева
ShowTree($tree);

$db->close();

?> 
<a href="#">Top</a>
</BODY>
</HTML>
