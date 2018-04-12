<?php

namespace gymadarasz\phpwebsocket;

<<<<<<< HEAD
class WebSocketUser implements WebSocketUserInterface {
=======
class WebSocketUser {
>>>>>>> aa9d22e90adae2250473770241ae1dc2eebb0d8c

  public $socket;
  public $id;
  public $headers = array();
  public $handshake = false;

  public $handlingPartialPacket = false;
  public $partialBuffer = "";

  public $sendingContinuous = false;
  public $partialMessage = "";
  
  public $hasSentClose = false;

  function __construct($id, $socket) {
    $this->id = $id;
    $this->socket = $socket;
  }
}