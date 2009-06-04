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

class PdoHandlerImpl extends PDO implements PdoHandler{
   private $dsn;
   private $username;
   private $password;

   private static $legalConstructorCall;
   function __construct(){
      if(!self::$legalConstructorCall){
	 throw OutletSchemaBuilderException::createWithPattern(OutletSchemaBuilderException::ERROR_CONSTRUCTOR_LOCKED);
      }
   }
   public static function createWithConnectionArray($connectionArray) {
      self::$legalConstructorCall = true;      
      $class = __CLASS__;
      $handler = new $class;
      $handler->initializeWithConnectionArray($connectionArray);
      self::$legalConstructorCall = false;      
      return $handler;
   }

   function initializeWithConnectionArray($connectionArray){
      parent::__construct($connectionArray['dsn']);
   }

}