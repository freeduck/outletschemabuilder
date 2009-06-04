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


class DatabaseDesriptorTestCase extends PHPUnit_Framework_TestCase{
   function setUp(){
      $this->pdoMock = $this->getMock('PdoHandler');
      $this->memberDefinition ="CREATE TABLE `member` (
 `id` int(11) NOT NULL auto_increment,
\n
 `name` varchar(30) NOT NULL,
\r
 `surname` varchar(30) NOT NULL,
 `description` text,
 `image_path` varchar(120) default NULL,
 `created_at` datetime default NULL,
 `updated_at` datetime default NULL,
 PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1";

      $this->playerDefinition = "CREATE TABLE `player` (
 `member_id` int(11) NOT NULL,
 `number` int(11) default NULL,
 PRIMARY KEY  (`member_id`),
 CONSTRAINT `player_FK_1` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1";
   }

   function testGetTableNames(){
      $this->pdoMock->expects($this->exactly(1))
	 ->method('query')
	 ->will($this->returnCallback(array($this, 'handleTablesQuery')));
      $databaseDescriptor = DatabaseDescriptorImpl::createWithPdoHandler($this->getPdoMock());
      $tableNames = $databaseDescriptor->getTableNames();
      $this->assertEquals(2, count($tableNames));
      $this->assertTrue(in_array('member', $tableNames));
      $this->assertTrue(in_array('player', $tableNames));
   }

   function testShowCreateTable(){
      $this->pdoMock->expects($this->exactly(1))
	 ->method('query')
	 ->will($this->returnCallback(array($this, 'handleCreateMemberQuery')));
      $databaseDescriptor = DatabaseDescriptorImpl::createWithPdoHandler($this->getPdoMock());
      $this->assertEquals($this->memberDefinition, $databaseDescriptor->showCreateTable('member'));
   }

   function handleTablesQuery($query){
      $result = array();
      if("show tables" == strtolower($query)){
	 $result[] = array("Tables_in_mysql" => 'member');
	 $result[] = array("Tables_in_mysql" => 'player');
      }
      return $result;
   }

   function handleCreateMemberQuery($query){
      if(strpos(strtolower($query), "show create table member") !== false){
	 $pdoStatement = $this->getMock('PDOStatement', array(), array(), '', false);
	 $pdoStatement->expects($this->once())
	    ->method('fetch')
	    ->will($this->returnValue(array("Create Table" => $this->memberDefinition)));
      }
      return $pdoStatement;
   }

   function getPdoMock(){

      return $this->pdoMock;
   }
}