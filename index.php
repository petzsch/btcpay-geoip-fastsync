<?php
/**
 * Calculates the great-circle distance between two points, with
 * the Vincenty formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function vincentyGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return $angle * $earthRadius;
}

$download_servers = array(	0 => array(	'hostname'  => "snapshots.btcpay.tech",
						'longitude' => null,
						'latitude'  => null,
						'city'      => null,
						'distance'  => null),
				1 => array(	'hostname'  => "vienna.btcpay.host",
						'longitude' => null,
						'latitude'  => null,
						'city'      => null,
						'distance'  => null)
                          );

require_once( __DIR__ . "/maxmind/geoip2.phar");
use GeoIp2\Database\Reader;

// City DB
$reader = new Reader( __DIR__ . '/maxmind/GeoLite2-City.mmdb');
$record = $reader->city($_SERVER['REMOTE_ADDR']);
// or for Country DB
// $reader = new Reader('/path/to/GeoLite2-Country.mmdb');
// $record = $reader->country($_SERVER['REMOTE_ADDR']);

$i = 0;
foreach ($download_servers as $server) {
   //$record2 = $reader->city($server['ip']);
   $record2 = $reader->city(gethostbyname($server['hostname']));
   if (!isset($download_servers[$i]['longitude'])) {
      $download_servers[$i]['longitude'] = $record2->location->longitude;
      $download_servers[$i]['latitude'] = $record2->location->latitude;
   }
   $download_servers[$i]['city'] = $record2->city->name;
   $download_servers[$i]['distance'] = vincentyGreatCircleDistance(	$record->location->latitude, $record->location->longitude,
									$download_servers[$i]['latitude'], $download_servers[$i]['longitude']);
   $i++;
}

$dists = array_column($download_servers, 'distance');
$min = array_search(min($dists), $dists, true);

//echo "Nearest download server is: " . $download_servers[$min]['hostname'] . "/" . $_GET['url'] . " in " . $download_servers[$min]['city']  . "<br>";
//echo "Distance to you (" . $record->city->name . ") is: " . $download_servers[$min]['distance'] / 1000 . " km";

header("Location: https://" . $download_servers[$min]['hostname'] . "/" . $_GET['url']);

?>
