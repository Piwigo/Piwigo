<?php
/*****************************************************************
 *			VIRTUAL-TEMPLATE
 *
 * Version : 1.3.1 Base Edition ( Juillet 2002 ) build 6
 *
 * Address : http://vtemplate.sourceforge.net
 * 
 * Authors: 
 *   + THIEBAUT Jean-Baptiste(J.Baptiste@leweby.com) - http://www.leweby.com .
 *   + CAMPANA François (fc@netouaibe.com).
 * Licence: GPL.
 *
 *
 *****************************************************************/ 

if ( !isset($DEFINE_VTEMPLATE) )
{
  define("ALL",1);
  define("VARTAG","{#"); // Tag d'ouverture des variables :
                         //  vous pouvez changer ce paramètre.
  define("VTEMPLATE_VERSION","1.3.1");
  define("VTEMPLATE_TYPE","BA");
  define("VTEMPLATE_BUILD","6");
  
  
  class Err
    {
      var $msg;
      var $titre;
      
      function error( $errno, $arg = "", $code = 0, $disp = 0 )
        {
// Gestion des erreurs
          switch($errno)
          {
          case 1:
            $this->titre="Erreur de session n° $code";
            $this->msg = "La zone $arg est déjà ouverte.Avant d'ajouter une session sur cette zone, vous devez la fermer à l'aide de la fonction closeSession().<br>"	;
            break;
          case 2:
            $this->titre="Erreur de session n° $code";
            $this->msg = "Vous tentez de fermer une session de la zone $arg alors qu'aucune session pour cette zone n'existe.Pour ouvrir une session, utilisez la fonction addSession().<br>";
            break;
          case 3:
            $this->titre="Erreur de session n° $code";
            $var = $arg[1];
            $zone = $arg[0];
            $this->msg = "Vous essayez de valoriser la variable $var sans avoir créer de session de la zone $zone.Utilisez la fonction addSession() pour créer une session, puis setVar pour valoriser une variable.<br>";
            break;
          case 4:
            $this->titre="Erreur de session n° $code";
            $var = $arg[1];
            $zone = $arg[0];
            $this->msg = "La variable $var que vous souhaitez valoriser n'existe pas dans la zone $zone.<br>";
            break;
          case 5:
            $this->titre="Erreur de parsing n° $code";
            $this->msg = "Vous utilisez des caractère non autorisés pour déclarer vos zones.Vous pouvez utiliser tous les caractères à l'exception de \'{\' , \'#\' \'}\' et \'|\'.<br>";
          break;
        case 6:
          $this->titre="Erreur de parsing n° $code";
          $this->msg = "Vous ne pouvez pas utiliser le même nom ($arg)de zone plusieurs fois.<br>";
          break;
        case 7:
          $this->titre="Erreur de parsing n° $code";
          $this->msg = "Vous avez oublié de fermer la zone $arg.<br>";
          break;
        case 8:
          $this->titre="Erreur de traitement n° $code";
          $this->msg = "Le fichier template $arg est introuvable.<br>";
          break;
        case 9:
          $this->titre="Erreur de traitement n° $code";
          $this->msg = "Impossible d'ouvrir le fichier $arg.Vérifiez les droits de ce fichier.<br>";
          break;
        case 10:
          $this->titre="Erreur de traitement n° $code";
          $this->msg = "Impossible de lire le fichier template $arg.<br>";	
          break;
        case 11:
          $this->titre="Erreur de traitement n° $code";
          $this->msg = "La zone $arg est introuvable.Vérifiez la syntaxe de cette zone.<br>"; 
          break;
        case 12:  
          $this->titre="Erreur de traitement n° $code";
          $this->msg = "La variable $arg est introuvable .Vérifiez la syntaxe de la variable.<br>";	
          break;
        case 13:
          $this->titre="Erreur de traitement n° $code";
          $this->msg = "L'identifiant de fichier spécifié n'existe pas.Vérifiez les fonctions Open() de votre script.<br>";
          break;  
        case 14:
          $this->titre="Erreur de traitement n° $code";
          $var = $arg[1];
          $file = $arg[0];
          $this->msg = "La variable $var dans le fichier $file est introuvable.Vérifiez la syntaxe de la variable.<br>";	
          break;  
        case 15:
          $this->titre="Erreur de traitement n° $code";
          $var = $arg[2];
          $zone = $arg[1];
          $fichier = $arg[0];
          $this->msg = "La variable $var dans la zone $zone du fichier $fichier est introuvable.Vérifiez la syntaxe de la variable et du nom de la zone.<br>";	
          break; 
        default:
          $this->titre = "Erreur inconnue $code";	 
          $this->msg = "Veuillez le rapporter aux auteurs de la classe.";
        }
        $this->titre .= ": <br>";
        if ($disp){
          $web = "Pour plus d'informations, consultez la <a href=\"http://www.virtual-solution.net/vtemplate/docs/debug-mod.php?version=".VTEMPLATE_VERSION."&build=".VTEMPLATE_BUILD."&type=".VTEMPLATE_TYPE."&error=$code\" target=\"_blank\">doc en ligne</a>";
          echo "<font face=verdana size=2 color=red><u>$this->titre</u><i>$this->msg</i>$web<br><br></font>";
        } 
        return -1;
      }
// Fin classe
    }

  class Session extends err{

    var $name;		// Name of the session
    var $globalvar = array();  // List of global variable of the session
    var $varlist = array();  // List of var in this session
    var $subzone = array(); // list of sub-zone
    var $temp; // Generated code for the current session
    var $generated = NULL; // The final code
    var $source; // Source code
    var $used=0; // Indicates if the session contain used variable
    var $stored; // Give the filename were is stored the session

    function Session($name,$source,$stored){
      $this->name = $name;
      $this->source = $source;
      $this->stored = $stored;
      $this->parseVar();
    }

    function parseVar(){
      // Récupération des noms des variables
      $regle = "|".VARTAG."(.*)}|sU"; 
      preg_match_all ($regle,$this->source,$var1);
      // Création du tableau de variable  à partir de la liste parsée.
      $this->varlist=@array_merge($var[1],$var1[1]);
      return 1;
    } 

    function init(){
      if($this->used) return $this->error(1,array($this->stored,$this->name),"SESSION1",1);
// Reset generated code
      $this->temp = $this->source;
      $this->used = 1;
    }
    function closeSession(){
// Check if the zone has been used.    
      if(!$this->used) return $this->error(2,array($this->stored,$this->name),"SESSION2",1);
// Set Globals vars.
      $this->generateCode();
      $this->used=0;
      return 1;
    }

    function reset(){
      $this->used = 0;
      $this->generated = NULL;
      return 1;
    }

    function addSubZone(&$subzone){
      $this->subzone[$subzone->name] = &$subzone;
      return 1;
    }

    function setVar($varname,$value){
      if (!$this->used) return $this->error(3,array($this->stored,$this->name,$varname),"SESSION3",1);
      if (!in_array($varname,$this->varlist)) return $this->error(4,array($this->name,$varname),"SESSION4",1);
      $regle = "(\\".VARTAG."$varname\})";
      $this->temp = preg_replace($regle,$value,$this->temp);
      return 1;
    }

    function dispVar(){
      echo "Liste variables de $this->name:<br>";
      foreach ( $this->varlist as $vars )
        echo "$vars <br>";
    }

    function setGlobalVar($varname,$value){
      $set = 0;
      if (in_array($varname,$this->varlist)){
        // Replace the var into this session 
        $this->globalvar[$varname]=$value;   
        $set = 1;
      }
      // Replace the var into sub zones
      foreach(array_keys($this->subzone) as $subzone){
        $set = $this->subzone[$subzone]->setGlobalVar($varname,$value) || $set;
      }
      return $set;
    }

    function replaceGlobalVar(){
      if ( count($this->globalvar) )
        foreach($this->globalvar as $varname => $value){
        $regle = "(\\".VARTAG."$varname\})";
        $this->temp = preg_replace($regle,$value,$this->temp);
      }
    }


    function generateCode(){
      if ($this->used == 0) return $this->generated;
      // Replace global var.
      if ( count($this->globalvar) ) $this->replaceGlobalVar();
      // Replace all unused variable by ""
      $regle = "|\\".VARTAG."(.*)\}|";
      $this->temp = preg_replace($regle,"",$this->temp);
      // Generate the subzone(s) code
      if(count($this->subzone)){
        foreach(array_keys($this->subzone) as $subzone){
          $text = ($this->subzone[$subzone]->used) ? $this->subzone[$subzone]->generateCode() : $this->subzone[$subzone]->generated;
          $this->temp = preg_replace("(\|$subzone\|)",$text,$this->temp); 	
          $this->subzone[$subzone]->reset();
        }
      }
      $this->generated .= $this->temp;
      return $this->generated;
    }

    function inVarList($varname){
      return in_array($varname,$this->varlist);
    }

// Fin classe
  }

  class VTemplate_Private extends Err{
/****************************************
 *	   Private Class.		*
 * ***************************************/

    var $sources=array(); // Sources des zones issues de la premiere partie du parsing.
    var $sessions=array(); // Tableau de sessions
    var $v_global=array(); // Globla var array.

/****************************************************************
	    Parsing Functions for Template files.  ( PF 1.0 )
****************************************************************/

    function getNom($code){
// Retourne le premier nom de zone qu'il trouve dans le code

      preg_match("(<!--VTP_([^()]+)-->)sU",$code,$reg);
   
      // Tester la présence des caratère invalides dans le nom ( | et {});
      if (@count(explode("|",$reg[1]))>1 || @count(explode("{",$reg[1]))>1 || @count(explode("}",$reg[1]))>1) exit($this->error(5,$reg[1],"PARSE1",1));
   
      return @$reg[1];
    }

    function endTag($code,$nom){
// Renvoie TRUE(1) si le tag de fermeture est présent.

      preg_match("(<!--/VTP_$nom-->)sU",$code,$reg);

      return ($reg[0]!="<!--/VTP_$nom-->") ? 0 : 1;
    }

    function getSource($code,$nom,$type=0){
// Retourne le source de la zone de nom $nom

      preg_match_all ("(<!--VTP_$nom-->(.*)<!--/VTP_$nom-->)sU",$code,$reg);

      return $reg[$type][0];
    }

    function parseZone($code_source,$nom_zone="|root|"){
// Fonction récursive de parsing du fichier template
      // Vérification que la zone n'existe pas
      if (isset($this->sources[$nom_zone])) exit($this->error(6,$nom_zone,"PARSE2",1));

      // Enregistrement du code source
      $this->sources[$nom_zone]["source"]=$code_source;

      // Rappel de la fonction pour chaque fils.
      while($nom_fils=$this->getNom($this->sources[$nom_zone]["source"])){

        // Vérification que le tag de fin est présent.
        if (!$this->endTag($code_source,$nom_fils)) exit($this->error(7,$nom_fils,"PARSE3",1));

        // Parse le fils
        $this->parseZone($this->getSource($this->sources[$nom_zone]["source"],$nom_fils,1),$nom_fils);

        // Enregistre le nom du fils dans la liste des fils
        $this->sources[$nom_zone]["fils"][]=$nom_fils;

        // Remplace le code du fils dans le source du père
        $this->sources[$nom_zone]["source"]=str_replace(
          $this->getSource($this->sources[$nom_zone]["source"],$nom_fils,0),
          "|$nom_fils|",
          $this->sources[$nom_zone]["source"]
          );
        // Teste si la zone $nom_fils n'existe pas plusieurs fois dans la zone $nom_zone
        if (count(explode("|$nom_fils|",$this->sources[$nom_zone]["source"]))>2) exit($this->error(6,$nom_fils,"PARSE4",1));
      }// fin While

      return 1;
    }

/****************************************************************
	    Session Management functions ( SMF 1.0 )
****************************************************************/

    function createSession($handle,$zone = "|root|"){
// Create a new session of the zone
      $this->sessions[$handle][$zone] = new Session($zone,$this->sources[$zone]["source"],$this->file_name[$handle]);

// Create sub-zone
      if (@count($this->sources[$zone]["fils"])){
        foreach($this->sources[$zone]["fils"] as $subzone){	  
          $this->createSession($handle,$subzone);
          $this->sessions[$handle][$zone]->addSubZone($this->sessions[$handle][$subzone]);
        }
      }
				  
//end createSession
    }


/****************************************************************
	    Global Variable Management Functions ( GVMF 1.0 )
****************************************************************/

    function setGZone($handle,$zone,$var,$value){
      // Define Global var for $zone and its sub-zone.
      // Set global value to $zone vars.
      return $this->sessions[$handle][$zone]->setGlobalVar($var,$value);
    }

    function setGFile($handle,$var,$value) {
      return $this->sessions[$handle]["|root|"]->setGlobalVar($var,$value);
    }

    function setGAll($var,$value){
      $declare = 0;
      $this->v_global[$var]=$value;
      if (is_array($this->sessions)){
        foreach($this->sessions as $handle => $v){
          $declare = $this->setGFile($handle,$var,$value) || $declare;
        }	
      } 
      return $declare;
    }

    function setGOpened($handle){
// Set Global var into the opened file
      foreach($this->v_global as $name => $val){
        $this->setGFile($handle,$name,$val);
      }
      return 1;
    }
 
// Fin VTemplate_Private
  }


  class VTemplate extends VTemplate_Private{
/****************************************
 *	   Public Class.		*
 * ***************************************/


/****************************************************************
	    Core Functions 
*****************************************************************/


    function Open($nomfichier){
// Ouverture d'un fichier source et retourne le handle de ce fichier
// Création du handle:
      $handle =  "{".count($this->sessions)."}" ;


// Récupération du source à parser
      if (!@file_exists($nomfichier)) return $this->error(8,$nomfichier,"TTT1",1);
      if (!$f_id=@fopen($nomfichier,"r")) return $this->error(9,$nomfichier,"TTT2",1);
      if (!$source=@fread($f_id, filesize($nomfichier))) return $this->error(10,$nomfichier,"TTT3",1);
      clearstatcache();
      fclose($f_id);

// Store the filename
      $this->file_name[$handle]=$nomfichier;

// Parse les zones
      $this->parseZone($source);

// Création du tableau de session
      $this->createSession($handle);

//Nettoyage des variables temporaires
      $this->sources=NULL;

// Set global var.
      $this->setGOpened($handle);

      $this->addSession($handle);
      return $handle;
    }

    function newSession($handle="{0}",$nom_zone = "|root|"){
      if ( $this->sessions[$handle][$nom_zone]->used ) $this->closeSession($handle,$nom_zone);
      $this->addSession($handle,$nom_zone,$cache,$time,$num_session);
      return 1;
    }

    function addSession($handle="{0}",$nom_zone = "|root|"){
      // Does the zone exist ?
      if(!isset($this->sessions[$handle][$nom_zone])) return $this->error(11,array($nom_zone,$this->file_name[$handle]),"TTT4",1);   
      $this->sessions[$handle][$nom_zone]->init();
      return 1;
    }

    function closeSession($handle="{0}",$nom_zone = "|root|"){ 
// Close the current session and all his sub-session
      // Check if the zone exists.
      if(!isset($this->sessions[$handle][$nom_zone])) return $this->error(11,array($nom_zone,$this->file_name[$handle]),"TTT5",1);
      // Closing sub-zone
      $this->sessions[$handle][$nom_zone]->closeSession();	
      return 1;
    }

    function setGlobalVar($arg1,$arg2,$arg3){
      if ($arg1 == 1){
        if (!$this->setGAll($arg2,$arg3)) return $this->error(12,$arg2,"TTT6",1);
        return 1; 
      }
      if (!isset($this->sessions[$arg1])) return $this->error(13,$arg1,"TTT7",1);
      $tab=explode(".",$arg2);
      if (count($tab)==1){
        if (!$this->setGFile($arg1,$arg2,$arg3)) return $this->error(14,array($this->file_name[$arg1],$arg2),"TTT8",1);
      }
      else if (count($tab==2)){
        if (!isset($this->sessions[$arg1][$tab[0]])) return $this->error(11,array($tab[0],$this->file_name[$arg1],"TTT9",1));
        if (!$this->setGZone($arg1,$tab[0],$tab[1],$arg3)) return $this->error(15,array($this->file_name[$arg1],$tab[0],$tab[1]),"TTT10",1);
      }
      return 1;
    }

    function setVar($handle,$zone_var,$val){
      // Fill the variable
      $tab=explode(".",$zone_var);
      if(count($tab)==2){
        $zone=$tab[0];
        $var=$tab[1];
      }
      else
      {
        $zone="|root|";
        $var=$tab[0];
      }

      // Teste l'existence de la zone dans la liste
      if (!isset($this->sessions[$handle][$zone])) return $this->error(11,array($this->file_name[$handle],$zone),"TTT11",1);

      //Enregistre la variable
      return $this->sessions[$handle][$zone]->setVar($var,$val);
    }

    function Parse($handle_dest,$zone_var_dest,$handle_source,$zone_source="|root|"){
      if($this->sessions[$handle_source][$zone_source]->used == 1) $this->closeSession($handle_source,$zone_source);
      $this->setVar($handle_dest,$zone_var_dest, $this->sessions[$handle_source][$zone_source]->generated);
    }

    function setVarF($handle,$zone_var,$file){
// Fonction qui ouvre le fichier file et copie ce qu'il y a dedans dans une variable.
      $tab=explode(".",$zone_var);

// Récupération nom de la zone et de la variable.
      if(count($tab)==2){
        $zone=$tab[0];
        $var=$tab[1];
      }
      else
      {
        $zone="|root|";
        $var=$tab[0];
      }
// Teste l'existence de la zone dans la liste
      if (!is_object($this->sessions[$handle][$zone])) return $this->error(11,array($handle,$zone),"TTT12",1);

      // Récupération du source à lire
      if (!@file_exists($file)) return $this->error(8,$file,"TTT13",1);
      if (!$f_id=@fopen($file,"r")) return $this->error(9,$file,"TTT14",1);
      if (!$val=@fread($f_id, filesize($file))) return $this->error(10,$file,"TTT15",1);
      clearstatcache();
      fclose($f_id);

//Enregistre la variable
      return $this->sessions[$handle][$zone]->setVar($var,$val);
    }

    function isZone($handle, $zone="|root|") 
      { 
        return isset($this->sessions[$handle][$zone]) ; 
      } 

    function Display($handle="{0}",$display=1,$zone="|root|"){
      $this->closeSession($handle,$zone);
      $c_genere = $this->sessions[$handle][$zone]->generated; 
	
      if ($display) echo $c_genere; else return ($c_genere);
    }


// End VTemplate
  }
  $DEFINE_VTEMPLATE = 1;
}
?>
