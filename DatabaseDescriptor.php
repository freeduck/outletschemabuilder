<?php
class DatabaseDescriptor{
   private $pdoHandler;
   
   private function __construct(){
   }

   function createWithPdoHandler(PdoHandler $pdoHandler){
      $this->pdoHandler = new $pdoHandler;
   }

   function getConnectionArray(){
   }

   function getTables(){
   }

   function showCreateTable(){
   }
}