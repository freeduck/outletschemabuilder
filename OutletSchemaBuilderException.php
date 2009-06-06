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

class OutletSchemaBuilderException extends  Exception{

   const ERROR_CONSTRUCTOR_LOCKED = 'You need to call the create method';
   const ERROR_NO_FILE_NAME_GIVEN='You need to give a file name, empty strings are not allowed';
   private $pattern;
   private $parameters;

   public static function createWithPattern($pattern){
      $exception = new OutletSchemaBuilderException();
      $args = func_get_args();
      $exception->pattern = array_shift($args);
      $exception->parameters = $args;
      $exception->initializeWithPatternAndParameters();
      return $exception;
   }

   protected function initializeWithPatternAndParameters(){
      parent::__construct($this->buildMessage());
   }

   protected function buildMessage(){
      return vsprintf($this->pattern, $this->parameters);
   }

   function getPattern(){
      return $this->pattern;
   }
}