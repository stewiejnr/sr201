<?php
error_reporting(E_ERROR | E_PARSE);

//Command filename is generated from the hashed serial and password
$device = md5($_POST['serial'] . $_POST['password']);
$file = './devices/' . $device .'_cmd';
$current = file_get_contents($file);

if($current == FALSE)
{
  //device not registered
  http_response(400);
  echo json_encode({'code': 'NONEXISTENT'});
}
elseif($current == "\"A\"")
{
  http_response_code(500);
  echo json_encode({'code': 'NOTREADY'});
}
elseif($_POST['operation']=="Operate")
{
  $action="\"A" . $_POST['action'] .  $_POST['channel'] . $_POST['timeout']."\"";
  //Write the action to the file
  $file_write_result = file_put_contents($file, $action);
  if($file_write_result != FALSE {
    sleep(4);
    $file_read_result = file_get_contents('./devices/' . $device . "_sta");
    if($file_read_result == "\"A\"")
    {
      echo json_encode({'code': 'PROCESSED'});
    }
    else {
      echo json_encode({'code': 'NOTPROCESSED'});
    }
  }
  else {
    echo json_encode({'code': 'CMDNOTSENT'});
  }
  
  
  echo json_encode({'code': 'PROCESSED'});
} 
else 
{
  echo json_encode({'code': 'STATUS', 'state': file_get_contents('./devices/' . $device . "_sta") });
}

//if($current != "\"A\"" && $current!=FALSE)
//{
//    //We only accept new commands if the previous one was processed by the device
//    $result = 'Device ' . $device .  ' is not ready to take action!' . $current;
//}
//elseif($_POST['submit']=="Apply")
//{
//    $action="\"A" . $_POST['action'] .  $_POST['channel'] . $_POST['timeout']."\"";
//    // Write the action to the file
//    file_put_contents($file, $action);
//   $result = 'Device ' . $device .  ' will receive the following command: ' . $action;
//}
//else
//{
//    $result = 'Device ' . $device .  ' status query only.'; 
//}

$logfile='./logs/clients.log';
$content=date('Y-m-d H:i:s') . ': ' . $_SERVER['REMOTE_ADDR']. ' ' . $result . "\n";
file_put_contents($logfile, $content, FILE_APPEND | LOCK_EX);

?>
