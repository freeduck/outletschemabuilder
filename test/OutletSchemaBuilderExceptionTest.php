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
require_once (ROOT_PATH.'/OutletSchemaBuilderException.php');
class OutletSchemaBuilderExceptionTestCase extends PHPUnit_Framework_TestCase{
   function testLockedConstructorMessage(){
      $exception = OutletSchemabuilderException::createWithPattern(OutletSchemaBuilderException::ERROR_CONSTRUCTOR_LOCKED);
      $this->assertEquals('You need to call the create method', $exception->getMessage());
   }

   function testNoFilaNameGiven(){
      $exception = OutletSchemaBuilderException::createWithPattern(OutletSchemaBuilderException::ERROR_NO_FILE_NAME_GIVEN);
      $this->assertEquals('You need to give a file name, empty strings are not allowed', $exception->getMessage());
   }
}