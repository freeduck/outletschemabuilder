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
*    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

class OutletSchemaBuilder{
   private $database;
   private $schema;
   function createWithDatabase($database){
      $builder = new OutletSchemaBuilder();
      $builder->initializeWithDatabase($database);
      return $builder;
   }

   protected function initializeWithDatabase($database){
      $this->database = $database;
      $this->schema = array();
   }
   
   function createSchema(){
      $this->addConnection();
      $this->addClassDefinitions();
      //$this->tables = $this->database->getTables
   }

   function addConnection(){
      $this->schema['connection'] = $this->database->getConnectionArray();
   }

   function addClassDefinitions(){
      foreach($this->database->getTables() as $tableName){
	 var_dump($tableName);
	 $this->database->showCreateTable($tableName);
      }
   }
}