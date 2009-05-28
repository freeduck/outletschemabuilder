<?php
  //$test = "PDO";
//$db = new $test;
  //class MyClass{
   //}
class test extends PHPUnit_Framework_TestCase{
   function test(){
      $pdoMock = $this->getMock('PDO',array(), array(), '', false, false);
      $classInsight = new ReflectionObject($pdoMock);
      $name = $classInsight->getName();
      $test = new $name;
      var_dump($test instanceof PDO);
   }
}