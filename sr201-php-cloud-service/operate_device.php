<?php
error_reporting(E_ERROR | E_PARSE);

//Command filename is generated from the hashed serial and password
$device = md5($_POST['serial'] . $_POST['password']);
$file = './devices/' . $device .'_cmd';
$current = file_get_contents($file);

if($current == FALSE)
{
  //device not registered
  http_response_code(400);
  echo json_encode(array('code' => 'NONEXISTENT'));
}
elseif(!preg_match('/^["A"]{3}/', $current))
{
  http_response_code(500);
  echo json_encode(array('code' => 'NOTREADY'));
}
elseif($_POST['operation']=="Operate")
{
  $action="\"A" . $_POST['action'] .  $_POST['channel'] . $_POST['timeout']."\"";
  //Write the action to the file
  $file_write_result = file_put_contents($file, $action);
  if($file_write_result != FALSE) {
    sleep(4);
    $file_read_result = file_get_contents('./devices/' . $device . "_cmd");
    if($file_read_result == "\"A\"")
    {
      http_response_code(200);
      echo json_encode(array('code' => 'PROCESSED'));
    }
    else {
      http_response_code(500);
      echo json_encode(array('code' => 'NOTPROCESSED'));
    }
  }
  else {
    http_response_code(500);
    echo json_encode(array('code' => 'CMDNOTSENT'));
  }
  
} 
else 
{
  $status_value = file_get_contents('./devices/' . $device . "_sta");
  echo json_encode(array('code' => 'STATUS', 'state' => $status_value ));
}

$logfile='./logs/clients.log';
$content=date('Y-m-d H:i:s') . ': ' . $_SERVER['REMOTE_ADDR']. ' ' . $result . "\n";
file_put_contents($logfile, $content, FILE_APPEND | LOCK_EX);

?>
