var transform = require('./transform.js');
var config = require('./config');
var request = require('request');
var net = require('net');
var mysql_driver = require('mysql');
var mysql = mysql_driver.createConnection(config.db);
mysql.connect();
mysql.query("SET time_zone = '"+config.time_offset+"'");
 var cache = {};
                        // Build the cache
                           var csql = "SELECT * FROM (SELECT accountID, deviceID,equipmentType, uniqueID FROM Device ORDER BY uniqueID DESC) sub GROUP BY uniqueID";
                           mysql.query(csql, function(err, rows, fields) {
                            for (key in rows) {
                           cache[rows[key].uniqueID] = {};
                           cache[rows[key].uniqueID].accountID = rows[key].accountID;
                           cache[rows[key].uniqueID].deviceID = rows[key].deviceID;
                           cache[rows[key].uniqueID].equipmentType = rows[key].equipmentType;
                           }
                           });
var Buffer = require('buffer').Buffer;
var dgram = require('dgram');

var recvMsg = function (msg, rinfo) {
  console.log('got message from '+ rinfo.address +':'+ rinfo.port);
  var data = msg;
        var strs = data;
        var buffer = strs.toString('hex');
        var head = buffer.slice(0,2);
        if ( head == 24 ){
            //todo with the hex parse
            // 2421302257000329022402112232103700113284958E000161FFFFFBFFFF000F
            if ( buffer.length < 1025 ){
                   var strArr = [];
                   var n = 64;
                   for (var i = 0, l = buffer.length; i < l/n; i++) {
                     var a = buffer.slice(n*i, n*(i+1));
                      strArr.push(a);
                    }
                   for (var m in strArr) {
                   var msg = strArr[m];
                  
                    var uniqueID = msg.slice(2,12);
                    var date = msg.slice(18,24);
                    var time = msg.slice(12,18);
                    var lat = msg.slice(24,32);
                    var long = msg.slice(34,43);
                    var speedkph = msg.slice(44,47);
		                var heading = msg.slice(47,50);

                    var enav = msg.slice(43,44);
                    var enavhex = enav.toString('hex');
                    var enavdigi = parseInt(enavhex,16);
                    var enavbin = enavdigi.toString(2); 
                    var latdeg0 = (lat/10000);
                    var longdeg0 = (long/10000);
                    var latdeg = latdeg0.toString();
                    var longdeg = longdeg0.toString();

                    function FormatNum(Source,Length){
                        var strTemp="";
                        for(i=1;i<=Length-Source.length;i++){
                            strTemp+="0";
                        }
                        return strTemp+Source;
                    }
                    //FF FF FB FF
                    var statusstring =msg.slice(50,58);
                    function tks(a,b)
                        {
                    var s0 = statusstring.slice(a,8);
                    var sstring = s0.slice(0,1)
                    var shex = sstring.toString('hex');
                    var sdigi = parseInt(shex,16);
                    var sbin = sdigi.toString(2);
                    var hex = FormatNum(sbin,4);
                    var status0 = hex.slice(b,4);
                    var status = status0.slice(0,1);
                    return status;        
                    }};
                    
                    var enavarr = FormatNum(enavbin,4);
                    //console.log(enavarr);
                    var enavb1 = enavarr.slice(0,1);
                    var enavb2 = enavarr.slice(1,2);
                    var enavb3 = enavarr.slice(2,3);
                    var enavb4 = enavarr.slice(3,4);
                     if (enavb1 == 1){
                          lew = "E";
                     }else{
                          lew = "W";
                     };
                     if (enavb2 == 1){
                          lns = "N";
                     }else{
                          lns = "S";
                     }
                /*
                tks(0,0); // 2) status:ground sensor 1 
                tks(0,1); // 2) alarm:+12V sensor 2
                tks(0,2); // 3) alarm:+12V sensor 1
                tks(0,3); // 4) alarm:main power is cut
                tks(1,0); // 5) status:oil & engine is cut
                tks(1,1); // 6) alarm:gprs block (live alarm in sms dcs)
                tks(1,2); // 7) alarm:passwd wrong
                tks(1,3); // 8) alarm:temp alarm
                tks(2,0); // 9) alarm:ground sensor 2 
                tks(2,1); // 10)status:gps antenna is circuit
                tks(2,2); // 11)status:gps antenna status is open 
                tks(2,3); // 12)status:main power cut
                tks(3,0); // 13)status:pwoer by backup battery
                tks(3,1); // 14)null
                tks(3,2); // 15)alarm:analog overrun
                tks(3,3); // 16)alarm:gps chip erroe
                tks(4,0); // 17)status:speeding
                tks(4,1); // 18)alarm:custom alarm
                tks(4,2); // 19)status:engine is on
                tks(4,3); // 20)null
                tks(5,0); // 21)null
                tks(5,1); // 22)status:ACC on
                tks(5,2); // 23)status:tracker is fortify
                tks(5,3); // 24)status:door open
                tks(6,0); // 25)alarm:geofence out alarm
                tks(6,1); // 26)alarm:gps antenne circuit alarm 
                tks(6,2); // 27)alarm:gps antenne open alarm 
                tks(6,3); // 28)alarm:geofence in alarm
                tks(7,0); // 29)alarm:start the engine alarm
                tks(7,1); // 30)alarm:speeding alarm
                tks(7,2); // 31)alarm:panic alarm
                tks(7,3); // 32)alarm:thief alarm
                */
             console.log(msg);
            // console.log(tks(5,1)); 
            if(tks(5,1) == 0){
                        var statusCode = "62144"
                    }
                    else  {
                        var statusCode = "61714"
                    }
                  
                    //console.log(statusCode);
                    var gpsdate = date.replace( /([0-9]{2})([0-9]{2})([0-9]{2})/, function( match, day, month, year ) {
                        return '20'+ year +'-'+ month +'-'+ day
                    })
                    var gpstime = time.replace( /([0-9]{2})([0-9]{2})([0-9]{2})/, function( match, hour, minute, second ) {
                        return hour +':'+ minute +':'+ second

                    })

                    function convert_coord(coord, direction) {
                        var dot = coord.indexOf('.');
                        var deg = parseInt(coord.substring(0, dot - 2));
                        var mins = parseFloat(coord.substring(dot - 2));

                        return (deg + (mins / 60)) * ((direction == 'S' || direction == 'W') ? -1 : 1);
                    }
                    var lon1 = convert_coord(longdeg,lns);
                    var lat1 = convert_coord(latdeg, lew);
                    //console.log(lon1);
                    //console.log(lat1);
                    var fixgeo = transform.wgs2gcj(lat1,lon1);
                    var fixlat = fixgeo.lat;
                    var fixlng = fixgeo.lng;
                    var datum = {
                        'uniqueID'  : uniqueID,
                        'data_type' : uniqueID,
                        'points'    : [],
                    };

                    
                            datum.points.push({
                                'type'      : uniqueID,
                                'lon'       : fixlng,
                                'lat'       : fixlat,
                                'speed'     : speedkph,
                                'direction' : heading,
                                'date'      : gpsdate,
                                'time'      : gpstime,
                            });
                            var val_strings = [];
                             console.log("GPS fix:" + enavb3);
                          if (cache[datum.uniqueID].equipmentType  == "vehicle" && enavb3== 1){
                            for (key in datum.points) {
                            var account = cache[datum.uniqueID].accountID;
                            var device = cache[datum.uniqueID].deviceID; 
                            request('http://apis.map.qq.com/ws/geocoder/v1/?location='+datum.points[key].lat+','+datum.points[key].lon+'&key=7H6BZ-L4234-UXBUA-XUY5O-L7OZK-GOF4M', 
                               function (error, response, body) { if (!error && response.statusCode == 200) {
                               var result = JSON.parse(body);
                               var gg = result["result"]["address"];                               
                               console.log(gg)                               
                               val_strings.push('("'+account+'","'+device+'","'+statusCode+'","'+datum.points[key].type+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',"'+gg+'",UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');
                                if (enavb3 == 1){
                                    if (speedkph == "000" ) {
                                       console.log("lng update timestamp")
                                       sql='UPDATE EventData set timestamp=UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'") WHERE accountID="'+account+'" and deviceID="'+device+'"  ORDER BY timestamp DESC LIMIT 1 '
                                       mysql.query(sql);
                                      }
                                    else {
                               var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,dataSource,longitude,latitude,speedKPH,heading,address,timestamp) VALUES ' + val_strings.join(',');
                                try {
                                    mysql.query(sql,function(err){
                                            if(err){
                                                return
                                            }
                                        }
                                    );
                                }
                                catch(err) {
                                   
                                    console.log(err);
                                } 
                                console.log(val_strings)                            
                                console.log("Logged eventdate Interval");
                                sql1='UPDATE Device set lastValidLatitude="'+datum.points[key].lat+'",lastValidLongitude="'+datum.points[key].lon+'",lastEventTimestamp=UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"),notes="'+gg+'"  WHERE accountID="'+account+'" and deviceID="'+device+'"'
                                   mysql.query(sql1);  
                                try {
                                    mysql.query(sql1,function(err){
                                            if(err){
                                                return
                                            }
                                        }
                                    )
                                }
                                catch(err) {                                   
                                    console.log(err);
                                }                             
                                console.log("update device data");        
                                    }                       
                                }
                               } 
                            })
                               }
                            }
                           //end of inster date
                          else if(enavb3== 1) {
                            for (key in datum.points) {
                            var account = cache[datum.uniqueID].accountID;
                            var device = cache[datum.uniqueID].deviceID; 
                            request('http://apis.map.qq.com/ws/geocoder/v1/?location='+datum.points[key].lat+','+datum.points[key].lon+'&key=7H6BZ-L4234-UXBUA-XUY5O-L7OZK-GOF4M', 
                               function (error, response, body) { if (!error && response.statusCode == 200) {
                               var result = JSON.parse(body);
                               var gg = result["result"]["address"];                               
                               console.log(gg)                               
                               val_strings.push('("'+account+'","'+device+'","'+statusCode+'","'+datum.points[key].type+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',"'+gg+'",UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');                                  
                               var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,dataSource,longitude,latitude,speedKPH,heading,address,timestamp) VALUES ' + val_strings.join(',');
                                try {
                                    mysql.query(sql,function(err){
                                            if(err){
                                                return
                                            }
                                        }
                                    );
                                }
                                catch(err) {
                                   
                                    console.log(err);
                                } 
                                console.log(val_strings)                            
                                console.log("Logged eventdate Interval");
                                sql1='UPDATE Device set lastValidLatitude="'+datum.points[key].lat+'",lastValidLongitude="'+datum.points[key].lon+'",lastCellServingInfo="",lastEventTimestamp=UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"),notes="'+gg+'"  WHERE accountID="'+account+'" and deviceID="'+device+'"'
                                   mysql.query(sql1);  
                                try {
                                    mysql.query(sql1,function(err){
                                            if(err){
                                                return
                                            }
                                        }
                                    )
                                }
                                catch(err) {                                   
                                    console.log(err);
                                }                             
                                console.log("update device data");                                     
                               } 
                            })
                               }
                            } 
                            else {
                              console.log("Failed to Log");
                            }                         
                   }
                 }
          
          else{
            console.log("bad data");  
          }
}
  sock = dgram.createSocket("udp4", recvMsg);
  sock.bind(9988, '0.0.0.0');

 console.log("SIPGEAR location DCS started"); 







