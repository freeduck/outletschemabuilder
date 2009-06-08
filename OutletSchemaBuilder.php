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

class OutletSchemaBuilder{
   private $database;
   private $dialect;
   private $schema;
   private $tableDefinition;
   private $tableName;
   private $className;
   private $mysqlOutletTypeMap;

   function createWithDatabase($database, $dialect){
      $builder = new OutletSchemaBuilder();
      $builder->initializeWithDatabase($database, $dialect);
      return $builder;
   }

   protected function initializeWithDatabase($database, $dialect){
      $this->database = $database;
      $this->dialect = $dialect;
      $this->schema = array();
      $this->tableDefinition = array();
      $this->mysqlOutletTypeMap = array('varchar' => 'varchar', 'text' => 'varchar', 'int' => 'int', 'datetime' => 'datetime');
   }
   
   function createSchema(){
      $this->addConnection();
      $this->addClassDefinitions();
      return $this->schema;
   }

   function addConnection(){
      $this->schema['connection'] = $this->database->getConnectionArray();
      $this->schema['connection']['dialect'] = $this->dialect;
   }

   function addClassDefinitions(){
      $tableNames = $this->database->getTableNames();
      foreach($tableNames as $tableName){
	 $tableDefinitionString = $this->database->showCreateTable($tableName);
	 $this->parseTableDefinitionString($tableDefinitionString);
	 $this->addClassName();
	 $this->setUseSettersAndGetters();
	 $this->setTableName();
	 $this->addColumns();
      }
   }
   function parseTableDefinitionString($tableDefinitionString){
      $str = str_replace("\r", "", str_replace("\n", "", $tableDefinitionString));

      $nameEnd = strpos($str, "(");
      $namePart = substr($str, 0, $nameEnd);
      $definition = substr($str, $nameEnd + 1);
      $nameParts = explode('(', $str);

      $metaStart = strrpos($definition, ")");
      $columnDefinitions = substr($definition, 0, $metaStart);
      $metaDefinitions = substr($definition, $metaStart + 1);
      $columnMetaParts = explode(")", $nameParts[1]);
      $this->tableDefinition = array_merge((array) $namePart, explode(",", $columnDefinitions)); 

   }

   function addClassName(){
    
      $this->tableName = $this->getTableName();
      $this->className = ucfirst(strtolower($this->tableName));
      $this->schema['classes'][$this->className] = array();
   }

   function setUseSettersAndGetters(){
      $this->schema['classes'][$this->className]['useGettersAndSetters'] = true;
   }

   function getTableName(){
      $nameLine = array_shift($this->tableDefinition);
      return $this->extractIdentifierName($nameLine);
   }

   function extractIdentifierName($contentLine){
      preg_match("/`([^`]+)`/", $contentLine, $matches);      
      return $matches[1];
   }

   function setTableName(){
      $this->schema['classes'][$this->className]['table'] = $this->tableName;
   }

   function addColumns(){
      if(count($this->tableDefinition) > 0){
	 $this->schema['classes'][$this->className]['props'] = array();
	 foreach($this->tableDefinition as $columnDefinition){
	    $columnLine = trim($columnDefinition);
	    if($columnLine[0] == '`'){
	       $this->addProperty($columnLine);
	    }
	    else{
	       $this->addExtra($columnLine);
	    }
	 }
      }
   }

   function addProperty($columnLine){
      $property = $this->extractIdentifierName($columnLine);
      $type = $this->translateType($this->extractType($columnLine));
      $this->schema['classes'][$this->className]['props'][$this->camelCase($property)] = array($property, $type);
   }

   function translateType($mysqlType){
      return $this->mysqlOutletTypeMap[$mysqlType];
   }

   function extractType($columnLine){
      $columnParts = explode(' ', $columnLine);
      $typePart = trim($columnParts[1]);
      $sizeStart = strpos($typePart, "(");
      if($sizeStart !== false){
	 $typePart = substr($typePart, 0, $sizeStart);
      }
      return $typePart;
   }

   function camelCase($identifierName){
      $identifierParts =explode('_', $identifierName);
      $camelCasedName = strtolower(array_shift($identifierParts));
      foreach($identifierParts as $namePart){
	 $camelCasedName .= ucfirst(strtolower($namePart));
      }
      return $camelCasedName;
   }

   function addExtra($columnLine){
      if(strpos($columnLine, 'PRIMARY KEY') === 0){
	 $pk = $this->extractIdentifierName($columnLine);
	 $this->schema['classes'][$this->className]['props'][$this->camelCase($pk)][] = array('pk'=>true, 'autoIncrement'=>true);
      }
      else{
	 $foreignStart = strpos($columnLine, "FOREIGN KEY");
	 $foreignEnd = strpos($columnLine, ")", $foreignStart);
	 $localAttPart = substr($columnLine, $foreignStart, $foreignEnd - $foreignStart);
	 $this->schema['classes'][$this->className]['associations'] = array(array(
										  'one-to-one', 
										  $this->extractIdentifierName($this->camelCase($localAttPart))));
      }
   }
}