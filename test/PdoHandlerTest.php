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
require_once(dirname(__FILE__).'/config.php');
require_once(ROOT_PATH.'/PdoHandler.php');
require_once(ROOT_PATH.'/PdoHandlerImpl.php');
require_once (ROOT_PATH.'/OutletSchemaBuilderException.php');
define("SQLITE_DB_PATH", sys_get_temp_dir().'/dummydb.sqlite');
class PdoHandlerTestCase extends PHPUnit_Framework_TestCase{
   const DB_PATH = SQLITE_DB_PATH;
   private $connectionArray;
   function setUp(){
      if(is_file(self::DB_PATH)){
	 unlink(self::DB_PATH);
      }
      $this->connectionArray = array('dsn' => 'sqlite:'.self::DB_PATH);
   }

   function testImplementsPDO(){
      $pdoHandler = PdoHandlerImpl::createWithConnectionArray($this->getConnectionArray());
      $this->assertTrue($pdoHandler instanceof PDO);
   }

   function testImplementsPdoHandler(){
      $pdoHandler = PdoHandlerImpl::createWithConnectionArray($this->getConnectionArray());
      $this->assertTrue($pdoHandler instanceof PdoHandler);
   }



   function testUsesConnectionArrayToInitializesPdo(){
      $pdoHandler = PdoHandlerImpl::createWithConnectionArray($this->getConnectionArray());
      $result = $pdoHandler->exec('begin;create table info (id integer primary key, name text);commit');
      if($result === false){
	 $this->fail($pdoHandler->errorInfo());
      }
   }

   function testConstructorCanNotBeCalledDirectly(){
      try{
	 $pdoHandler = new PdoHandlerImpl($this->getConnectionArray());
	 $this->fail("Expects an OutletSchemaBuilderException to be thrown");
      }catch(OutletSchemaBuilderException $e){
	 $this->assertEquals(OutletSchemaBuilderException::ERROR_CONSTRUCTOR_LOCKED, $e->getPattern());
      }
   }

   function testGetUsername(){
      $localConnectionArray = $this->getConnectionArrayWithUser();
      $pdoHandler = PdoHandlerImpl::createWithConnectionArray($localConnectionArray);
      $this->assertEquals($localConnectionArray['username'], $pdoHandler->getUsername());      
   }

   function testGetPassword(){
      $localConnectionArray = $this->getConnectionArrayWithCredentials();
      $pdoHandler = PdoHandlerImpl::createWithConnectionArray($localConnectionArray);
      $this->assertEquals($localConnectionArray['password'], $pdoHandler->getPassword());      
   }

   function testGetDsn(){
      $localConnectionArray = $this->getConnectionArray();
      $pdoHandler = PdoHandlerImpl::createWithConnectionArray($localConnectionArray);
      $this->assertEquals($localConnectionArray['dsn'], $pdoHandler->getDsn());      
   }

   function getConnectionArrayWithCredentials(){
      $connectionArray = $this->getConnectionArrayWithUser();
      $connectionArray['password'] = 'userpass';
      return $connectionArray;
   }

   function getConnectionArrayWithUser(){
      $connectionArray = $this->getConnectionArray();
      $connectionArray['username'] = 'user';
      return $connectionArray;
   }

   function getConnectionArray(){
      return $this->connectionArray;
   }
}