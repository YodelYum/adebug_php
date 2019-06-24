<?php
error_reporting(E_ALL);

session_start();
$_SESSION['blub'] = 'bla';
$_SESSION['dasdas'] = 'two';

$string = (string)"dasdas";
var_dump($string);

class adebugger{
  public $server_ip;
  public $server_port;
  public $message;
  public $socket;

  private $debug_messages;

  private $debug_package_id;
  private $debug_package_time;



  function __construct($ip = '127.0.0.1', $port = 80){
       $this -> debug_package_id = sha1(time());
       $this -> debug_package_time = time();
       $this -> server_ip = $ip;
       $this -> server_port = $port;
       $this -> socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  }

  function testFunction(){
      $this -> message = "[".__LINE__."] ".__METHOD__."()";
  }

  function __destruct() {
       //$this -> debug(debug_backtrace(), "complete backtrace");
       $output = $this -> debug_messages_pack($this -> debug_messages);
       socket_sendto($this ->socket, $output, strlen($output),
                     0, $this -> server_ip, $this -> server_port);
          socket_close($this -> socket);
   }

   function debug($message, $comment=""){ //adds a debug-message to the package in $debug_messages
        $backtrace = debug_backtrace();
        $new_debug_message = [];
        $new_debug_message['time'] = time();
        $new_debug_message['line'] = $backtrace[0]['line'];
        $new_debug_message['file'] = $backtrace[1]['file'];
        $new_debug_message['method'] = $backtrace[1]['function']."@".debug_backtrace()[1]['line'];
        $new_debug_message['message'] = $message;
        $new_debug_message['formattedMessage'] = gettype($message);
        $new_debug_message['comment'] = $comment;
        $new_debug_message['type'] = gettype($message);
        array_shift($backtrace);
        foreach ($backtrace as $value){
             $stacktrace[] =$value['function'];
        }
        $new_debug_message['stacktrace'] = $stacktrace;
        $this -> debug_messages[] = $new_debug_message;
        print_r($new_debug_message);
       // echo $new_debug_message['file'];
   }

   function debug_messages_pack($data){ //packs all the $debug_messages to a json-object
        $buffer = json_encode($data);
        return $buffer;

   }
}

function blub(){
     $adebug = new adebugger();
     $adebug -> debug("text", __LINE__);
     $adebug -> debug($_SESSION, 'backtrace');
     $adebug -> debug("text", 'backtrace');
}

blub();







 ?>
