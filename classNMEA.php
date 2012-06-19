<?php

//
//This is a work in progress and probably riddled with errors. I have no ability to test all of these sentences in real life.
//Only the ones my gps spits out, I know work 100%. The rest are just written by looking at examples online.
//Feel free to use this in your projects. If you change anything to the better or add more sentences. Please let me know so I can update the class.
//You may contact me at: magsimoe@gmail.com.
//
//
//
// USAGE!
//
//include "classNMEA.php";
//$nmea = new NmeaParser();
//$nmea->ParseLine('$GPRMC,144644,A,5738.1727,N,01134.2490,E,2.4,130.0,260512,1.3,E,A*1A');
//$data = $nmea->DumpNmea();
//echo "Type: " . $data[type] . "<br>";
//echo "Time: " . $data[utc] . "<br>";
//echo "Fix (Active or Void): " . $data[statusrmc] . "<br>";
//echo "Latitude: " . $data[lat] . "<br>";
//echo "NS: " . $data[ns] . "<br>";
//echo "Longitude: " . $data[long] . "<br>";
//echo "EW: " . $data[ew] . "<br>";
//echo "Speed: " . $data[speed] . "<br>";
//echo "Course: " . $data[track] . "<br>";
//echo "Date: " . $data[date] . "<br>";
//echo "Magnetic Variation: " . $data[magvar] . "<br>";
//echo "Magnetic Var.. EW: " . $data[mag_ew] . "<br>";
//
//Not yet implemented: 
//GPAPA, GPGRS, GPGST, GPRMA, GPRTE, GPRTF, GPSTN, GPVBW, GPWCV, GPXTC, GPXTG, GPZTG, PSLIB, PGRME, PGRMM, PGRMZ. Among several more...


class NmeaParser {

    private $Nmea;
    private $maxHDOP;
    private $maxVDOP;

    function __construct() {
        $this->Nmea = array();
        $this->maxHDOP = 0.0;
        $this->maxVDOP = 0.0;
    }

    public function DumpNmea() {
        return $this->Nmea;
    }

    private function NMEAtoUnixTime($utc, $date) {
        $h = substr($utc, 0, 2);
        $i = substr($utc, 2, 2);
        $s = substr($utc, 4, 2);
        $d = substr($date, 0, 2);
        $m = substr($date, 2, 2);
        $y = substr($date, 4, 2);
        //list($y,$m,$d) = explode('-',$date);
        return mktime($h, $i, $s, $m, $d, $y);
    }

    private function degree2decimal($deg_coord, $direction, $precision = 8) {
        $degree = (int) ($deg_coord / 100); //simple way
        $minutes = $deg_coord - ($degree * 100);
        $dotdegree = $minutes / 60;
        $decimal = $degree + $dotdegree;
        //South latitudes and West longitudes need to return a negative result
        if (($direction == "S") or ($direction == "W")) {
            $decimal = $decimal * (-1);
        }
        $decimal = number_format($decimal, $precision, '.', ''); //truncate decimal to $precision places
        return $decimal;
    }

    private function fixUTC($UTC) {
        list($Fixed, $Null) = explode('.', $UTC);
        return $Fixed;
    }

    private function SetNmeaType($line) {
        $this->type = trim(strtoupper(substr($line, 1, 5)));
        return $this->type;
    }

    public function SetMinSatellites($minSats = 4) {
        $this->minSats = $minSats;
    }

    public function SetMaxHdop($maxHDOP = 10) {
        $this->maxHDOP = $maxHDOP;
    }

    public function SetMaxVdop($maxVDOP = 10) {
        $this->maxVDOP = $maxVDOP;
    }

    public function ParseLine($line) {
        $this->NmeaType = $this->SetNmeaType($line);
        switch ($this->type) {
            //
            // COMMON SENTENCES
            //
            case "GPAAM": $this->GPAAM($line);
                break; //Waypoint Arrival Alarm
            case "GPALM": $this->GPALM($line);
                break; //Almanac data
            case "GPAPA": $this->GPAPA($line);
                break; //Auto Pilot A sentence
            case "GPAPB": $this->GPAPB($line);
                break; //Auto Pilot B sentence
            case "GPBOD": $this->GPBOD($line);
                break; //Bearing Origin to Destination
            case "GPBWC": $this->GPBWC($line);
                break; //Bearing using Great Circle route
            case "GPDTM": $this->GPDTM($line);
                break; //Datum being used
            case "GPGGA": $this->GPGGA($line);
                break; //Fix information
            case "GPGLL": $this->GPGLL($line);
                break; //Lat/Lon Data
            case "GPGRS": $this->GPGRS($line);
                break; //GPS Range Residuals
            case "GPGSA": $this->GPGSA($line);
                break; // Overall Satellite data
            case "GPGST": $this->GPGST($line);
                break; //GPS Pseudorange Noise Statistics
            case "GPGSV": $this->GPGSV($line);
                break; //Detailed Satellite data
            case "GPMSK": $this->GPMSK($line);
                break; //send control for a beacon receiver
            case "GPMSS": $this->GPMSS($line);
                break; //Beacon receiver status information
            case "GPRMA": $this->GPRMA($line);
                break; //recommended Loran data
            case "GPRMB": $this->GPRMB($line);
                break; //recommended navigation data for gps
            case "GPRMC": $this->GPRMC($line);
                break; //recommended minimum data for gps
            case "GPRTE": $this->GPRTE($line);
                break; //route message
            case "GPTRF": $this->GPTRF($line);
                break; //Transit Fix data
            case "GPSTN": $this->GPSTN($line);
                break; //Multiple data ID
            case "GPVBW": $this->GPVBW($line);
                break; //dual Ground / Water Speed
            case "GPVTG": $this->GPVTG($line);
                break; //Vector track and Speed over the Ground
            case "GPWCV": $this->GPWCV($line);
                break; //Waypoint closure velocity (Velocity made good)
            case "GPWPL": $this->GPWPL($line);
                break; //Waypoint Location information
            case "GPXTC": $this->GPXTC($line);
                break; //cross track error
            case "GPXTE": $this->GPXTE($line);
                break; //measured cross track error
            case "GPZDA": $this->GPZDA($line);
                break; //Zulu (UTC) time and time to go (to destination)
            case "GPZTG": $this->GPZTG($line);
                break; //Date and Time
            //SPECIAL SENTENCES
            case "HCHDG": $this->HCHDG($line);
                break; //Compass output
            case "PSLIB": $this->PSLIB($line);
                break; //Remote Control for a DGPS receiver
            case "WIMWV": $this->WIMWV($line);
                break; //Weather something something...
            //
            //GARMIN PROPRIETARY SENTENCES
            //
            case "PGRME": $this->PGRME($line);
                break; //Estimated error (not sent if set to 0183 v1.5)
            case "PGRMM": $this->PGRMM($line);
                break; //Map datum
            case "PGRMZ": $this->PGRMZ($line);
                break; //Altitude
            case "PSLIB": $this->PSLIB($line);
                break; //Beacon receiver control
            //
            //DEPTHSOUNDER SENTENCES
            //
            case "SDDBT": $this->SDDBT($line);
                break; //Depth Below Transducer
            case "SDDPT": $this->SDDPT($line);
                break; //Depth
            case "SDMTW": $this->SDMTW($line);
                break; //Mean Temperature of Water
            default: return;
        }
    }

    private function GPAAM($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['arrcirent'] = $split[1];
        $this->Nmea['perpass'] = $split[2];
        $this->Nmea['cirradius'] = $split[3];
        $this->Nmea['nautmiles'] = $split[4];
        $this->Nmea['wptnme'] = $split[5];
    }

    private function GPALM($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['msgnmbrs'] = $split[1];
        $this->Nmea['msgnmbr'] = $split[2];
        $this->Nmea['prn'] = $split[3];
        $this->Nmea['gpsweek'] = $split[4];
        $this->Nmea['health'] = $split[5];
        $this->Nmea['ecc'] = $split[6];
        $this->Nmea['oa'] = $split[7];
        $this->Nmea['sigma'] = $split[8];
        //Unfinished
    }

    private function GPAPA($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['lcb'] = $split[1];
        $this->Nmea['lcc'] = $split[2];
        $this->Nmea['xtrkerr'] = $split[3];
        $this->Nmea['lrcorrect'] = $split[4];
        $this->Nmea['ctu'] = $split[5];
        $this->Nmea['ace'] = $split[6];
        $this->Nmea['ppw'] = $split[7];
        $this->Nmea['bod'] = $split[8];
        $this->Nmea['mt'] = $split[9];
        $this->Nmea['dwi'] = $split[10];
    }

    private function GPAPB($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['lcb'] = $split[1];
        $this->Nmea['lcc'] = $split[2];
        $this->Nmea['xtrkerr'] = $split[3];
        $this->Nmea['lrcorrect'] = $split[4];
        $this->Nmea['errunit'] = $split[5];
        $this->Nmea['aac'] = $split[6];
        $this->Nmea['aap'] = $split[7];
        $this->Nmea['mbod'] = $split[8];
        $this->Nmea['dwid'] = $split[9];
        $this->Nmea['mbppd'] = $split[10];
        $this->Nmea['mhs'] = $split[11];
    }

    private function GPBOD($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['btsd'] = $split[1];
        $this->Nmea['t'] = $split[2];
        $this->Nmea['bsmd'] = $split[3];
        $this->Nmea['m'] = $split[4];
        $this->Nmea['dwi'] = $split[5];
        $this->Nmea['owi'] = $split[6];
    }

    private function GPBWC($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['time'] = $split[1];
        $this->Nmea['lat'] = $split[2];
        $this->Nmea['ns'] = $split[3];
        $this->Nmea['long'] = $split[4];
        $this->Nmea['btwt'] = $split[5];
        $this->Nmea['t'] = $split[6];
        $this->Nmea['btwm'] = $split[7];
        $this->Nmea['m'] = $split[8];
        $this->Nmea['dtwn'] = $split[9];
        $this->Nmea['n'] = $split[10];
        $this->Nmea['wid'] = $split[11];
    }

    private function GPDTM($geostr) {
//Not yet implemented.
    }

    private function GPGGA($geostr) {
        $split = explode(",", $geostr);
        $this->CurrentUTC = $this->fixUTC($split[1]);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['utc'] = $this->fixUTC($split[1]);
        $this->Nmea['lat'] = $this->degree2decimal($split[2], $split[3]);
        $this->Nmea['ns'] = $split[3];
        $this->Nmea['long'] = $this->degree2decimal($split[4], $split[5]);
        $this->Nmea['ew'] = $split[5];
        $this->Nmea['gpsqual'] = $split[6];
        $this->Nmea['numsat'] = $split[7];
        $this->Nmea['hdp'] = $split[8];
        $this->Nmea['alt'] = $split[9];
        $this->Nmea['un_alt'] = $split[10];
        $this->Nmea['geoidal'] = $split[11];
        $this->Nmea['un_geoidal'] = $split[12];
        $this->Nmea['dgps'] = $split[13];
        $this->Nmea['diffstat'] = trim($split[14]);
    }

    private function GPGLL($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->CurrentUTC = $this->fixUTC($split[3]);
        $this->Nmea['utc'] = $this->fixUTC($split[3]);
        $this->Nmea['status'] = $this->dataStatus($split[4]);
    }

    private function GPGRS($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['time'] = $split[1];
        $this->Nmea['gga'] = $split[2];
        $this->Nmea['sat1'] = $split[3];
        $this->Nmea['sat2'] = $split[4];
        $this->Nmea['sat3'] = $split[5];
        $this->Nmea['sat4'] = $split[6];
        $this->Nmea['sat5'] = $split[7];
        $this->Nmea['sat6'] = $split[8];
        $this->Nmea['sat7'] = $split[9];
        $this->Nmea['sat8'] = $split[10];
        $this->Nmea['sat9'] = $split[11];
        $this->Nmea['sat10'] = $split[12];
        $this->Nmea['sat11'] = $split[13];
        $this->Nmea['sat12'] = $split[14];
    }

    private function GPGSA($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['selectmode'] = $split[1];
        $this->Nmea['mode'] = $split[2];
        $this->Nmea['sat1'] = $split[3];
        $this->Nmea['sat2'] = $split[4];
        $this->Nmea['sat3'] = $split[5];
        $this->Nmea['sat4'] = $split[6];
        $this->Nmea['sat5'] = $split[7];
        $this->Nmea['sat6'] = $split[8];
        $this->Nmea['sat7'] = $split[9];
        $this->Nmea['sat8'] = $split[10];
        $this->Nmea['sat9'] = $split[11];
        $this->Nmea['sat10'] = $split[12];
        $this->Nmea['sat11'] = $split[13];
        $this->Nmea['sat12'] = $split[14];
        $this->Nmea['pdop'] = $split[15];
        $this->Nmea['hdop'] = $split[16];
        $this->Nmea['vdop'] = $split[17];
    }

    private function GPGST($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['time'] = $split[1];
        $this->Nmea['tot'] = $split[2];
        $this->Nmea['dev1'] = $split[3];
        $this->Nmea['dev2'] = $split[4];
        $this->Nmea['ori'] = $split[5];
        $this->Nmea['dev3'] = $split[6];
        $this->Nmea['dev4'] = $split[7];
        $this->Nmea['dev5'] = $split[8];
    }

    private function GPGSV($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['satmessages'] = $split[1];
        $this->Nmea['messnum'] = $split[2];
        $this->Nmea['satview'] = $split[3];
        $this->Nmea['satnum'] = $split[4];
        $this->Nmea['elevdeg'] = $split[5];
        $this->Nmea['azimuthdeg'] = $split[6];
        $this->Nmea['snr'] = $split[7];
    }

    private function GPMSK($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['freq'] = $split[1];
        $this->Nmea['mode'] = $split[2];
        $this->Nmea['baud'] = $split[3];
        $this->Nmea['rate'] = $split[4];
        $this->Nmea['status'] = $split[5];
    }

    private function GPMSS($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['ss'] = $split[1];
        $this->Nmea['snr'] = $split[2];
        $this->Nmea['freq'] = $split[3];
        $this->Nmea['bps'] = $split[4];
    }

    private function GPRMA($geostr) {
//Not yet implemented.
    }

    private function GPRMB($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['status'] = $split[1];
        $this->Nmea['cte'] = $split[2];
        $this->Nmea['lr'] = $split[3];
        $this->Nmea['owi'] = $split[4];
        $this->Nmea['dwi'] = $split[5];
        $this->Nmea['lat'] = $this->degree2decimal($split[6], $split[7]);
        $this->Nmea['ns'] = $split[7];
        $this->Nmea['long'] = $this->degree2decimal($split[8], $split[9]);
        $this->Nmea['ew'] = $split[9];
        $this->Nmea['rd'] = $split[10];
        $this->Nmea['tbd'] = $split[11];
        $this->Nmea['vtd'] = $split[12];
        $this->Nmea['aa'] = $split[13];
    }

    private function GPRMC($geostr) {
        $split = explode(",", $geostr);
        $this->CurrentUTC = $this->fixUTC($split[1]);
        $this->Nmea['utc'] = $this->fixUTC($split[1]);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['statusrmc'] = $split[2];
        $this->Nmea['lat'] = $this->degree2decimal($split[3], $split[4]);
        $this->Nmea['ns'] = $split[4];
        $this->Nmea['long'] = $this->degree2decimal($split[5], $split[6]);
        $this->Nmea['ew'] = $split[6];
        $this->Nmea['speed'] = $split[7];
        $this->Nmea['track'] = $split[8];
        $this->Nmea['date'] = $split[9];
        $this->Nmea['magvar'] = $split[10];
        $this->Nmea['mag_ew'] = trim($split[11]);
        if ($this->CurrentUTC && $split[9])
            $this->Nmea['Unix'] = $this->NMEAtoUnixTime($this->CurrentUTC, $split[9]);
    }

    private function GPRTE($geostr) {
//Not yet implemented.
    }

    private function GPTRF($geostr) {
//Not yet implemented.
    }

    private function GPSTN($geostr) {
//Not yet implemented.
    }

    private function GPVBW($geostr) {
//Not yet implemented.
    }

    private function GPVTG($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['trkdeg1'] = $split[1];
        $this->Nmea['t'] = $split[2];
        $this->Nmea['trkdeg2'] = $split[3];
        $this->Nmea['m'] = $split[4];
        $this->Nmea['spdknots'] = $spdk = $split[5];
        $this->Nmea['knots'] = $split[6];
        $this->Nmea['spdkmph'] = $split[7];
        $this->Nmea['kph'] = $split[8];
    }

    private function GPWCV($geostr) {
//Not yet implemented.
    }

    private function GPWPL($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['lat'] = $this->degree2decimal($split[1], $split[2]);
        $this->Nmea['ns'] = $split[2];
        $this->Nmea['long'] = $this->degree2decimal($split[3], $split[4]);
        $this->Nmea['ew'] = $split[4];
        $this->Nmea['name'] = $split[5];
    }

    private function GPXTC($geostr) {
//Not yet implemented.
    }

    private function GPXTE($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['gw'] = $split[1];
        $this->Nmea['unused'] = $split[2];
        $this->Nmea['cted'] = $split[3];
        $this->Nmea['lr'] = $split[4];
        $this->Nmea['units'] = $split[5];
    }

    private function GPXTG($geostr) {
//Not yet implemented.
    }

    private function GPZDA($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['hmss'] = $split[1];
        $this->Nmea['day'] = $split[2];
        $this->Nmea['mnt'] = $split[3];
        $this->Nmea['year'] = $split[4];
        $this->Nmea['lch'] = $split[5];
        $this->Nmea['lcm'] = $split[6];
    }

    private function GPZTG($geostr) {
//Not yet implemented.
    }

    private function HCHDG($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['hdg'] = $split[1];
        $this->Nmea['var'] = $split[4];
        $this->Nmea['w'] = $split[5];
    }

    private function PSLIB($geostr) {
//Not yet implemented.
    }

    private function PGRME($geostr) {
//Not yet implemented.
    }

    private function PGRMM($geostr) {
//Not yet implemented.
    }

    private function PGRMZ($geostr) {
//Not yet implemented.
    }

    private function SDDBT($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['feet'] = $split[1];
        $this->Nmea['ft'] = $split[2];
        $this->Nmea['meters'] = $split[3];
        $this->Nmea['m'] = $split[4];
        $this->Nmea['fathoms'] = $split[5];
        $this->Nmea['f'] = $split[6];
    }

    private function SDDPT($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['meters'] = $split[1];
        $this->Nmea['offset'] = $split[2];
        $this->Nmea['max'] = $split[3];
    }

    private function SDMTW($geostr) {
        $split = explode(",", $geostr);
        $this->Nmea['type'] = $split[0];
        $this->Nmea['temp'] = $split[1];
        $this->Nmea['scale'] = $split[2];
    }

}

?>
