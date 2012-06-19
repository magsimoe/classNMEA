<?php

/**
 * Description of classAIS
 *
 * @author Magnus Berntsson
 * magsimoe@gmail.com
 */
class classAIS {

    function __construct() {
        $this->AIS = array();
    }

    public function DumpAIS() {
        return $this->AIS;
    }

    private function ascii_2_dec($chr) {
        $dec = ord($chr); //get decimal ascii code
        //$hex = dechex($dec); //convert decimal to hex
        return ($dec);
    }

    function asciidec_2_8bit($ascii) {
        if ($ascii < 48) {
            
        } else {
            if ($ascii > 119) {
                
            } else {
                if ($ascii > 87 && $ascii < 96) {
                    
                } else {
                    $ascii = $ascii + 40;
                    if ($ascii > 128) {
                        $ascii = $ascii + 32;
                    } else {
                        $ascii = $ascii + 40;
                    }
                }
            }
        }
        return ($ascii);
    }

    function dec_2_6bit($dec) {
        $bin = decbin($dec);
        return(substr($bin, -6));
    }

    public function ParseAIS($ais) {
        $aisdata168 = NULL; //six bit array of ascii characters
        $ais_nmea_array = str_split($ais);
        foreach ($ais_nmea_array as $value) {
            $dec = $this->ascii_2_dec($value);
            $bit8 = $this->asciidec_2_8bit($dec);
            $bit6 = $this->dec_2_6bit($bit8);
            //echo $value . "-" . $bit6 . "";
            $aisdata168 .=$bit6;
        }
        //echo $aisdata168 . "";

        $this->AIS['mmsi'] = bindec(substr($aisdata168, 8, 30));
        $this->AIS['status'] = bindec(substr($aisdata168, 38, 4));
        $this->AIS['hdg'] = bindec(substr($aisdata168, 128, 9));
        $this->AIS['cog'] = bindec(substr($aisdata168, 116, 12)) / 10;
        $this->AIS['sog'] = bindec(substr($aisdata168, 50, 10)) / 10;
        $this->AIS['lat'] = bindec(substr($aisdata168, 89, 27)) / 600000;
        $this->AIS['lon'] = bindec(substr($aisdata168, 61, 28)) / 600000;

        /* echo "mmsi= " . bindec(substr($aisdata168, 8, 30)) . "<br>";
          echo "Status= " . bindec(substr($aisdata168, 38, 4)) . "<br>";
          echo "hdg= " . bindec(substr($aisdata168, 128, 9)) . "<br>";
          echo "cog= " . bindec(substr($aisdata168, 116, 12)) / 10 . "<br>";
          echo "sog= " . bindec(substr($aisdata168, 50, 10)) / 10 . "<br>";
          echo "lat= " . bindec(substr($aisdata168, 89, 27)) / 600000 . "<br>";
          echo "lon= " . bindec(substr($aisdata168, 61, 28)) / 600000 . "<br>"; */
    }

}

?>
