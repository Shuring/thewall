<?php
/**
 * Страница авторизации для проекта Стена сообщений (The Wall of Messages)
 * Проект разработан в порядке выполнения тестового задания на вакансию веб-разработчик в компании Light IT
 *
 * Copyright (C) 2017 Alexander Malygin
 *
 */
require_once("fbauth.php");
require_once("thewall-model.php");

// объект авторизации на Facebook
$fb = new FBAuth(array( // параметры авторизации
  "client_id"     => "121474638496991",
  "client_secret" => "9b600471ac1fe1dde32738b24540bf47",
  "redirect_uri"  => "http://". $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0]
));

// обратная связь от Facebook API
if (isset($_GET["code"])) $fb->auth($_GET["code"]);

if ($fb->auth_status) { // успешная авторизация
  // имя пользователя для стены
  $name = implode(' ', array($fb->user_info["first_name"], $fb->user_info["last_name"]));
  // ID пользователя в базе
  $uid = UpdateUser($fb->user_info["id"], $name);
  // перейти на стену
  header("Location: show?uid=$uid");
  exit;
}
?>

<!-- Авторизация не состоялась, надо показать страницу авторизации -->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0//EN">
<HTML>
  <HEAD>
    <META HTTP-EQUIV="Content-Language" CONTENT="ru">
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf8">
    <META NAME=Generator CONTENT="FAR 3.0">
    <META NAME=Author CONTENT="Александр Малыгин">
    <META NAME=Description CONTENT="Тестовое задание на вакансию веб-разработчик. Страница авторизации"
   <TITLE>The Wall of Messages - Authentication</TITLE>
   <STYLE TYPE="text/css"><!--
     body {text-align: justify; font-style: normal; font-family: Arial, Verdana, Helvetica, sans-serif; font-size: 12pt;
           background-color: white; }
     table {text-align: center; border: 1px solid grey; margin-left: auto; margin-right: auto}
     td { border-collapse:collapse;
          padding: 3px; border:4px groove buttonshadow; }
     #floater {
       float: left;
       height: 30%;
       width: 100%;
     }
   </STYLE>

   <script type="text/javascript">
// скрипт для определения высоты экрана с целью центровки по вертикали
     function findDimensions()
     {
       var height = 0;
       if (window.innerWidth) height = window.innerHeight;
       else if (document.body && document.body.clientWidth) height = document.body.clientHeight;
       var h = document.getElementById("layer");
       if (h) h.style.height = height -40 + "px";
     }
     if (window.addEventListener) window.addEventListener("load", findDimensions, false);
     else if (window.attachEvent) window.attachEvent("onload", findDimensions);
   </script>

</HEAD>
<BODY>

<div id="layer" style="width:100%; border:2px solid;">
  <div id="floater"></div>
  <table width=300><tr>
  <td bgcolor="#ccddff"><b>
<!-- Ссылка на Фейсбук для авторизации -->
  <a href="<?=$fb->get_link()?>">Facebook</a>
  </b></td>
  <td>Google+</td>
  <td>Vk.com</td>
  </tr></table>
</div>

</BODY></HTML>