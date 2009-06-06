<?php
interface DatabaseDescriptor{   
   function getConnectionArray();
   function getTableNames();
   function showCreateTable($tableName);
}