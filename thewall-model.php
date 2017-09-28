<?php
/**
 * Модель для проекта Стена сообщений (The Wall of Messages)
 * Проект разработан в порядке выполнения тестового задания на вакансию веб-разработчик в компании Light IT
 *
 * Copyright (C) 2017 Alexander Malygin
 *
 */

$db = new mysqli('localhost', 'root', '', 'thewall');
if ($db->connect_errno) die($db->connect_error);

global $db;

// Добавление пользователя в БД, на основе информации из соцсети
// Если такой уже есть, обновляется его имя
// Возврат ID пользователя в таблице User
function UpdateUser($sid, $name) {
global $db;
  $sql = "SELECT ID,Name FROM User WHERE SocID=$sid";
  if (!$qr = $db->query($sql)) echo $db->error; //debug!!!
  if ($row = $qr->fetch_assoc()) {
    if ( $row['Name'] != $name ) $db->query("UPDATE User SET Name='$name' WHERE ID={$row['ID']}");
    return $row['ID'];
  } else {
    $db->query("INSERT INTO User (SocID, Name) VALUES ('$sid', '$name')");
    return $db->insert_id;
  }
}

// Получение имени пользователя по его ID
function GetUserName($uid) {
global $db;
  if ($qr = $db->query("SELECT Name FROM User WHERE ID=$uid"))
    return $qr->fetch_array()[0];
  else return false;
}

// Построение иерархической структуры списка сообщений в виде вложеннывх массивов
// Одна запись содержит значения id,parentid,userid,createstamp,name,text из текущей выборки
// и массив comments из рекурсивного вызова функции
function BuildTree($pid=null) { 
global $db;
  // первичные сообщения (parentid is null) выводятся в обратном порядке, комментарии в хронологическом
  $SQL="SELECT m.id,m.parentid,m.userid,m.createstamp,u.name,m.text FROM message as m, user as u WHERE m.userid=u.id AND m.parentid ".
    (is_null($pid)? "is null ORDER BY ID DESC" : "= $pid ORDER BY CreateStamp");
  $qr = $db->query($SQL);
  $result = array();
  try {
    while ( $row = $qr->fetch_assoc() ) {
      $row['comments'] = BuildTree($row['id']); 
      $result[] = $row;
    }
  } finally {
    $qr->free();
  }
  return $result;
}

// Добавление нового сообщения
// Возврат ID новой записи в таблице Message
function AddMessage($id, $uid, $txt) {
global $db;
  if (!$id) $id='null';
  $sql = "INSERT INTO message (parentid, userid, text) VALUES ($id, $uid, '$txt')";
  if (!$db->query($sql)) echo $db->error; //debug!!!
  return $db->insert_id;
}

?>
