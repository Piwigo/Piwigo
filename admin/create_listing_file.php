<?php
	$prefixe_thumbnail = "TN-";
	
	
	$tab_ext = array ( 'jpg', 'JPG','gif','GIF','png','PNG' );

	$listing = "";
	
	$local_folder = substr( $PHP_SELF, 0, strrpos( $PHP_SELF, "/" ) + 1 );
	$url = "http://".$HTTP_HOST.$local_folder;
	$listing.= "<url>$url</url>";
	
	// get_dirs retourne un tableau contenant tous les sous-répertoires d'un répertoire
	function get_dirs( $rep, $indent, $level )
	{
		$sub_rep = array();
		$i = 0;
		$dirs = "";
		if ( $opendir = opendir ( $rep ) )
		{
			while ( $file = readdir ( $opendir ) )
			{
				if ( $file != "." && $file != ".." && is_dir ( $rep."/".$file ) && $file != "thumbnail" )
				{
					$sub_rep[$i++] = $file;
				}
			}
		}
		// write of the dirs
		for ( $i = 0; $i < sizeof( $sub_rep ); $i++ )
		{
			$dirs.= "\n".$indent."<dir".$level.">";
			$dirs.= "\n".$indent."\t<name>".$sub_rep[$i]."</name>";
			$dirs.= get_pictures( $rep."/".$sub_rep[$i], $indent."\t" );
			$dirs.= get_dirs( $rep."/".$sub_rep[$i], $indent."\t", $level + 1 );
			$dirs.= "\n".$indent."</dir".$level.">";
		}
		return $dirs;		
	}
	
	function is_image ( $filename )
	{
		global $tab_ext;
		if ( in_array ( substr ( strrchr($filename,"."), 1, strlen ( $filename ) ), $tab_ext ) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function TN_exist ( $dir, $file )
	{
		global $tab_ext, $prefixe_thumbnail;
		
		$titre = substr ( $file, 0, -4 );
		for ( $i = 0; $i < sizeof ( $tab_ext ); $i++ )
		{
			$test = $dir."/thumbnail/".$prefixe_thumbnail.$titre.".".$tab_ext[$i];
			if ( is_file ( $test ) )
			{
				return $tab_ext[$i];
			}
		}
		return false;
	}

	function get_pictures( $rep, $indent )
	{
		$pictures = array();		
		$i = 0;
		$tn_ext = "";
		$root = "";
		if ( $opendir = opendir ( $rep ) )
		{
			while ( $file = readdir ( $opendir ) )
			{
				if ( is_image( $file ) && $tn_ext = TN_exist( $rep, $file ) )
				{
					$pictures[$i] = array();
					$pictures[$i]['file'] = $file;
					$pictures[$i]['tn_ext'] = $tn_ext;
					$pictures[$i]['date'] = date( "Y-m-d", filemtime ( $rep."/".$file ) );
					$pictures[$i]['filesize'] = floor ( filesize( $rep."/".$file ) / 1024 );
					$image_size = @getimagesize( $rep."/".$file );
					$pictures[$i]['width'] = $image_size[0];
					$pictures[$i]['height'] = $image_size[1];
					$i++;
				}
			}
		}
		// write of the node <root> with all the pictures at the root of the directory
		$root.= "\n".$indent."<root>";
		if ( sizeof( $pictures ) > 0 )
		{
			for( $i = 0; $i < sizeof( $pictures ); $i++ )
			{
				$root.= "\n".$indent."\t<picture>";
				$root.= "\n".$indent."\t\t<file>".$pictures[$i]['file']."</file>";
				$root.= "\n".$indent."\t\t<tn_ext>".$pictures[$i]['tn_ext']."</tn_ext>";
				$root.= "\n".$indent."\t\t<date>".$pictures[$i]['date']."</date>";
				$root.= "\n".$indent."\t\t<filesize>".$pictures[$i]['filesize']."</filesize>";
				$root.= "\n".$indent."\t\t<width>".$pictures[$i]['width']."</width>";
				$root.= "\n".$indent."\t\t<height>".$pictures[$i]['height']."</height>";
				$root.= "\n".$indent."\t</picture>";
			}
		}
		$root.= "\n".$indent."</root>";
		return $root;
	}

	$listing.= get_dirs( ".", "", 0 );

	if ( $fp = @fopen("./listing.xml","w") )
	{
		fwrite( $fp, $listing );
		fclose( $fp );
	}
	else
	{
		echo "impossible de créer ou d'écrire dans le fichier listing.xml";
	}

	//echo str_replace( "\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", nl2br( htmlspecialchars( $listing, ENT_QUOTES ) ) );
	echo "listing.xml created";
?>