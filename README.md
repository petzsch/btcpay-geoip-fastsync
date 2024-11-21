# btcpay-geoip-fastsync
Simple Mirror Select Script based on GeoIP data

## Requirements

* Apache 2.x Webserver with mod_redirect enabled
* PHP 8.0+

## Install Dependencies

* Download recent geoip2.phar from GeoIP2-PHP [Release Page](https://github.com/maxmind/GeoIP2-php/releases)
* place it in the maxmind folder
* Signup and Login for a [GeoIP Lite Account](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data/)
* Download the City and Country Databases, extract and untar them and place the 2 files (GeoLite2-City.mmdb and GeoLite2-Country.mmdb) in the maxmind folder

## Adding Mirrors

The mirror currently needs to be reachable via https (script could be modified to allow different protocols for each mirror) and have the mainnet (and in future maybe testnet) snapshot on it.

Simply extend the `$download_servers` variable to include additional servers.
