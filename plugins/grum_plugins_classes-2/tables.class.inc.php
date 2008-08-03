<?php 

/* -----------------------------------------------------------------------------
  class name: manage_tables
  class version: 1.3
  date: 2007-12-02
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------
  
  this class provides base functions to manage tables while plugin installation
    - constructor manage_tables($tables)
    - (public) function create_tables($tables_def)
    - (public) function update_tables_fields($tables_alteration)
    - (public) function drop_tables()
    - (public) function rename_tables($list)  -v1.1
    - (public) function tables_exists()  -v1.1
    - (public) function export($filename, $options, $tables, $infos)  -v1.3
    - (public) function multi_queries($queries)  -v1.3
    - (public) function import($filename)  -v1.3

  ------------------------------------------------------------------------------
  v1.1 + add rename_tables($list) function
       + add tables_exists() function
  v1.2 + add export($filename, $options, $tables) function
  v1.3 + modify export($filename, $options, $tables, $infos, $resultboolean) function
          + new parameters '$infos' allows to add some information on the
            exported file
          + add 'delete' and 'colnames' options
          + $resultbooelan option for return
       + add multi_queries($queries) function
       + add import($filename) function 

   -------------------------------------------------------------------------- */
class manage_tables
{
  var $tables;    //array of tables names
  var $version = "1.3";

  function manage_tables($tables)
  {
    $this->tables = $tables;
  }
  
  /* 
      create tables
      $tables_def is an array of SQL CREATE queries

      return true if everything is ok, otherwise tablename 
  */
  function create_tables($tables_def)
  {
    //deleting tables if exists
    $this->drop_tables();

    for($i=0;$i<count($tables_def);$i++)
    {
      $result=pwg_query($tables_def[$i]);
      if(!$result)
      {
        //if an error occurs, deleting created tables
        $this->drop_tables();
        return(false);
      }
    }
    return(true);
  }

  /* update tables definition 
     $tables_alteration : array of arrays
      example :
      $tables_alteration['table1']=array(
        "attribute1" => " ADD COLUMN `attribute1` text null default ''",
        "attribute2" => " ADD COLUMN `attribute2` text null default ''"));
      $tables_alteration['table2']=array(
        "attribute1" => " ADD COLUMN `attribute1` text null default ''",
        "attribute2" => " ADD COLUMN `attribute2` text null default ''"));

      return true if no error, otherwise return table.fields of error 
  */
  function update_tables_fields($tables_alteration)
  {
    if(!is_array($tables_alteration))
    {
      return('');
    }

    reset($tables_alteration);
    while (list($key, $val) = each($tables_alteration))
    {
      $sql="SHOW COLUMNS FROM $key";
      $result=pwg_query($sql);
      if($result)
      {
        $columns=array();
        while($row=mysql_fetch_assoc($result))
        { 
          array_push($columns, $row['Field']); 
        }

        reset($val);
        while (list($attname, $sql) = each($val))
        {
          if(!in_array($attname, $columns))
          {
            $result=pwg_query("ALTER TABLE `$key` ".$sql);
            if(!$result)
            {
              return($key.".".$attname);
            }
          }
        }
      }
    }
    return(true);
  }


  /* 
      delete tables listed in $this->tables_list
  */
  function drop_tables()
  {
    foreach($this->tables as $key => $table_name)
    {
      $sql="DROP TABLE IF EXISTS ".$table_name;
      $result=pwg_query($sql);
    }
  }

  /*
      rename tables name of list
        $list is an array('old_name' => 'new_name')
      return true if ok, else old table name
  */
  function rename_tables($list)
  {
    $tmplist=array_flip($this->tables);
    foreach($list as $key => $val)
    {
      if(isset($tmplist[$key]))
      {
        $this->tables[$tmplist[$key]] = $val;
        $sql="ALTER TABLE `$key` RENAME TO `$val`";
        if(!pwg_query($sql))
        {
          return($key);
        }
      }
      else
      {
        return($key);
      }
    }
    return(true);
  }

  /*
    return true if all listed tables exists
  */
  function tables_exists()
  {
    $list=array_flip($this->tables);
    $sql="SHOW TABLES";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=mysql_fetch_row($result))
      {
        if(isset($list[$row[0]]))
        {
          array_splice($list, $row[0],1);
        }
      }
    }
    if(count($list)>0)
    {
      return(false);
    }
    else
    {
      return(true);
    }
  }

  /*
    export all tables as SQL in a text file

    each query end with a " -- EOQ" ; it's just a method to increase parsing for
    import function

      $filename : name of the file
      $options : array of options like
                    array(
                      'drop' => true/false,  //add DROP TABLE statements
                      'create' => true/false,  //add CREATE TABLE statements
                      'insert' => true/false,  //add INSERT statements
                      'delete' => true/false, //add delete statements
                      'colnames' => true/false, //add columns names for inserts statements
                    )
      $tables : array of tables names to export
                    array('tablename1', 'tablenamen', 'tablename3', ...)
                  if empty, assume that all tables have to be exported
      $infos : additional info written in exported file (as comment)
      $resultboolean : if true, result is true/false ; 
                       if false, if result, return a string with nfo about export
  */
  function export($filename, $options=array(), $tables=array(), $infos="", $resultboolean=true)
  {
    $defaultopt=array(
      'drop' => true,
      'create' => true,
      'insert' => true,
      'delete' => false,
      'colnames' => false
    );

    if(!isset($options['drop']))
    {
      $options['drop']=$defaultopt['drop'];
    }
    if(!isset($options['create']))
    {
      $options['create']=$defaultopt['create'];
    }
    if(!isset($options['insert']))
    {
      $options['insert']=$defaultopt['insert'];
    }
    if(!isset($options['delete']))
    {
      $options['delete']=$defaultopt['delete'];
    }
    if(!isset($options['colnames']))
    {
      $options['colnames']=$defaultopt['colnames'];
    }
    if(count($tables)==0)
    {
      $tables=$this->tables;
    }

    $resultnfo='';

    $returned=true;
    $text='
-- ***************************************************************              -- EOQ
-- * SQL export made with Grum Plugins Classes (Export tool r'.$this->version.')         -- EOQ
-- * Export date    :'.date('Y-m-d H:i:s').'                                    -- EOQ
-- * Export options :';
if($options['drop']){$text.=' [drop]';}
if($options['delete']){$text.=' [delete]';}
if($options['create']){$text.=' [create]';}
if($options['insert']){$text.=' [insert]';}
if($options['colnames']){$text.=' [colnames]';}
$text.="                            -- EOQ";
if($infos!="")
{
  $text.='
-- * '.$infos." -- EOQ";
}
$text.='
-- ***************************************************************              -- EOQ

';
    foreach($tables as $key => $val)
    {
      $countelems=0;

      $text.="

-- ***************************************************************              -- EOQ
-- * Statements for ".$this->tables[$key]." table                               -- EOQ
-- ***************************************************************              -- EOQ
";

      if($options['drop'])
      {
        $text.=sprintf("DROP TABLE `%s`; -- EOQ\n", $this->tables[$key]);
      }

      if($options['delete'])
      {
        $text.=sprintf("DELETE FROM `%s`; -- EOQ\n", $this->tables[$key]);
      }

      if($options['create'])
      {
        $sql='SHOW CREATE TABLE '.$this->tables[$key];
        $result=pwg_query($sql);
        if($result)
        {
          while($row=mysql_fetch_row($result))
          {
            $text.=sprintf("%s; -- EOQ\n", $row[1]);
          }
        }
        else
        {
          $returned=false;
        }
      }

      if($options['insert'])
      {
        $colnames="";
        if($options['colnames'])
        {
          $sql='SHOW COLUMNS FROM `'.$this->tables[$key].'`';
          $result=pwg_query($sql);
          if($result)
          {
            $tmp=array();
            while($row=mysql_fetch_row($result))
            {
              $tmp[]=$row[0];
            }
          }
          $colnames='('.implode(',', $tmp).')';
        }
    
        $sql='SELECT * FROM '.$this->tables[$key];
        $result=pwg_query($sql);
        if($result)
        {
          while($row=mysql_fetch_row($result))
          {
            foreach($row as $key2 => $val2)
            {
              $row[$key2]="'".addslashes($val2)."'";
            }
            $text.=sprintf("INSERT INTO `%s` %s VALUES(%s); -- EOQ\n", $this->tables[$key], $colnames, implode(', ', $row));
            $countelems++;
          }
        }
        else
        {
          $returned=false;
        }
        $resultnfo.=$key.':'.$countelems.'@';
      }
    }
    $fhandle=fopen($filename, 'wb');
    if($fhandle)
    {
      fwrite($fhandle, $text);
      fclose($fhandle);
    }
    else
    {
      $returned=false;
    }
    if(($resultboolean==false)&&($returned))
    {
      $returned=$resultnfo;
    }
    return($returned);
  }

  /*
    import an .sql file
      $filename : name of the file
      'errors' : -1 file don't exists
                 -2 can't open file
  */
  function import($filename)
  {
    $return = array(
      'numinsert'=>0,
      'numdelete'=>0,
      'numdrop'=>0,
      'numcreate'=>0,
      'errors'=>array(),
      'total_ok'=>0
    );

    if(file_exists($filename))
    {
      $fhandle=fopen($filename, 'r');
      if($fhandle)
      {
        $queries=fread($fhandle, filesize($filename));
        fclose($fhandle);
        $return=$this->multi_queries($queries);
      }
      else
      {
        $return['errors']=-2;
      }
    }
    else
    {
      $return['errors']=-1;
    }
    return($return);
  }

  /*
    execute multiple query
      each query have to be separated by a "-- EOQ\n"
      
      $queries : sql queries
  */
  function multi_queries($queries)
  {
    $queries_list=preg_split(
      '/\s*;?\s*--\s+EOQ[\r\n]{1}/i', $queries, -1, PREG_SPLIT_NO_EMPTY);

    $return = array(
      'numinsert'=>0,
      'numdelete'=>0,
      'numdrop'=>0,
      'numcreate'=>0,
      'errors'=>array(),
      'total_ok'=>0
    );

    $i=0;
    foreach($queries_list as $key => $sql)
    {
      $i++;
      @$result=pwg_query($sql);
      if($result)
      {
        $return['total_ok']++;
        if(preg_match('/\b[\s]*insert[\s]+/i', $sql)>0)
        {$return['numinsert']++;}
        elseif(preg_match('/\b[\s]*drop[\s]+/i', $sql)>0)
        {$return['numdrop']++;}
        elseif(preg_match('/\b[\s]*delete[\s]+/i', $sql)>0)
        {$return['numdelete']++;}
        elseif(preg_match('/\b[\s]*create[\s]+/i',$sql)>0)
        {$return['numcreate']++;}
      }
      else
      {
        array_push($return['errors'], '['.$i.'] '.$sql);
      }
    }
    return($return);
  }

} //class


?>