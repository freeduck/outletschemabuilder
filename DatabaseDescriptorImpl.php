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
class DatabaseDescriptorImpl implements DatabaseDescriptor{
   private $pdoHandler;
   
   private function __construct(){
   }

   public static function createWithPdoHandler(PdoHandler $pdoHandler){
      $descriptor = new DatabaseDescriptorImpl();
      $descriptor->initializeWithPdoHandler($pdoHandler);
      return $descriptor;
   }

   function initializeWithPdoHandler(PdoHandler $pdoHandler){
      $this->pdoHandler = $pdoHandler;
   }

   function getConnectionArray(){
      $connectionArray = array();
      $connectionArray['dsn'] = $this->pdoHandler->getDsn();
      $connectionArray['username'] = $this->pdoHandler->getUsername();
      $connectionArray['password'] = $this->pdoHandler->getPassword();
      return $connectionArray;
   }

   function getTableNames(){
      $tableNames = array();
      $result = $this->pdoHandler->query("SHOW TABLES");
      foreach($result as $row){
	 $tableNames[] = $row[0];
      }
      return $tableNames;
   }

   function showCreateTable($tableName){
      $this->assertTableName($tableName);
      $result = $this->pdoHandler->query("SHOW CREATE TABLE ".$tableName);
      $row = $result->fetch();
      return $row["Create Table"];
   }

   protected function assertTableName($tableName){
      if(strlen($tableName) == 0){
	 throw OutletSchemaBuilderException::createWithPattern(OutletSchemaBuilderException::ERROR_NO_FILE_NAME_GIVEN);
      }
   }
}