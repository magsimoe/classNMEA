classNMEA
=========

This is a work in progress and probably riddled with errors. I have no ability to test all of these sentences in real life.
Only the ones my gps spits out, I know work 100%. The rest are just written by looking at examples online.
Feel free to use this in your projects. If you change anything to the better or add more sentences. Please let me know so I can update the class.
You may contact me at: magsimoe@gmail.com.


<?
include "classNMEA.php";
$nmea = new NmeaParser();
$nmea->ParseLine('$GPRMC,144644,A,5738.1727,N,01134.2490,E,2.4,130.0,260512,1.3,E,A*1A');
$data = $nmea->DumpNmea();
echo "Type: " . $data[type] . "<br>";
echo "Time: " . $data[utc] . "<br>";
echo "Fix (Active or Void): " . $data[statusrmc] . "<br>";
echo "Latitude: " . $data[lat] . "<br>";
echo "NS: " . $data[ns] . "<br>";
echo "Longitude: " . $data[long] . "<br>";
echo "EW: " . $data[ew] . "<br>";
echo "Speed: " . $data[speed] . "<br>";
echo "Course: " . $data[track] . "<br>";
echo "Date: " . $data[date] . "<br>";
echo "Magnetic Variation: " . $data[magvar] . "<br>";
echo "Magnetic Var.. EW: " . $data[mag_ew] . "<br>";
?>

Not yet implemented: 
GPAPA, GPGRS, GPGST, GPRMA, GPRTE, GPRTF, GPSTN, GPVBW, GPWCV, GPXTC, GPXTG, GPZTG, PSLIB, PGRME, PGRMM, PGRMZ. Among several more...
