<?php
/**
* Copyright 2009 Kristian Nygaard Jensen <freeduck@member.fsf.org>
* This file is part of Outletschemabuilder.
*
*    Outletschemabuilder is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    Outletschemabuilder is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
* 
*    You should have received a copy of the GNU General Public License
*    along with Outletschemabuilder.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once (dirname(__FILE__).'/config.php');
require_once (ROOT_PATH.'/DatabaseDescriptorImpl.php');
require_once (ROOT_PATH.'/PdoHandler.php');

class PdoHandlerMock implements PdoHandler{
   function query(){
   }
}

class DatabaseDesriptorTestCase extends PHPUnit_Framework_TestCase{
   function setUp(){
      $this->pdoMock = $this->getMock('PdoHandler',array(), array(), '', false, false);
   }

   function testGetTableNames(){
      $dbh = new PDO("mysql:host=localhost;dbname=mysql", "root", "rootpass");
      $result = $dbh->query("show tables");
      foreach($result as $row){
	 var_dump($row);
      }
      $this->pdoMock->expects($this->exactly(1))
	 ->method('query')
	 ->will($this->returnCallback(array($this, 'handleQueryCalls')));
      $databaseDescriptor = DatabaseDescriptorImpl::createWithPdoHandler($this->getPdoMock());
      $databaseDescriptor->getTableNames();
   }

   function handleQueryCalls($query){
      $result = array();
      if("show tables" == strtolower($query)){
	 $result[] = array("Tables_in_mysql", 'member');
	 $result[] = array("Tables_in_mysql", 'player');
      }
   }


   function getPdoMock(){

      return $this->pdoMock;
   }
}