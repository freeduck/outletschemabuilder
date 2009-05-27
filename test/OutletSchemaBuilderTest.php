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
require_once (SHELLS_PATH.'/DatabaseDescriptor.php');
require_once (ROOT_PATH.'/OutletSchemaBuilder.php');
class OutletSchemaBuilderTestCase extends PHPUnit_Framework_TestCase{
   function setUp(){
      $this->tableArray = array('member', 'player');

      $this->connectionArray = array('dsn' => 'mysql:host=localhost;dbname=rvunited',
				     'username' => 'root',
				     'password' => 'rootpass',
				     'dialect'  => 'mysql');

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
   function testGivenADatabaseObjectTheBuilderReturnsAOutletSchemaArray(){
      $builder = OutletSchemaBuilder::createWithDatabase($this->getDatabaseMock());
      $schema = $builder->createSchema();
      $this->assertEquals($this->connectionArray, $schema['connection']);
      $this->assertEquals(2, count($schema['classes']));
      $classNameArray = array_keys($schema['classes']);
      $this->assertTrue(in_array('Member', $classNameArray), "Member is not in class schema");
      $this->assertTrue(in_array('Player', $classNameArray), "Player is not in class schema");
      $this->assertMember($schema['classes']['Member']);
      $this->assertPlayer($schema['classes']['Player']);
   }

   function getDatabaseMock(){
      $database = $this->getMock('DatabaseDescriptor');

      $database->expects($this->once())->
	 method('getConnectionArray')->
	 will($this->returnValue($this->connectionArray));

      $database->expects($this->once())->
	 method('getTables')->
	 will($this->returnValue($this->tableArray));

      $database->expects($this->exactly(2))->
	 method('showCreateTable')->
	 will($this->returnCallback(array($this,'showCreateTable')));
      return $database;
   }

   protected function assertMember($memberArray){
      $this->assertEquals('member', $memberArray['table']);
      $this->assertEquals(array('id', 'int', array('pk'=>true, 'autoIncrement'=>true)), $memberArray['props']['id']);
      $this->assertEquals(array('name', 'varchar'), $memberArray['props']['name']);
      $this->assertEquals(array('surname', 'varchar'), $memberArray['props']['surname']);
      $this->assertEquals(array('description', 'varchar'), $memberArray['props']['description']);
      $this->assertEquals(array('image_path', 'varchar'), $memberArray['props']['imagePath']);
      $this->assertEquals(array('created_at', 'datetime'), $memberArray['props']['createdAt']);
      $this->assertEquals(array('updated_at', 'datetime'), $memberArray['props']['updatedAt']);
      $this->assertUseSettersAndGetters($memberArray);
   }

   protected function assertUseSettersAndGetters($classArray){
      $this->assertEquals(true, $classArray['useGettersAndSetters']);
   }

   protected function assertPlayer($playerArray){
      $this->assertEquals('player', $playerArray['table']);
      $this->assertEquals(array('member_id', 'int', array('pk'=>true, 'autoIncrement'=>true)), $playerArray['props']['memberId']);
      $this->assertEquals(array('number', 'int'), $playerArray['props']['number']);
      $this->assertEquals(array(array('one-to-one', 'memberId')), $playerArray['associations']);
   }

   function showCreateTable($tableName){
      $tableDefinition = $this->playerDefinition;
      if($tableName == 'member'){
	 $tableDefinition = $this->memberDefinition;
      }
      return $tableDefinition;
   }
}