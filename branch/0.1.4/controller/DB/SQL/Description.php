<?php
/*
 * Mozilla Public License
 * 
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * The Original Code is OOSpores content management framework, released August 2nd, 2010.
 * The Initial Developer of the Original Code is Antoine Berranger.
 * 
 * Portions created by Antoine Berranger are Copyright (C) 2010 Antoine Berranger
 * All Rights Reserved.
 * 
 * Contributor(s): ____________________.
 *
 * Alternatively, the contents of this file may be used under the terms
 * of the GPL license (the  "[GPL] License"), in which case the
 * provisions of [GPL] License are applicable instead of those
 * above.  If you wish to allow use of your version of this file only
 * under the terms of the [GPL] License and not to allow others to use
 * your version of this file under the MPL, indicate your decision by
 * deleting  the provisions above and replace  them with the notice and
 * other provisions required by the [GPL] License.  If you do not delete
 * the provisions above, a recipient may use your version of this file
 * under either the MPL or the [GPL] License.
 */

/**
 * Description of the databases from XML to SQL.
 * It mainly implements the generateDatabases method that creates every tables needed depending those XML informations.
 * 
 * @package	Oos_DB
 * @subpackage	SQL
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_DB_SQL_Description extends Oos_DB_Common_Description 
{
	/**	string	On what type of database we are. Maybe idiot, but it's saving time and ressources */
	protected $_db_type = "SQL"; 
	
	/**
	 * Generates or updates tables and fields depending on XML Data.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @throws	Oos_XML_Exception	Database xml file(s) is(are) not correctly formatted
	 */	
	public function generateDatabases() 
	{
		if(!$this->checkXML())
		{
			throw new Oos_XML_Exception("Database xml file(s) is(are) not correctly formatted", OOS_E_FATAL);
		}
		
		$db = Oos_DB::connect($this->_db_type);
		
		// deletion of useless tables
		$request = "SHOW tables";
		$rs = mysql_query($request);
		
		while($table = mysql_fetch_array($rs)) 
		{
			$tableCode = $table[0];
			if(!$this->getTable($tableCode)) 
			{
				$request = "DROP TABLE ".$tableCode; 
				mysql_query($request);				
			}
		}
		
		// adding fields and / or tables
		$xml_tables	= $this->getTables();
		
		foreach($xml_tables as $table) 
		{
			$request = "SHOW tables LIKE '".$table->getCode()."'";
			$rs = mysql_query($request);
			
			// if table does not exist we gotta create it
			if(mysql_num_rows($rs) == 0) 
			{
				// creating table...
				$request = "
					CREATE TABLE `".$table->getCode()."` (
						`".$table->getCode()."_ID` INT NOT NULL AUTO_INCREMENT ,
						UNIQUE (
							`".$table->getCode()."_ID`
						)
					) TYPE = MYISAM;
				";
				mysql_query($request);
				
				// ... and its fields
				foreach($table->getFields() as $field) 
				{
					$request = "
						ALTER TABLE `".$table->getCode()."` 
						ADD `".$field->getCode()."` ".$field->getSqlType().";
					";
					mysql_query($request);
				}
			} 
			
			// else we gotta check if we gotta update it
			else 
			{
				// removing old fields
				$request = "SHOW COLUMNS FROM ".$table->getCode();
				$rs = mysql_query($request);
						
				if($rs)
				while($column = mysql_fetch_assoc($rs)) 
				{
					// looking for this field
					$hasField = $table->hasField($column["Field"]);

					if($hasField) 
					{ 
						continue;
					} 
					
					// if not found, it's going to the trash
					else 
					{
						$request = "
							ALTER TABLE `".$table->getCode()."` 
							DROP `".$column["Field"]."`
						";
						$res = mysql_query($request);				
					}
				}
				
				// adding new fields
				foreach($table->getFields() as $field) 
				{
					// looking for it in the database
					$request = "SHOW COLUMNS FROM ".$table->getCode()." LIKE '".$field->getCode()."';";
					$rs = mysql_query($request);
					
					// it does not exists, we create it !
					if(!mysql_fetch_array($rs)) 
					{
						$request = "
							ALTER TABLE `".$table->getCode()."` 
							ADD `".$field->getCode()."` ".$field->getSqlType().";
						";
						mysql_query($request);
					}
				}				
			}
			
			// we are done with that table, we move to the next
			continue;
		}
	}
}