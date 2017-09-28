<?php

$db = new mysqli('localhost', 'root', '', 'thewall');
if ($db->connect_errno) die($db->connect_error);

global $db;

function UpdateUser($sid, $name) {
global $db;
//  $sql = "INSERT INTO user (SocID, Name) VALUES ('$sid', '$name') ON DUPLICATE KEY UPDATE Name='$name'";
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

function GetUserName($uid) {
global $db;
  if ($qr = $db->query("SELECT Name FROM User WHERE ID=$uid"))
    return $qr->fetch_array()[0];
  else return false;
}

function BuildTree($pid=null) { 
global $db;

  $SQL="SELECT m.id,m.parentid,m.userid,m.createstamp,u.name,m.text FROM message as m, user as u WHERE m.userid=u.id AND m.parentid ".(is_null($pid)? "is null ORDER BY ID DESC" : "= $pid ORDER BY CreateStamp");
  $qr = $db->query($SQL);
  $result = array();
  try {
//    if ($qr->num_rows)
    while ( $row = $qr->fetch_assoc() ) {
      $row['comments'] = BuildTree($row['id']); 
      $result[] = $row;
    }
  } finally {
    $qr->free();
  }
  return $result;
}

function AddMessage($id, $uid, $txt) {
global $db;
  if (!$id) $id='null';
  $sql = "INSERT INTO message (parentid, userid, text) VALUES ($id, $uid, '$txt')";
  if (!$db->query($sql)) echo $db->error; //debug!!!
  return $db->insert_id;
}

?>