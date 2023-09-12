<?php
class zipfile
{
    /**
     * array to store compressed data
     *
     * @var array   $datasec
     */
    var $datasec        = array();

    /**
     * Central directory
     *
     * @var array   $ctrl_dir
     */
    var $ctrl_dir    = array();

    /**
     * end of central directory record
     *
     * @var string   $eof_ctrl_dir
     */
    var $eof_ctrl_dir = "x50x4bx05x06x00x00x00x00";

    /**
     * Last offset position
     *
     * @var integer $old_offset
     */
    var $old_offset  = 0;


    /**
     * Converts an Unix timestamp to a four byte DOS date and time format (date
     * in high two bytes, time in low two bytes allowing magnitude comparison).
     *
     * @param   integer the current Unix timestamp
     *
     * @return integer  the current date in a four byte DOS format
     *
     * @access private
     */
    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if (1) {
            $timearray['year']  = 2000;
            $timearray['mon']    = 1;
            $timearray['mday']  = 1;
            $timearray['hours']  = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method


    /**
     * Adds "file" to archive
     *
     * @param   string   file contents
     * @param   string   name of the file in the archive (may contains the path)
     * @param   integer the current timestamp
     *
     * @access public
     */
    function addFile($data, $name, $time = 0)
    {
        $name    = str_replace('', '/', $name);

        $dtime  = dechex($this->unix2DosTime($time));
        $hexdtime = 'x' . $dtime[6] . $dtime[7]
                    . 'x' . $dtime[4] . $dtime[5]
                    . 'x' . $dtime[2] . $dtime[3]
                    . 'x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr  = "x50x4bx03x04";
        $fr  .= "x14x00";         // ver needed to extract
        $fr  .= "x00x00";         // gen purpose bit flag
        $fr  .= "x08x00";         // compression method
        $fr  .= $hexdtime;           // last mod time and date

        // "local file header" segment
        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data,0); //Change le 0 pour avoir une compression differente
        $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
        $c_len   = strlen($zdata);
        $fr     .=  pack ('V', $crc);          // crc32
        $fr     .= pack('V', $c_len);            // compressed filesize
        $fr     .= pack('V', $unc_len);      // uncompressed filesize
        $fr     .= pack('v', strlen($name));    // length of filename
        $fr     .= pack('v', 0);                // extra field length
        $fr     .= $name;

        // "file data" segment
        $fr .= $zdata;

         // "data descriptor" segment (optional but necessary if archive is not
        // served as file)
        $fr .= pack('V', $crc);              // crc32
        $fr .= pack('V', $c_len);                // compressed filesize
        $fr .= pack('V', $unc_len);          // uncompressed filesize

        // add this entry to array
        $this -> datasec[] = $fr;
        $new_offset     = strlen(implode('', $this->datasec));


        // now add to central directory record
        $cdrec = "x50x4bx01x02";
        $cdrec .= "x00x00";               // version made by
        $cdrec .= "x14x00";               // version needed to extract
        $cdrec .= "x00x00";               // gen purpose bit flag
        $cdrec .= "x08x00";               // compression method
        $cdrec .= $hexdtime;                 // last mod time & date
        $cdrec .= pack('V', $crc);           // crc32
        $cdrec .= pack('V', $c_len);         // compressed filesize
        $cdrec .= pack('V', $unc_len);       // uncompressed filesize
        $cdrec .= pack('v', strlen($name) ); // length of filename
        $cdrec .= pack('v', 0 );             // extra field length
        $cdrec .= pack('v', 0 );             // file comment length
        $cdrec .= pack('v', 0 );             // disk number start
        $cdrec .= pack('v', 0 );             // internal file attributes
        $cdrec .= pack('V', 32 );           // external file attributes - 'archive' bit set

        $cdrec .= pack('V', $this -> old_offset ); // relative offset of local header
        $this -> old_offset = $new_offset;

        $cdrec .= $name;

        // optional extra field, file comment goes here
        // save to central directory
        $this -> ctrl_dir[] = $cdrec;
    } // end of the 'addFile()' method


    /**
     * Dumps out file
     *
     * @return  string  the zipped file
     *
     * @access public
     */
    function file()
    {
    $data   = implode('', $this -> datasec);
        $ctrldir = implode('', $this -> ctrl_dir);

        return
            $data .
            $ctrldir .
            $this -> eof_ctrl_dir .
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries "on this disk"
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries overall
            pack('V', strlen($ctrldir)) .            // size of central dir
            pack('V', strlen($data)) .              // offset to start of central dir
            "x00x00";                          // .zip file comment length
    } // end of the 'file()' method

} // end of the 'zipfile' class
?>
<?php


function unzip($file, $path='', $effacer_zip=false)
{/*Méthode qui permet de décompresser un fichier zip $file dans un répertoire de destination $path
  et qui retourne un tableau contenant la liste des fichiers extraits
  Si $effacer_zip est égal à true, on efface le fichier zip d'origine $file*/
    
    $tab_liste_fichiers = array(); //Initialisation

    $zip = zip_open($file);

    if ($zip)
    {
        while ($zip_entry = zip_read($zip)) //Pour chaque fichier contenu dans le fichier zip
        {
            if (zip_entry_filesize($zip_entry) > 0)
            {
                $complete_path = $path.dirname(zip_entry_name($zip_entry));

                /*On supprime les éventuels caractères spéciaux et majuscules*/
                $nom_fichier = zip_entry_name($zip_entry);
                $nom_fichier = strtr($nom_fichier,"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
                $nom_fichier = strtolower($nom_fichier);
                $nom_fichier = preg_replace('[^a-zA-Z0-9.]','-',$nom_fichier);

                /*On ajoute le nom du fichier dans le tableau*/
                array_push($tab_liste_fichiers,$nom_fichier);

                $complete_name = $path.$nom_fichier; //Nom et chemin de destination

                if(!file_exists($complete_path))
                {
                    $tmp = '';
                    foreach(explode('/',$complete_path) AS $k)
                    {
                        $tmp .= $k.'/';

                        if(!file_exists($tmp))
                        { mkdir($tmp, 0755); }
                    }
                }

                /*On extrait le fichier*/
                if (zip_entry_open($zip, $zip_entry, "r"))
                {
                    $fd = fopen($complete_name, 'w');

                    fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));

                    fclose($fd);
                    zip_entry_close($zip_entry);
                }
            }
        }

        zip_close($zip);

        /*On efface éventuellement le fichier zip d'origine*/
        if ($effacer_zip === true)
        unlink($file);
    }

    return $tab_liste_fichiers;
}
    


?> 