var transform = require('./transform.js');
var bscoords = require('bscoords');
var config = require('./config');
var geocoder = require('geocoder');
var net = require('net');
var mysql_driver = require('mysql');
var InNOut = require('in-n-out');
var nodemailer = require("nodemailer");
var smtpTransport  = nodemailer.createTransport("SMTP", {
    host: config.email_host, // hostname
    secureConnection: true, // use SSL
    port: config.email_port, // port for secure SMTP
    auth: {
        user: config.email_user,
        pass: config.email_pass,
    }
})
var datelog = new Date();
var timelog = datelog.getHours();
var minutes = 1, the_interval = minutes * 60 * 1000;
var user_coll=[];
var mysql = mysql_driver.createConnection(config.db);
mysql.connect();
mysql.query("SET time_zone = '"+config.time_offset+"'");
var cache = {};
function datetime_to_unix(datetime){
    var tmp_datetime = datetime.replace(/:/g,'-');
    tmp_datetime = tmp_datetime.replace(/ /g,'-');
    var arr = tmp_datetime.split("-");
    var now = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]));
    return parseInt(now.getTime()/1000);
}

var csql = "SELECT * FROM (SELECT accountID, deviceID,equipmentType,expirationTime,uniqueID,deviceCode,serialNumber,notifyEmail,lastNotifyTime,lastNotifyCode FROM Device ORDER BY uniqueID DESC) sub GROUP BY uniqueID";
mysql.query(csql, function(err, rows, fields) {
    for (key in rows) {
        cache[rows[key].uniqueID] = {};
        cache[rows[key].uniqueID].accountID = rows[key].accountID;
        cache[rows[key].uniqueID].deviceID = rows[key].deviceID;
        cache[rows[key].uniqueID].expirationTime = rows[key].expirationTime;
        cache[rows[key].uniqueID].equipmentType = rows[key].equipmentType;
        cache[rows[key].uniqueID].deviceCode = rows[key].deviceCode;
        cache[rows[key].uniqueID].serialNumber = rows[key].serialNumber;
        cache[rows[key].uniqueID].notifyEmail = rows[key].notifyEmail;
        cache[rows[key].uniqueID].lastNotifyTime = rows[key].lastNotifyTime;
         cache[rows[key].uniqueID].lastNotifyCode = rows[key].lastNotifyCode;
    }
});
function getCityFromCoordinates(lat, long, cb){
 return geocoder.reverseGeocode(lat,long, function ( err, data ) {
    
    if(err){
       return cb(err.message);
    } else {
       if(data["results"].length == 0){ 
          return cb("not found");
       } else {
        var dd = JSON.stringify(data);
        var result = JSON.parse(dd);
        address = result.results[0].formatted_address;
       }//end
    }   
    cb(null, address);
 }, { language: 'zh_CN' }
 ); 
}

// Build the cache
setInterval(function() {
    console.log("query db account every 1 min");
    var csql = "SELECT * FROM (SELECT accountID, deviceID,equipmentType,expirationTime,uniqueID,deviceCode,serialNumber,notifyEmail,lastNotifyTime,lastNotifyCode FROM Device ORDER BY uniqueID DESC) sub GROUP BY uniqueID";
    mysql.query(csql, function(err, rows, fields) {
        for (key in rows) {
            cache[rows[key].uniqueID] = {};
            cache[rows[key].uniqueID].accountID = rows[key].accountID;
            cache[rows[key].uniqueID].deviceID = rows[key].deviceID;
            cache[rows[key].uniqueID].expirationTime = rows[key].expirationTime;
            cache[rows[key].uniqueID].equipmentType = rows[key].equipmentType;
            cache[rows[key].uniqueID].deviceCode = rows[key].deviceCode;
            cache[rows[key].uniqueID].serialNumber = rows[key].serialNumber;
            cache[rows[key].uniqueID].notifyEmail = rows[key].notifyEmail;
            cache[rows[key].uniqueID].lastNotifyTime = rows[key].lastNotifyTime;
            cache[rows[key].uniqueID].lastNotifyCode = rows[key].lastNotifyCode;
        }
    });
}, the_interval);        
function hex2a(hex) {
    var str = '';
    for (var i = 0; i < hex.length; i += 2)
        str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
    return str;
}                                               
var server = net.createServer(function(c) {
   console.log('CONNECTED: ' +
        c.remoteAddress + ':' + c.remotePort);
    user_coll.push(c); 
    var buffer;
    var reset = function() {
        buffer = '';
    }
    reset();
    c.on('data', function(data) {
        var strs = data;
        var buffer = strs.toString('hex');
        var head = buffer.slice(0,2);
        var meihead = buffer.slice(0,4);     

    // tcp OBD

    if ( head == 24 ){
            if ( buffer.length < 1025 ){
                var flag = 64;
                var strArr = [];
                var n = 64;
                console.log("TK-NANO hearbeat");
                console.log(buffer);
                //var m = buffer.length/n;
                
                for (var i = 0, l = buffer.length; i < l/n; i++) {
                    var a = buffer.slice(n*i, n*(i+1));
                    strArr.push(a);
                }

                // console.log(strArr);
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
                    var enavb1 = enavarr.slice(0,1);
                    var enavb2 = enavarr.slice(1,2);
                    var enavb3 = enavarr.slice(2,3);
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

                    var datum = {
                        'uniqueID'  : uniqueID,
                        'data_type' : uniqueID,
                        'points'    : [],
                    };


                    datum.points.push({
                        'type'      : uniqueID,
                        'lon'       : convert_coord(longdeg,lew),
                        'lat'       : convert_coord(latdeg, lns),
                        'speed'     : speedkph,
                        'direction' : heading,
                        'date'      : gpsdate,
                        'time'      : gpstime,
                    });
                            var val_strings = [];
                            var new_end = 0;
                 
                       
                            for (key in datum.points) {
                                // Ignore invalid points             
                                if ( buffer.length) {
                                    var account = cache[datum.uniqueID].accountID;
                                    var device = cache[datum.uniqueID].deviceID;                                
                                    val_strings.push('("'+account+'","'+device+'","'+"61536"+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');
                                }
                                else {
                                    c.end()
                                }
                            }
                            if (enavb3== 10) {
                                var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,longitude,latitude,speedKPH,heading,timestamp) VALUES ' + val_strings.join(',');
                                try {
                                    mysql.query(sql,function(err){
                                            if(err){
                                                return
                                            }
                                        }
                                    );
                                }
                                catch(err) {
                                    c.end()
                                    console.log(err);
                                }
                                // console.log(heading);
                             
                    console.log("Logged Interval");
                    mysql.query('SELECT commandArgs,creationTime FROM PendingCommands WHERE accountID = "'+account+'" AND deviceID = "'+device+'" AND sendState = 0  ORDER BY creationTime DESC LIMIT 1', function(err, fields) {
                    if (fields.length > 0){
                    var res = fields[0];
                    var cmd = res['commandArgs'];
                    var createtime = res['creationTime'];
                    var dt = Date.parse(new Date())/1000; 
                    c.write(cmd)         
                     sql='UPDATE PendingCommands set queueTime="'+dt+'",sendState=1,sendTime="'+dt+'"  WHERE accountID="'+account+'" and deviceID="'+device+'" and creationTime = "'+createtime+'" '
                     mysql.query(sql);
                    }
                    })
                            }
                    else {
                        c.end()
                        console.log("Failed to Log");
                    }
                    

                }
            }  
//*HQ,1140917150,V1,130106,V,0000.0000,N,00000.0000,E,000.00,000,191014,FFFFFBFF#*HQ,1140917150,V3,130106,46002,04,010186,042489,62,-77,010186,009932,,,010186,043316,,,009846,008102,,,0282,0,X,191014,FFFFFBFF#
     // tcp SIPGEAR
    else if ( head == "2a" ){
            var hqdata = data.toString();
            console.log("hqdata: " + hqdata);
            var hqdataasc = hqdata.toString();
            var hqsplit = hqdataasc.split('#');
            var parse1 = hqsplit[0];
            var parse1asc = parse1.toString();
            var parse1split = parse1asc.split(',');
            var parse1cmd = parse1split[2];
            console.log("parse 1" + parse1);          
            var parse2 = hqsplit[1];
            var parse2asc = parse2.toString();
            var parse2split = parse2asc.split(',');
            var parse2cmd = parse2split[2];
            var gpsoff = parse2split[5];
            console.log("parse 2" + parse2);  
            if (parse1cmd == "V1"){
                console.log("gpson V1 date")
                    var bufferasc = parse1.toString();
                    var logdata = bufferasc.slice(4);
                    var split = bufferasc.split(',');
                    var datum = {
                        'uniqueID'      : split[1],
                        'data_type' : split[2],
                        'points'    : [],
                    };
                    var trackerid = split[1];
                    var lat = split[5];
                    var lng = split[7];
                    var lew = split[8];
                    var lns = split[6];
                    var type = split[2];
                    var S17 = split[3];
                    var valid = split[4];
                    function FormatNum(Source,Length){
                        var strTemp="";
                        for(i=1;i<=Length-Source.length;i++){
                            strTemp+="0";
                        }
                        return strTemp+Source;
                    }
                    if (parse1.length > 0){
                        var statusstring = split[12];
                        //FF FF FB FF
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
                    if (type == "V1" ){
                        function date(part) {
                            var date = split[11];
                            return date.substring(part * 2, (part * 2) + 2);
                        }
                        function time(part) {
                            var time = split[3];
                            return time.substring(part * 2, (part * 2) + 2);
                        }
                        function convert_coord(coord, direction) {
                            var dot = coord.indexOf('.');
                            var deg = parseInt(coord.substring(0, dot - 2));
                            var mins = parseFloat(coord.substring(dot - 2));
                            return (deg + (mins / 60)) * ((direction == 'S' || direction == 'W') ? -1 : 1);
                        }
                        var lon1 = convert_coord(lng,lns);
                        var lat1 = convert_coord(lat, lew);
                        var fixgeo = transform.wgs2gcj(lat1,lon1);
                        var fixlat = fixgeo.lat;
                        var fixlng = fixgeo.lng;
                       datum.points.push({
                            'type'      : split[2],
                            'lon'       : fixlng,
                            'lat'       : fixlat,
                            'speed'     : parseFloat(split[9]),
                            'direction' : parseFloat(split[10]),
                            'date'      : '20'+date(2)+'-'+date(1)+'-'+date(0),
                            'time'      : time(0)+':'+time(1)+':'+time(2),
                        });

                    
                         mysql.query('UPDATE Device SET ? WHERE uniqueID = "'+trackerid+'"',{deviceCode: "JUNLE Series",ipAddressCurrent: c.remoteAddress, remotePortCurrent: c.remotePort});
             var dt = Date.parse(new Date())/1000;
             var expTime = dt + 31449599;
             //typeof cache[datum.uniqueID].expirationTime == 'undefined'
             console.log("expTime" + cache[datum.uniqueID].expirationTime)
             if(cache[datum.uniqueID].expirationTime == '0'){
              mysql.query('UPDATE Device SET ? WHERE uniqueID = "'+trackerid+'"',{expirationTime: expTime}); 
              console.log("exptime insert db")  
             } 
                    // vehicle inster db
                   //  cache[datum.uniqueID].expirationTime;
                    if(cache[datum.uniqueID].equipmentType  == "vehicle"  ) {    
                            if (tks(5,1) == 1 && valid == "A" ){
                                var statusCode = "61713";
                                console.log("vehicle ACC ON " + statusCode);
                                    var val_strings = [];
                                    for (key in datum.points) {            
                                    var account = cache[datum.uniqueID].accountID;
                                    var device = cache[datum.uniqueID].deviceID;
                                    var add_lat = datum.points[key].lat;
                                    var add_lng = datum.points[key].lon;                                   
                                    getCityFromCoordinates(add_lat,add_lng, function(err, address) {
                                    if (err) {
                                    console.log('error: '+err);
                                      } else {
                                        var add_respond = address;
                                      console.log("Status Code " + statusCode);
                                         val_strings.push('("'+account+'","'+device+'","'+statusCode+'","'+datum.points[key].type+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',"'+add_respond+'",UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');  
                                         var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,dataSource,longitude,latitude,speedKPH,heading,address,timestamp) VALUES ' + val_strings.join(',');
                                        mysql.query(sql);   
                                        console.log("insert DB EventData")                                     
                                        sql1='UPDATE Device set lastValidLatitude="'+datum.points[key].lat+'",lastValidLongitude="'+datum.points[key].lon+'",lastEventTimestamp=UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"),notes="'+address+'" WHERE accountID="'+account+'" and deviceID="'+device+'"'
                                        mysql.query(sql1);
                                        
                                        }
                                     });                                  
                                }
                            }
                            else if (tks(5,1) == 0 && valid == "A"){
                                var statusCode = "61715";
                                console.log("vehicle ACC off" + statusCode);
                                    var val_strings = [];
                                    for (key in datum.points) {            
                                    var account = cache[datum.uniqueID].accountID;
                                    var device = cache[datum.uniqueID].deviceID;
                                    var add_lat = datum.points[key].lat;
                                    var add_lng = datum.points[key].lon;                                   
                                    getCityFromCoordinates(add_lat,add_lng, function(err, address) {
                                    if (err) {
                                    console.log('error: '+err);
                                      } else {
                                        var add_respond = address;
                                      console.log("Status Code " + statusCode);
                                         val_strings.push('("'+account+'","'+device+'","'+statusCode+'","'+datum.points[key].type+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',"'+add_respond+'",UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');  
                                         var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,dataSource,longitude,latitude,speedKPH,heading,address,timestamp) VALUES ' + val_strings.join(',');
                                        mysql.query(sql);   
                                        console.log("insert DB EventData")                                     
                                        sql1='UPDATE Device set lastValidLatitude="'+datum.points[key].lat+'",lastValidLongitude="'+datum.points[key].lon+'",lastEventTimestamp=UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"),notes="'+address+'" WHERE accountID="'+account+'" and deviceID="'+device+'"'
                                        mysql.query(sql1);
                                        
                                        }
                                     });                                  
                                }
                            }
                            else if (valid == "V"){
                                var  statusCode = "63505";
                                console.log("vehicle ACC off invalid" + parse1);
                            }
                            else if (valid == "A"){
                                var statusCode = "61472";
                                console.log("StandByGPS valid" + statusCode);
                                    var val_strings = [];
                                    for (key in datum.points) {            
                                    var account = cache[datum.uniqueID].accountID;
                                    var device = cache[datum.uniqueID].deviceID;
                                    var add_lat = datum.points[key].lat;
                                    var add_lng = datum.points[key].lon;                                   
                                    getCityFromCoordinates(add_lat,add_lng, function(err, address) {
                                    if (err) {
                                    console.log('error: '+err);
                                      } else {
                                        var add_respond = address;
                                       console.log("Status Code " + statusCode);
                                         val_strings.push('("'+account+'","'+device+'","'+statusCode+'","'+datum.points[key].type+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',"'+add_respond+'",UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');  
                                         var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,dataSource,longitude,latitude,speedKPH,heading,address,timestamp) VALUES ' + val_strings.join(',');
                                        mysql.query(sql);   
                                        console.log("insert DB EventData")                                     
                                        sql1='UPDATE Device set lastValidLatitude="'+datum.points[key].lat+'",lastValidLongitude="'+datum.points[key].lon+'",lastEventTimestamp=UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"),notes="'+address+'" WHERE accountID="'+account+'" and deviceID="'+device+'"'
                                        mysql.query(sql1);
                                        
                                        }
                                     });                                  
                                }
                            }
                            else if (valid == "V"){
                                var statusCode = "63505";
                                console.log("StandByGPS valid" + parse1);
                            }

                            }
                    // tracker inster db
                    else if(type == "V1") {    
                            if (valid == "A" ){
                                var statusCode = "61472";
                                console.log("tracker valid " + statusCode);
                                    var val_strings = [];
                                    for (key in datum.points) {            
                                    var account = cache[datum.uniqueID].accountID;
                                    var device = cache[datum.uniqueID].deviceID;
                                    var add_lat = datum.points[key].lat;
                                    var add_lng = datum.points[key].lon;                                   
                                    getCityFromCoordinates(add_lat,add_lng, function(err, address) {
                                    if (err) {
                                    console.log('error: '+err);
                                      } else {
                                        var add_respond = address;
                                      console.log("Status Code " + statusCode);
                                         val_strings.push('("'+account+'","'+device+'","'+statusCode+'","'+datum.points[key].type+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',"'+add_respond+'",UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');  
                                         var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,dataSource,longitude,latitude,speedKPH,heading,address,timestamp) VALUES ' + val_strings.join(',');
                                        mysql.query(sql);   
                                        console.log("insert DB EventData")                                     
                                        sql1='UPDATE Device set lastValidLatitude="'+datum.points[key].lat+'",lastValidLongitude="'+datum.points[key].lon+'",lastEventTimestamp=UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"),notes="'+address+'" WHERE accountID="'+account+'" and deviceID="'+device+'"'
                                        mysql.query(sql1);
                                        
                                        }
                                     });                                  
                                }
                            }
                            else if (valid == "V"){
                                var statusCode = "61473";
                                console.log("tracker invalid" + parse1);
                                   
                            }                    
                        }
   if (parse1cmd == "V3"){
             // *HQ,1140917150,V3,130106,46002,04,010186,042489,62,-77,010186,009932,,,010186,043316,,,009846,008102,,,0282,0,X,191014,FFFFFBFF#
             // *HQ,1140917150,V3,130040,46002,01,010186,009932,62,-77,027A,0,X,301114,FFFFFBFF
             console.log("gpsoff V3 date")
             var parse2_uniqueid = parse2split[1];  //id
             var parse2_cmd      = parse2split[2];  //cmd
             var parse2_time     = parse2split[3];  //time
             var parse2_mccmnc   = parse2split[4];  //mccmnc
             var parse2_mcc      = parse2_mccmnc.slice(0,2); //mcc
             var parse2_mnc      = parse2_mccmnc.slice(3,4); //mcc
             var parse2_base_num = parse2split[5];  //base_num
             var parse2_lac      = parse2split[6];  //lac
             var parse2_cell_id  = parse2split[7];  //cell_id
             var parse2_signal   = parse2split[8];  //signal
             var parse2_db       = parse2split[9];  //db
             var parse2_lac2     = parse2split[10];  //lac2 
             var parse2_fail_info = parse2split[23];  //fail_info
             var parse2_ext_info = parse2split[24];  //ext_info

             var parse2_status   = parse2split[26];  //status_info
                        function parse2date(part) {
                            var date = parse2_date;
                            return date.substring(part * 2, (part * 2) + 2);
                        }
                        function parse2time(part) {
                            var time = parse2_time ;
                            return time.substring(part * 2, (part * 2) + 2);
                        }
              if (parse2_base_num =="01"){
             var parse2_batt     = parse2split[10];  //battery             
             var parse2_date      = parse2split[13];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
               console.log(p2_date,p2_time); 
               }
             else if (parse2_base_num =="02"){
             var parse2_batt     = parse2split[14];  //battery             
             var parse2_date      = parse2split[17];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
               console.log(p2_date,p2_time); 
               }
             else if (parse2_base_num =="03"){
             var parse2_batt     = parse2split[18];  //battery             
             var parse2_date      = parse2split[21];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
               console.log(p2_date,p2_time); 
               }
               else if (parse2_base_num == "04"){
             var parse2_batt     = parse2split[22];  //battery
             var parse2_date      = parse2split[25];  //date             
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
             console.log(p2_date,p2_time);             
             }
               else if (parse2_base_num == "05"){
             var parse2_batt     = parse2split[26];  //battery             
             var parse2_date      = parse2split[29];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
             console.log(p2_date,p2_time);             
             }
               else if (parse2_base_num == "06"){
             var parse2_batt     = parse2split[30];  //battery             
             var parse2_date      = parse2split[33];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
             console.log(p2_date,p2_time);             
             };
             var p2_batt = parseInt(parse2_batt,16);
             var p2_batt_p = p2_batt/1024*5.6/3.7*100 
             var p2_batt_p1 = p2_batt_p.toString()
             var p2_batt_pe = p2_batt_p1.slice(0,4);
             var account = cache[datum.uniqueID].accountID;
             var device = cache[datum.uniqueID ].deviceID;
             mysql.query('UPDATE Device SET ? WHERE uniqueID = "'+parse2_uniqueid+'"',{lastBatteryLevel: p2_batt_pe});         
                 var onResponse = function(err, coords) {
                      if (err == null) {
                         if (typeof coords.cell != 'undefined') {
                          } 
                        else {
                        var statusCode = 61481;
                        var val_strings = [];
                       //var fixgeo = transform.gcj2wgs(coords.lat,coords.lon);                       
                        val_strings.push('("'+account+'","'+device+'","'+statusCode+'",'+coords.lon+','+coords.lat+',UNIX_TIMESTAMP("'+p2_date+' '+p2_time+'"))');  
                         sql1 = 'INSERT INTO EventData (accountID,deviceID,statusCode,longitude,latitude,timestamp) VALUES ' + val_strings.join(',');
                         console.log(sql1)
                         mysql.query(sql1);
                         sql='UPDATE Device set lastValidLatitude="'+coords.lat+'",lastValidLongitude="'+coords.lon+'",lastCellServingInfo="'+parse2_lac+'",lastEventTimestamp=UNIX_TIMESTAMP("'+p2_date+' '+p2_time+'"),notes="" WHERE accountID="'+account+'" and deviceID="'+device+'"'
                         mysql.query(sql);                                     
                        }
                       }
                      }
                      console.log()
                      if (parse2cmd == "V3"){
                       bscoords.requestGoogle(parse2_mcc,parse2_mnc,parse2_lac,parse2_cell_id, onResponse);
                      }   
                     
                    // sent command  
                    mysql.query('SELECT commandArgs,creationTime FROM PendingCommands WHERE accountID = "'+account+'" AND deviceID = "'+device+'" AND sendState = 0  ORDER BY creationTime DESC LIMIT 1', function(err, fields) {
                    if (fields.length > 0){
                    var res = fields[0];
                    var cmd = res['commandArgs'];
                    var createtime = res['creationTime'];
                    var dt = Date.parse(new Date())/1000; 
                    c.write(cmd)         
                     sql='UPDATE PendingCommands set queueTime="'+dt+'",sendState=1,sendTime="'+dt+'"  WHERE accountID="'+account+'" and deviceID="'+device+'" and creationTime = "'+createtime+'" '
                     mysql.query(sql);
                    }
                    })            
            }  
                    };
                  
              
                    // start command by acc
                    if (type == "V1" && cache[datum.uniqueID].equipmentType  == "vehicle") {
                            if (tks(5,1) == 1 && type == "V1"){
                                c.write("*HQ,0000,S17,130305,"+ config.acc_on_interval +","+ config.log_times_to_send +"#")
                                console.log("====acc on ===");
                            }
                            else if (tks(5,1) == 0 && type == "V1"){
                                c.write("*HQ,0000,S17,130305,"+ config.acc_off_interval +","+ config.log_times_to_send +"#")
                                console.log("====acc off ===");
                                console.log(logdata)
                                console.log(datelog + timelog)
                            }
                    }
                    else {
                       c.write("*HQ,0000,S17,130305,"+ config.default_interval +","+ config.log_times_to_send +"#")
                    }                 
       
   if (parse2cmd == "V3"){
             // *HQ,1140917150,V3,130106,46002,04,010186,042489,62,-77,010186,009932,,,010186,043316,,,009846,008102,,,0282,0,X,191014,FFFFFBFF#
             // *HQ,1140917150,V3,130040,46002,01,010186,009932,62,-77,027A,0,X,301114,FFFFFBFF
             console.log("gpsoff V3 date")
             var parse2_uniqueid = parse2split[1];  //id
             var parse2_cmd      = parse2split[2];  //cmd
             var parse2_time     = parse2split[3];  //time
             var parse2_mccmnc   = parse2split[4];  //mccmnc
             var parse2_mcc      = parse2_mccmnc.slice(0,2); //mcc
             var parse2_mnc      = parse2_mccmnc.slice(3,4); //mcc
             var parse2_base_num = parse2split[5];  //base_num
             var parse2_lac      = parse2split[6];  //lac
             var parse2_cell_id  = parse2split[7];  //cell_id
             var parse2_signal   = parse2split[8];  //signal
             var parse2_db       = parse2split[9];  //db
             var parse2_lac2     = parse2split[10];  //lac2 
             var parse2_fail_info = parse2split[23];  //fail_info
             var parse2_ext_info = parse2split[24];  //ext_info

             var parse2_status   = parse2split[26];  //status_info
                        function parse2date(part) {
                            var date = parse2_date;
                            return date.substring(part * 2, (part * 2) + 2);
                        }
                        function parse2time(part) {
                            var time = parse2_time ;
                            return time.substring(part * 2, (part * 2) + 2);
                        }
              if (parse2_base_num =="01"){
             var parse2_batt     = parse2split[10];  //battery             
             var parse2_date      = parse2split[13];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
               console.log(p2_date,p2_time); 
               }
             else if (parse2_base_num =="02"){
             var parse2_batt     = parse2split[14];  //battery             
             var parse2_date      = parse2split[17];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
               console.log(p2_date,p2_time); 
               }
             else if (parse2_base_num =="03"){
             var parse2_batt     = parse2split[18];  //battery             
             var parse2_date      = parse2split[21];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
               console.log(p2_date,p2_time); 
               }
               else if (parse2_base_num == "04"){
             var parse2_batt     = parse2split[22];  //battery
             var parse2_date      = parse2split[25];  //date             
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
             console.log(p2_date,p2_time);             
             }
               else if (parse2_base_num == "05"){
             var parse2_batt     = parse2split[26];  //battery             
             var parse2_date      = parse2split[29];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
             console.log(p2_date,p2_time);             
             }
               else if (parse2_base_num == "06"){
             var parse2_batt     = parse2split[30];  //battery             
             var parse2_date      = parse2split[33];  //date
             var p2_date = "20"+parse2date(2)+"-"+parse2date(1)+"-"+parse2date(0);
             var p2_time =  parse2time(0)+":"+parse2time(1)+":"+parse2time(2);
             console.log(p2_date,p2_time);             
             };
             var p2_batt = parseInt(parse2_batt,16);
             var p2_batt_p = p2_batt/1024*5.6/3.7*100 
             var p2_batt_p1 = p2_batt_p.toString()
             var p2_batt_pe = p2_batt_p1.slice(0,4);
             var batt = parseInt(p2_batt_pe)
             if (batt < 20){
                 c.write("*HQ,000,S41,130305,6,4,1,1,1,1,1,1,1#")    
             }
             var account = cache[datum.uniqueID].accountID;
             var device = cache[datum.uniqueID ].deviceID;

             var lac = parseInt(parse2_lac)
             var cell = parseInt(parse2_cell_id)
             mysql.query('SELECT O_LNG,O_LAT FROM Loc WHERE LAC = "'+lac+'" AND CELL = "'+cell+'"', function(err, fields) {
                var res = fields[0];
                   if (fields.length > 0){
                    var lat = res['O_LAT'];
                    var lng = res['O_LNG'];
                    console.log("Loc" + lat + "##" + lng) 
                    var statusCode = 61481;
                    var val_strings = [];                
                    val_strings.push('("'+account+'","'+device+'","'+statusCode+'",'+lng+','+lat+',UNIX_TIMESTAMP("'+p2_date+' '+p2_time+'"))');
                    sql1 = 'INSERT INTO EventData (accountID,deviceID,statusCode,longitude,latitude,timestamp) VALUES ' + val_strings.join(',');
                     console.log("Local LOC")
                         mysql.query(sql1);
                         sql='UPDATE Device set lastBatteryLevel="'+p2_batt_pe+'",lastValidLatitude="'+lat+'",lastValidLongitude="'+lng+'",lastCellServingInfo="'+parse2_lac+'",lastEventTimestamp=UNIX_TIMESTAMP("'+p2_date+' '+p2_time+'"),notes="" WHERE accountID="'+account+'" and deviceID="'+device+'"'
                         mysql.query(sql); 
                   }
                   else{
                     var onResponse = function(err, coords) {
                      if (err == null) {
                         if (typeof coords.cell != 'undefined') {
                          } 
                        else {
                        var statusCode = 61481;
                        var val_strings = [];                
                        val_strings.push('("'+account+'","'+device+'","'+statusCode+'",'+coords.lon+','+coords.lat+',UNIX_TIMESTAMP("'+p2_date+' '+p2_time+'"))');
                         sql1 = 'INSERT INTO EventData (accountID,deviceID,statusCode,longitude,latitude,timestamp) VALUES ' + val_strings.join(',');
                         console.log(sql1)
                         mysql.query(sql1);
                         sql='UPDATE Device set lastBatteryLevel="'+p2_batt_pe+'",lastValidLatitude="'+coords.lat+'",lastValidLongitude="'+fixlng+'",lastCellServingInfo="'+parse2_lac+'",lastEventTimestamp=UNIX_TIMESTAMP("'+p2_date+' '+p2_time+'"),notes="" WHERE accountID="'+account+'" and deviceID="'+device+'"'
                         mysql.query(sql);                                     
                        }
                       }
                      }
                      console.log()
                      if (parse2cmd == "V3"){
                       bscoords.requestGoogle(parse2_mcc,parse2_mnc,parse2_lac,parse2_cell_id, onResponse);
                        console.log("GOOGLE LOC" + parse2_lac + parse2_cell_id)
                      }     
                   }
               })
                  /*
 
                    */ 
                    // sent command  
                    mysql.query('SELECT commandArgs,creationTime FROM PendingCommands WHERE accountID = "'+account+'" AND deviceID = "'+device+'" AND sendState = 0  ORDER BY creationTime DESC LIMIT 1', function(err, fields) {
                    if (fields.length > 0){
                    var res = fields[0];
                    var cmd = res['commandArgs'];
                    var createtime = res['creationTime'];
                    var dt = Date.parse(new Date())/1000; 
                    c.write(cmd)         
                     sql='UPDATE PendingCommands set queueTime="'+dt+'",sendState=1,sendTime="'+dt+'"  WHERE accountID="'+account+'" and deviceID="'+device+'" and creationTime = "'+createtime+'" '
                     mysql.query(sql);
                    }
                    })            
            }                  
// location one  V4
            else if (type  == "V4"){
                    if (split[4].length > 9 ){
                        
                       var cmd = "Location interval";
                         var datum = {
                        'uniqueID'      : split[1],
                        'points'    : [],
                    };
                    var trackerid = split[1];
                    var lat = split[8];
                    var lng = split[10];
                    var lew = split[11];
                    var lns = split[9];
                    function S17_FormatNum(Source,Length){
                        var strTemp="";
                        for(i=1;i<=Length-Source.length;i++){
                            strTemp+="0";
                        }
                        return strTemp+Source;
                    }
                    if (cmd.length > 0){
                        var statusstring = split[1];
                        function S17_tks(a,b)
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
                   
                        function S17_date(part) {
                            var date = split[14];

                            return date.substring(part * 2, (part * 2) + 2);
                        }
                        function S17_time(part) {
                            var time = split[6];

                            return time.substring(part * 2, (part * 2) + 2);
                        }

                        function S17_convert_coord(coord, direction) {
                            var dot = coord.indexOf('.');

                            var deg = parseInt(coord.substring(0, dot - 2));
                            var mins = parseFloat(coord.substring(dot - 2));

                            return (deg + (mins / 60)) * ((direction == 'S' || direction == 'W') ? -1 : 1);
                        }

                            datum.points.push({
                            'type'      : split[2],
                            'lon'       : S17_convert_coord(lng,lns),
                            'lat'       : S17_convert_coord(lat, lew),
                            'speed'     : parseFloat(split[12]),
                            'direction' : parseFloat(split[13]),
                            'date'      : '20'+S17_date(2)+'-'+S17_date(1)+'-'+S17_date(0),
                            'time'      : S17_time(0)+':'+S17_time(1)+':'+S17_time(2),
                        });
                 for (key in datum.points) {    
                    var statusCode = "61504";                
                    var val_strings = [];
                    for (key in datum.points) {            
                               
                                    var account = cache[datum.uniqueID].accountID;
                                    var device = cache[datum.uniqueID].deviceID;
                                    var add_lat = datum.points[key].lat;
                                    var add_lng = datum.points[key].lon;
                                    
                                    
                                    getCityFromCoordinates(add_lat,add_lng, function(err, address) {
                                    if (err) {
                                    console.log('error: '+err);
                                      } else {
                                        //console.log(address);
                                        var add_dat = address;
                                         val_strings.push('("'+account+'","'+device+'","'+statusCode+'","'+datum.points[key].type+'",'+datum.points[key].lon+','+datum.points[key].lat+','+datum.points[key].speed+','+datum.points[key].direction+',"'+add_dat+'",UNIX_TIMESTAMP("'+datum.points[key].date+' '+datum.points[key].time+'"))');  
                                       console.log( val_strings);
                                       var sql = 'INSERT INTO EventData (accountID,deviceID,statusCode,dataSource,longitude,latitude,speedKPH,heading,address,timestamp) VALUES ' + val_strings.join(',');
                                
                                    mysql.query(sql);
                                    console("V4 replay");
                                        }
                                     });                                  
                                }
                 }           
                    // end  S17 cmd/location           
                    }   
                    else if(split[3] == "S30"){
                      var cmd = "Alarm to DCS";
                    }
                    else if (split[3] == "S14"){
                      var cmd = "Speeding";
                    }
                    else if (split[3] == "R8"){
                      var cmd = "Voice monitoring";
                    }
                    else if (split[3] == "R7"){
                        var cmd = "Change IP & Port"
                    }
                    else if (split[3] == "S24"){
                        var cmd = "Change APN";                        
                    }
                    else if (split[3] == "S2"){
                        var cmd = "Change tracking Center SMS No.";
                    }
                    else if (split[3] == "S4"){
                        var cmd = "Config tracker";
                    }
                    else if (split[3] == "A1"){
                        var cmd = "Confirm alarm";
                    }
                    else if (split[3] == "R7"){
                        var cmd = "Clean alarm";
                    }
                    else if (split[3] == "S21"){
                        var cmd = "Geofence";
                    }
                    var log = parse1.slice(18,parse1.length)
                    var respond =  "<br> DeviceID: " + cache[datum.uniqueID].deviceID + "<br>" + "Command: " + cmd + "<BR>" + log; 
                     console.log(parse1);    
             for(var i=0;i<user_coll.length;i++){
                 var httpip = user_coll[i].remoteAddress;
                        if(httpip == config.gui_server_ip){
                            user_coll[i].write(respond);
                       console.log(respond);
                        }
                    }            
                 }          
            // end of monitor
             }
        } 
    // command to tracker 
   else if(head == 40){
        var bufferarr = data.toString();         
        var uid = bufferarr.substring(1,11);
                var command = bufferarr.substring(11,bufferarr.length);
                console.log(command);
                
                var sendcmd = "*HQ,0000," + command;
                console.log(sendcmd);
        mysql.query('SELECT ipAddressCurrent,remotePortCurrent FROM  Device WHERE uniqueID = "'+uid+'" ', function(err, fields) {
                    var res = fields[0];
                    var clientip = res['ipAddressCurrent'];
                    var clientport = res['remotePortCurrent'];
                    for(var i=0;i<user_coll.length;i++){
                        if(clientip==user_coll[i].remoteAddress
                            && clientport==user_coll[i].remotePort){
                            user_coll[i].write(sendcmd);
                       console.log(bufferarr);
                        }

                    }
                })
}        
        else{
        console.log('bad data')
        console.log('=======tcp======')
        console.log(data);
        c.end()
    }
    });
});
server.listen(9999, function() {
    console.log('SIPGEAR DCS Running');
});






