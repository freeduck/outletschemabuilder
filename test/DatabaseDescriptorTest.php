<?php
require_once dirname(__FILE__).'/config.php';
require_once (ROOT_PATH.'/DatabaseDescriptor.php');
class DatabaseDesriptorTestCase extends PHPUnit_Framework_TestCase{
   function testTheDatabaseDescriptorWillAcceptAnyDerivativeOfPDOAsConnectionClassName(){
      $pdoMock = $this->getMock('PDO',array(), array(), '', false, false);
      $pdoInsight = new ReflectionObject($pdoMock);
      $pdoMockClassname = $pdoInsight->getName();
      $databaseDescriptor = new DatabaseDescriptor(array(), $pdoMockClassname);
   }
}