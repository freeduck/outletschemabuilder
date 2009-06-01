<?php
require_once dirname(__FILE__).'/config.php';
require_once (ROOT_PATH.'/DatabaseDescriptor.php');
class DatabaseDesriptorTestCase extends PHPUnit_Framework_TestCase{
   function testGetTableNames(){
      $databaseDescriptor = DatabaseDescriptor::createWithPdoHandler($this->getPdoMock());
      $databaseDescriptio->getTableNames();
   }


   function getPdoMock(){
      $pdoMock = $this->getMock('PdoHandler',array(), array(), '', false, false);
      return $pdoMock;
   }
}