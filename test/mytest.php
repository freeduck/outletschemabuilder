<?php
class MyClass{
   function doIt(){
      var_dump("## DO IT ##");
   }
   function keepOnDoingIt(){
      var_dump("## KEEP ON DOING IT");
   }
   function run(){
      var_dump("## OK ##");
   }
}
class Mytestcase extends PHPUnit_FrameWork_TestCase{
   function testMyMock(){
      $mock = $this->getMock("MyClass", array("t"));
      $mock->expects($this->once())
	 ->method('run');
      $mock->run();
      $mock->doIt();
      $mock->keepOnDoingIt();
   }
}