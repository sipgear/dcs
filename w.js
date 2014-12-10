
var config = require('./config');
var express = require('express');
var webot = require('weixin-robot');
var app = express();
var mysql_driver = require('mysql');
var mysql = mysql_driver.createConnection(config.db);
mysql.connect();
mysql.query("SET time_zone = '"+config.time_offset+"'");
function padZero(number) {
    if (number < 10) {
        number = "0" + number;
    }

    return number;
}

function unixtime2YYMMDD(unixtime) {
    var unixTimestamp = new Date(unixtime * 1000) 
    var commonTime = unixTimestamp.toLocaleString()
    var temp = []; 
    var t = commonTime.split(' ');
    var mm = t[1];
    var dd = t[2];
    var yy = t[3];
    if (mm == "Jan"){
      mm = 01
    }
    else if (mm == "Feb"){
      mm =02
    }
    else if (mm == "Mar"){
      mm = 03
    }
    else if (mm == "Apr"){
      mm = 04
    }
    else if (mm == "May"){
      mm = 05
    }
    else if (mm == "Jun"){
      mm = 06
    }
    else if (mm == "Jul"){
      mm = 07
    }
    else if (mm == "Aug"){
      mm = 08
    }
    else if (mm == "Sep"){
      mm = 09
    }
    else if (mm == "Oct"){
      mm =10
    }
    else if (mm == "Nov"){
      mm = 11
    }
    else if (mm == "Dec"){
      mm =12
    }
    temp.push(yy);
    temp.push(mm); 
    temp.push(dd);
    return temp.join("-");
}
function unixtime2hhmmss(unixtime) {
    var milliseconds = unixtime * 1000,
        dateObject = new Date(milliseconds),
        temp = [];

    temp.push(dateObject.getHours().toString());
    temp.push(padZero(dateObject.getMinutes()));
    temp.push(padZero(dateObject.getSeconds()));

    return temp.join(":");
}


// 指定复消息
webot.set('subscribe', {
  pattern: function(info) {
    return info.is('event') && info.param.event === 'subscribe';
  },
  handler: function(info) {
    return '欢迎使用君乐定位系统，发送你定位器账号信息进行绑定，格式为: 帐号#用户#密码。试用请选择 【定位服务】-> 【试用产品】';
  }
});
webot.set('ly', 'adduser,account,user,passwd,sim \n adddevice,account,deviceID,uniqueID,simNum \n pwd,account,user,passwd,sim ');
webot.set('CLICK', {
  pattern: function(info) {
    return info.is('event') && info.param.event === 'CLICK';
  },
  handler: function(info, next) {
      if (info.param.eventKey == '绑定设备') {
        var reply = {
               title: '设备绑定',
               pic: 'http://liyume.qiniudn.com/2012617545066475468.jpg',
               url: 'http://liyume.qiniudn.com/2012617545066475468.jpg',
               description: '发送:帐号#用户#密码，进行绑定。如：'+
                            'zhongshan#user#passwd',
              }
        return next(null,reply) 
      }
      else if (info.param.eventKey == '购买产品') {
        var reply = {
               title: '购买产品',
               pic: 'http://liyume.qiniudn.com/2012617545066475468.jpg',
               url: 'http://geargts.com',
               description: '君乐定位产品分为：'+ '\n' +
                            'TK-S：内置电池，无需接线，可连续待机使用2年，适用于宠物、电动车、摩托车、汽车等等'+ '\n' +
                            'TK-N：内置电池，可以连接汽车OBD2接口，适用于汽车'+ '\n' +
                            'TK-Q：内置电池，集成北斗、GPS双定位，适用于汽车监控'+ '\n' +
                            '请联系销售加盟电话：13326960628'
              }
        return next(null,reply) 
      }
      else if (info.param.eventKey == '售后服务') {
        var reply = {
               title: '售后服务',
               pic: 'http://liyume.qiniudn.com/service.jpg',
               url: 'http://geargts.com',
               description: '所有产品提供1月包换，1年保修，终身维护服务：'+ '\n' +
                            '售后服务电话：13590800628',
              }        
        return next(null,reply) 
      }
      else if (info.param.eventKey == '联系我们') {
        var reply = {
               title: '联系我们',
               pic: 'http://liyume.qiniudn.com/contactus.jpg',
               url: 'http://geargts.com',
               description: '销售加盟电话：13326960628'+ '\n' +
                            '售后服务电话：13326960628'+ '\n' +
                            '地址：中山市火炬开发区'
              }        
        return next(null,reply) 
      }
       else if (info.param.eventKey == '系统介绍') {
        var reply = {
               title: '系统介绍',
               pic: 'http://liyume.qiniudn.com/system.jpg',
               url: 'http://geargts.com',
               description: '君乐定位系统基于美国Geotelematic系统开发，以微信公众平台作为用户终端，提供基于北斗、GPS的超长待机定位产品。满足社会对位置服务的需求',
              }        
        return next(null,reply) 
      }
      else if (info.param.eventKey == '工作原理') {
        var reply = {
               title: '工作原理',
               pic: 'http://liyume.qiniudn.com/topu.jpg',
               url: 'http://geargts.com',
               description: '君乐定位系统是架设于云计算平台上的位置服务系统，每个定位器内置1张SIM卡用于与定位系统通讯。定位系统响应并处理定位器的登陆和位置信息，保存于云平台数据库中，通知部署了应用服务器响应微信公众账号每个用户的定位和指令请求。用户只需关注君乐定位系统的微信公众账号，即可实现便捷定位服务。',
              }        
        return next(null,reply) 
      }
      else if (info.param.eventKey == '常见问题') {
        var reply = {
               title: '常见问题',
               pic: 'http://liyume.qiniudn.com/qa.jpg',
               url: 'http://geargts.com',
               description: '问: 定位器有没有年费？'+ '\n' +
                            '答: 定位器内置SIM，会产生通讯费，每年费用为100元。'+ '\n' +
                            '问: 为什么查询定位器位置每天只有3次定位数据？'+ '\n' +
                            '答: 定位器默认每天登陆3次服务器，并上传GSM基站位置，以保证内置的电池可以连续工作2年。'+ '\n' +
                            '问: 需要紧急定位设备的位置，该如何操作? '+ '\n' +
                            '答: 请通过微信发送报警指令，格式为：设备名称@报警 比如：1141023233@报警 设备会每30分钟报告一次精确位置信息'+ '\n' +
                            '问: 如何撤销报警? '+ '\n' +
                            '答: 请通过微信发送撤防指令，格式为：设备名称@撤防 比如：1141023233@撤防 设备会每30分钟报告一次精确位置信息'+ '\n' +
                            '问: 电池电量低时，如何处理？'+ '\n' +
                            '答: 每次查询位置均有显示设备的电量信息，请在设备电量低于20前，联系我们更换电池。费用为30元。'

              }        
        return next(null,reply) 
      }
      else if (info.param.eventKey == '报警撤防') {
        var reply = {
               title: '报警撤防',
               pic: 'http://liyume.qiniudn.com/alarm.jpg',
               url: 'http://geargts.com',
               description: '报警指令：设备名称@报警 比如：1141023233@报警'+ '\n' +
                            '紧急指令：设备名称@报警 比如：1141023233@紧急'+ '\n' +
                            '撤防指令：设备名称@撤防 比如：1141023233@撤防'+ '\n' +
                            '睡眠指令：设备名称@睡眠 比如：1141023233@睡眠'
              }        
        return next(null,reply) 
      }

      else if (info.param.eventKey == '指令查询') {
        var reply = {
               title: '指令查询',
               pic: 'http://liyume.qiniudn.com/command.jpg',
               url: 'http://geargts.com',
               description: '绑定指令：【帐号#用户#密码】'+ '\n' +
                            '紧急指令：【设备号@紧急】 '+ '\n' +'急指令执行时，定位器一直开启并每30秒返回一次定位数据，直到电源30%时自动进入报警指令状态'+ '\n' +
                            '报警指令：【设备号@报警】 '+ '\n' +'报警指令执行时定位器每1小时返回一次定位数据，直到电源20%时自动进入撤防指令'+ '\n' +
                            '撤防指令：【设备号@撤防】'+ '\n' +'撤防指令执行时，定位器早上、中午、晚上、凌晨各返回一次定位数据'+ '\n' +
                            '睡眠指令：【设设备号@睡眠】'+ '\n' +'撤防指令执行时，定位器每天12点返回一次定位数据'+ '\n' +
                            '改名指令：【设设备号@改名@新的设备名称】'+ '\n' +'系统返改名成功'+ '\n' +
                            '如：'+ '\n' +
                            '76030110#admin#passwd' + '\n' +
                            '1141023233@报警' + '\n' +
                            '1141023233@紧急' + '\n' +
                            '1141023233@撤防' + '\n' +
                            '1141023233@睡眠' + '\n' +
                            '1141023233@改名@大众汽车'

              }
        return next(null,reply) 
      }
      else if (info.param.eventKey == 'T'){
  
                mysql.query('SELECT accountID,userID,notes FROM User WHERE notes = "'+info.uid+'"', function(err, fields) {
                    var res = fields[0];
                    if (fields.length > 0){
                    var acc = res['accountID'];
                    var user = res['userID'];
                    //console.log(user)
                    //query grouplist
                    mysql.query('SELECT accountID,userID,groupID FROM GroupList WHERE accountID = "'+acc+'" AND userID = "'+user+'"', function(err, fields) {
                    var res = fields[0];
                    //start query groupID
                    if (fields.length > 0){
                      var acc = res['accountID'];
                      var grouplist = res['groupID']; 
                      // start query eventdata mysql
                      mysql.query('SELECT accountID,deviceID,equipmentType,expirationTime,description,lastBatteryLevel,lastValidLatitude,lastValidLongitude,lastEventTimestamp,lastCellServingInfo,notes FROM Device WHERE deviceID in (SELECT deviceID FROM DeviceList WHERE accountID = "'+acc+'" AND groupID = "'+grouplist+'")', function(err,rows,fields) {
                      var re2 = []                   
                      for(var i=0;i<rows.length;i++){
                      var accountID = rows[i].accountID;
                      var deviceID = rows[i].deviceID;
                      var description = rows[i].description;
                      var lastBatteryLevel = rows[i].lastBatteryLevel;
                      var address = rows[i].notes;
                      var longitude =  rows[i].lastValidLongitude;
                      var latitude = rows[i].lastValidLatitude;
                       var timestamp = rows[i].lastEventTimestamp;
                      var fix = rows[i].lastCellServingInfo;
                      if (fix.length > 0){
                        var fix = "基站定位"
                      }
                      else {
                        var fix = "精确定位"
                      }
                     if (rows[i].equipmentType == "vehicle"){
                        re2.push({
                      title: "设备名称:"+description+ '\n' +
                             "设备号："+deviceID+ '\n' +
                             "服务时限:"+unixtime2YYMMDD(expirationTime)+ '\n' +   
                             "电量:"+lastBatteryLevel+ '\n' +
                             "定位方式:"+fix+ '\n' +                          
                             "定位日期:"+unixtime2YYMMDD(timestamp)+ '\n' +
                             "定位时间:"+unixtime2hhmmss(timestamp)+ '\n' +
                             "设备位置:"+address, 
                      pic: 'http://liyume.qiniudn.com/2012617545066475468.jpg',
                      url: 'http://apis.map.qq.com/uri/v1/marker?marker=coord:'+latitude+','+longitude+';title:'+deviceID+'',
                      });

                      }
                      else { 
                      re2.push({
                      title: "设备名称:"+description+ '\n' +
                             "设备号："+deviceID+ '\n' +
                             "服务时限:"+unixtime2YYMMDD(expirationTime)+ '\n' +   
                             "电量:"+lastBatteryLevel+ '\n' +
                             "定位方式:"+fix+ '\n' +                          
                             "定位日期:"+unixtime2YYMMDD(timestamp)+ '\n' +
                             "定位时间:"+unixtime2hhmmss(timestamp)+ '\n' +
                             "设备位置:"+address, 
                      pic: 'http://liyume.qiniudn.com/standbygps.png',
                      url: 'http://apis.map.qq.com/uri/v1/marker?marker=coord:'+latitude+','+longitude+';title:'+deviceID+'',                                                 
                      });                         
                      
                     }
                    }
                     var re3 = ({
                      title: "君乐定位系统",
                      pic: 'http://liyume.qiniudn.com/head.jpg',
                      url: 'https://standbygps.com',
                      });
 
                    re2.unshift(re3) 
                    return next(null,re2)    
                    } 
                    )// end query eventdata mysql

                    }//end of query groupid
                   //start all group
                    else {
                    mysql.query('SELECT accountID,deviceID,equipmentType,expirationTime,description,lastBatteryLevel,lastValidLatitude,lastValidLongitude,lastEventTimestamp,lastCellServingInfo,notes FROM Device WHERE accountID in (SELECT accountID FROM User WHERE notes = "'+info.uid+'")', function(err,rows,fields) {
                     var re2 = []                   
                    for(var i=0;i<rows.length;i++){
                      var accountID = rows[i].accountID;
                      var deviceID = rows[i].deviceID;
                      var expirationTime = rows[i].expirationTime;
                      var description = rows[i].description;
                      var lastBatteryLevel = rows[i].lastBatteryLevel;
                       var address = rows[i].notes;
                      var longitude =  rows[i].lastValidLongitude;
                      var latitude = rows[i].lastValidLatitude;
                       var timestamp = rows[i].lastEventTimestamp;
                      var fix = rows[i].lastCellServingInfo;
                      if (fix.length > 0){
                        var fix = "基站定位"
                      }
                      else {
                        var fix = "精确定位"
                      }
                      
                     if (rows[i].equipmentType == "vehicle"){
                        re2.push({
                      title: "设备名称:"+description+ '\n' +
                             "设备号："+deviceID+ '\n' +
                             "有效期:"+unixtime2YYMMDD(expirationTime)+ '\n' +   
                             "电量:"+lastBatteryLevel+ '\n' +
                             "定位方式:"+fix+ '\n' +                          
                             "日期:"+unixtime2YYMMDD(timestamp)+ '\n' +
                             "时间:"+unixtime2hhmmss(timestamp)+ '\n' +
                             "设备位置:"+address,  
                      pic: 'http://liyume.qiniudn.com/2012617545066475468.jpg',
                      url: 'http://apis.map.qq.com/uri/v1/marker?marker=coord:'+latitude+','+longitude+';title:'+deviceID+'',
                      });

                      }
                      else { 
                      re2.push({
                      title: "设备名称:"+description+ '\n' +
                             "设备号："+deviceID+ '\n' +
                             "有效期:"+unixtime2YYMMDD(expirationTime)+ '\n' +   
                             "电量:"+lastBatteryLevel+ '\n' +
                             "定位方式:"+fix+ '\n' +                          
                             "日期:"+unixtime2YYMMDD(timestamp)+ '\n' +
                             "时间:"+unixtime2hhmmss(timestamp)+ '\n' +
                             "设备位置:"+address,
                      pic: 'http://liyume.qiniudn.com/standbygps.png',
                      url: 'http://apis.map.qq.com/uri/v1/marker?marker=coord:'+latitude+','+longitude+';title:'+deviceID+'',                                                 
                      });                         
                      
                     }
                    }
                     var re3 = ({
                      title: "君乐定位系统",
                      pic: 'http://liyume.qiniudn.com/head.jpg',
                      url: 'https://standbygps.com',
                      });
 
                    re2.unshift(re3) 
                    return next(null,re2) 
                    })
                    }//end of all group
                    })// end query grouplist 
                                    
                    }  
                    else{
                      next(null, "你未绑定帐号，请先绑定，或者试用产品")
                    }
        })  //mysql
      }
      else if (info.param.eventKey == 'D'){
                     mysql.query('UPDATE User set notes="" WHERE notes = "'+info.uid+'"', function(err, fields) {
                    //console.log(fields)
                    if (fields.changedRows >= 1){
                     return next(null,'已经解除绑定') 
                    }
                    else{
                     return next(null,'你未绑定帐号') 
                    }
        })            
      }
      else if (info.param.eventKey == "sysadmin#demo#demo"){

                      mysql.query('SELECT accountID,deviceID,equipmentType,expirationTime,description,lastBatteryLevel,lastValidLatitude,lastValidLongitude,lastEventTimestamp,lastCellServingInfo,notes FROM Device WHERE accountID = "sysadmin"', function(err,rows,fields) {
                      var re2 = []                   
                      for(var i=0;i<rows.length;i++){
                      var accountID = rows[i].accountID;
                      var deviceID = rows[i].deviceID;
                      var expirationTime = rows[i].expirationTime;
                      var description = rows[i].description;
                      var lastBatteryLevel = rows[i].lastBatteryLevel;
                      var address = rows[i].notes;
                      var longitude =  rows[i].lastValidLongitude;
                      var latitude = rows[i].lastValidLatitude;
                       var timestamp = rows[i].lastEventTimestamp;
                      var fix = rows[i].lastCellServingInfo;
                      if (fix.length > 0){
                        var fix = "基站定位"
                      }
                      else {
                        var fix = "精确定位"
                      }
                     if (rows[i].equipmentType == "vehicle"){
                        re2.push({
                      title: "设备名称:"+description+ '\n' +
                             "设备号："+deviceID+ '\n' +
                             "有效期:"+unixtime2YYMMDD(expirationTime)+ '\n' +   
                             "电量:"+lastBatteryLevel+ '\n' +
                             "定位方式:"+fix+ '\n' +                          
                             "日期:"+unixtime2YYMMDD(timestamp)+ '\n' +
                             "时间:"+unixtime2hhmmss(timestamp)+ '\n' +
                             "设备位置:"+address,
                             
                      pic: 'http://liyume.qiniudn.com/2012617545066475468.jpg',
                      url: 'http://apis.map.qq.com/uri/v1/marker?marker=coord:'+latitude+','+longitude+';title:'+deviceID+'',
                      });

                      }
                      else { 
                      re2.push({
                      title: "设备名称:"+description+ '\n' +
                             "设备号："+deviceID+ '\n' +
                             "有效期:"+unixtime2YYMMDD(expirationTime)+ '\n' +   
                             "电量:"+lastBatteryLevel+ '\n' +
                             "定位方式:"+fix+ '\n' +                          
                             "日期:"+unixtime2YYMMDD(timestamp)+ '\n' +
                             "时间:"+unixtime2hhmmss(timestamp)+ '\n' +
                             "设备位置:"+address,
                      pic: 'http://liyume.qiniudn.com/standbygps.png',
                      url: 'http://apis.map.qq.com/uri/v1/marker?marker=coord:'+latitude+','+longitude+';title:'+deviceID+'',                                                 
                      });                         
                      
                     }
                    }
                     var re3 = ({
                      title: "君乐定位系统",
                      pic: 'http://liyume.qiniudn.com/head.jpg',
                      url: 'https://standbygps.com',
                      });
 
                    re2.unshift(re3) 
                    return next(null,re2)    
                    } 
                    )// end query eventdata mysql
      
         }


  } //end of 
});

webot.set('#+', {
  pattern: /#+/i,
  handler: function(info, next) {
    var string = info.text;
    var split = string.split('#');
    var s0 = split[0];
    var s1 = split[1];
    var s2 = split[2];
    console.log(s0,s1) 
                mysql.query('SELECT accountID,userID,password,notes FROM User WHERE accountID = "'+s0+'" AND userID = "'+s1+'"', function(err, fields) {
                    var res = fields[0];
                    if (fields.length > 0){
                    var acc = res['accountID'];
                    var user = res['userID'];
                    var pass = res['password'];
                    var notes = res['notes']; 
                    }
                    else{
                      next(null, "无效账号或密码")
                    }
                   //console.log(acc,user,pass);

                  if (acc == s0 && user == s1 && pass == s2){
              
                    if (notes.length > 0){
                        next(null, "已被其他用户绑定，请先解除绑定")
                    }
              //绑定更新用户表notes info.uid 写入
                  else { mysql.query('UPDATE User set notes="'+info.uid+'" WHERE  accountID = "'+s0+'" AND userID = "'+s1+'"', function(err, fields) {
                    console.log(fields)
                    if (fields.changedRows == 1){
                    next(null, "绑定成功")
                    }


                    else{
                      next(null, "绑定失败")
                    }
                 })  //mysql
                }
             }
              else{
                      next(null, "无效密码")
                    }
        })  //mysql

    }
})



webot.set(',+', {
  pattern: /,+/i,
  handler: function(info, next) {
    if (info.uid == "oY-xqs0AXNm7bt3aiJ2ovu0ITGi4"){
    var string = info.text;
    var split = string.split(',');
    var s0 = split[0];   //cmd : 1.adduser 2.adddevice 3.pwd
    var s1 = split[1];   //account
    var s2 = split[2];   //user   or deviceID
    var s3 = split[3];   //password or uniqueID
    var s4 = split[4];   //sim Num
    //console.log(s0+s1+s2+s3+s4)
    if(s0.length == 0){
      next(null, "命令无效")
    }
    else if (s1.length == 0){
      next(null, "账号无效")
    }
    else if (s2.length == 0){
      next(null, "用户无效")
    } 
    else if (s3.length == 0){
      next(null, "密码无效")
    } 
    else if(s4.length == 0){
      next(null, "SIM Num 无效")
    } 
    if (s0 == "adduser"){
                mysql.query('SELECT accountID,userID,password,notes FROM User WHERE accountID = "'+s1+'" AND userID = "'+s2+'"', function(err, fields) {
                    var res = fields[0];
                    if (s2 == "admin"){
                      access = 0;
                    }
                    else{
                      access = 3;
                    }
                    var dt = Date.parse(new Date())/1000;
                    if (fields.length > 0){
                       next(null, "用户已存在")
                    }
                    else{
                      var addaccount  = {account设备号： s1, password: s3, timeZone: 'GMT+08:00', isActive: 1,description: s1, creationTime: dt};
                      var query = mysql.query('INSERT INTO Account SET ?', addaccount, function(err, result) {
                          })
                      var adduser  = {account设备号： s1, user设备号： s2, password: s3, contactPhone: s2, timeZone: 'GMT+08:00', maxAccessLevel: access, isActive: 1, creationTime: dt};
                      var query = mysql.query('INSERT INTO User SET ?', adduser, function(err, result) {
                          next(null, "新增用户完成")  
                          })
                    }
                })     
    } //end of add user
    else if (s0 == "adddevice"){
                mysql.query('SELECT accountID,uniqueID FROM Device WHERE accountID = "'+s1+'" AND uniqueID = "'+s3+'"', function(err, fields) {
                    var dt = Date.parse(new Date())/1000;
                    if (fields.length > 0){
                       next(null, "设备ID已存在")
                    }
                    else{
                      var adddevice  = {account设备号： s1, device设备号： s2, unique设备号： s3, simPhoneNumber: s4, isActive: 1, creationTime: dt, description: s2};
                      var query = mysql.query('INSERT INTO Device SET ?', adddevice, function(err, result) {
                          next(null, "新增设备完成")  
                          })
                    }
                })       

    } //end of adddevice
    else if (s0 == "pwd"){
                   sql='UPDATE Account set password="'+s3+'" WHERE accountID="'+s1+'"'
                   mysql.query(sql);
                    sql1='UPDATE User set password="'+s3+'" WHERE accountID="'+s1+'" and userID="'+s2+'"'
                   mysql.query(sql1);
                   next(null, "修改密码完成")  
    } //end of change pwd
    else {
       next(null, "指令无效")
    }
}
    } //end handle next

})
// set standby
//
webot.set('@+', {
  pattern: /@+/i,      
  handler: function(info, next) {
    var string = info.text;
    var split = string.split('@');
    var s0 = split[0];
    var s1 = split[1];
    var s2 = split[2];
                   mysql.query('SELECT accountID,userID,notes FROM User WHERE notes = "'+info.uid+'"', function(err, fields) {
                    var res = fields[0];
                    if (fields.length > 0){
                    var acc = res['accountID'];
                    var user = res['userID'];
                    mysql.query('SELECT accountID,userID,groupID FROM GroupList WHERE accountID = "'+acc+'" AND userID = "'+user+'"', function(err, fields) {
                    var res = fields[0];
                    if (fields.length > 0){
                    var acc = res['accountID'];
                    var grouplist = res['groupID']; 
                    mysql.query('SELECT accountID,deviceID FROM DeviceList WHERE accountID = "'+acc+'" AND groupID = "'+grouplist+'" AND deviceID = "'+s0+'" ', function(err,rows,fields) {                  
                    var dt = Date.parse(new Date())/1000; 
                    if (s1 == "报警"){
                       cmd = "*HQ,000,S41,130305,6,4,2,2,2,2,2,2,2#"
                       sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,4,2,2,2,2,2,2,2#', creationTime: dt};
                       res_string = "报警指令已提交,定位器下次签到时，会每小时回传一次GPS位置，追踪完毕后，请及时提交撤防指令，否则电池很快耗尽"
                       }
                    else if ( s1 == "撤防"){
                        cmd = "*HQ,000,S41,130305,6,4,1,1,1,1,1,1,1#"
                        sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,4,1,1,1,1,1,1,1#', creationTime: dt};
                        res_string =  "撤防指令已提交，定位器每天签到服务器4次"
                       }
                    else if ( s1 == "睡眠"){
                       cmd = "HQ,000,S41,130305,6,1,1,1,1,1,1,1,1,0400040004000400040004000400#"
                       sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,1,1,1,1,1,1,1,1,0400040004000400040004000400#', creationTime: dt};
                       res_string = "睡眠指令已提交,定位器每天中午12点签到服务器一次，建议提交撤防指令，让定位器每天签到服务器4次"
                       }
                     else if (s1 == "紧急"){
                          cmd = "*HQ,000,S41,130305,6,7#"
                          sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,7#', creationTime: dt};
                          res_string = "紧急指令已提交,定位器下次签到时，会马上启动GPS定位！追踪完毕后，请及时提交撤防指令，否则电池会在1天内耗尽"                          
                       }
                     else if (s1 == "改名"){
                          sql_string  = {description: s2};
                          res_string = "更改设备名称完成"                          
                       }; 
                       var res = rows[0];
                       if(rows.length > 0){
                       mysql.query('SELECT sendState FROM PendingCommands WHERE accountID ="'+acc+'" AND deviceID = "'+s0+'" ORDER BY creationTime DESC LIMIT 1', function(err, fields) {
                        var res = fields[0];
                        console.log(res)
                       if (fields.length > 0){
                        var state = res['sendState'];
                        if (state == 0){
                           mysql.query('UPDATE PendingCommands SET ? WHERE accountID ="'+acc+'" AND deviceID = "'+s0+'" AND sendState = "0"',{commandArgs: cmd});    
                           next(null, res_string)                     
                          }
                        else {
                         var query = mysql.query('INSERT INTO PendingCommands SET ?', sql_string, function(err, result) {
                         next(null, res_string)    
                        })                            
                          } 

                            }// have cmd have sendstate
                         else {
                         var query = mysql.query('INSERT INTO PendingCommands SET ?', sql_string, function(err, result) {
                         next(null, res_string)    
                        })                            
                          }                        

                       }
                      )
                        if(s1== "改名"){
                          mysql.query('UPDATE Device SET ? WHERE accountID ="'+acc+'" AND deviceID = "'+s0+'"',sql_string);    
                           next(null, res_string)     
                        }
                       }
                    else{
                      next(null, "无权限或设备名称错误")
                     } 
                     })
                     } //end grouplist vaild
                    // grouplist invaild start query all device
                    else {
                   mysql.query('SELECT accountID,userID,notes FROM User WHERE notes = "'+info.uid+'"', function(err, fields) {
                    var res = fields[0];
                    if (fields.length > 0){
                    var acc = res['accountID'];
                    mysql.query('SELECT accountID,deviceID FROM Device WHERE accountID ="'+acc+'" AND deviceID = "'+s0+'"', function(err,rows,fields) {
                    var dt = Date.parse(new Date())/1000; 
                    if (s1 == "报警"){
                       cmd = "*HQ,000,S41,130305,6,4,2,2,2,2,2,2,2#"
                       sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,4,2,2,2,2,2,2,2#', creationTime: dt};
                       res_string = "报警指令已提交,定位器下次签到时，会每小时回传一次GPS位置，追踪完毕后，请及时提交撤防指令，否则电池很快耗尽"
                       }
                    else if ( s1 == "撤防"){
                        cmd = "*HQ,000,S41,130305,6,4,1,1,1,1,1,1,1#"
                        sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,4,1,1,1,1,1,1,1#', creationTime: dt};
                        res_string =  "撤防指令已提交，定位器每天签到服务器4次"
                       }
                    else if ( s1 == "睡眠"){
                       cmd = "HQ,000,S41,130305,6,1,1,1,1,1,1,1,1,0400040004000400040004000400#"
                       sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,1,1,1,1,1,1,1,1,0400040004000400040004000400#', creationTime: dt};
                       res_string = "睡眠指令已提交,定位器每天中午12点签到服务器一次，建议提交撤防指令，让定位器每天签到服务器4次"
                       }
                     else if (s1 == "紧急"){
                          cmd = "*HQ,000,S41,130305,6,7#"
                          sql_string  = {account设备号： acc, device设备号： s0,queueTime: dt, sendState: 0, commandArgs: '*HQ,000,S41,130305,6,7#', creationTime: dt};
                          res_string = "紧急指令已提交,定位器下次签到时，会马上启动GPS定位！追踪完毕后，请及时提交撤防指令，否则电池会在1天内耗尽"                          
                       }
                     else if (s1 == "改名"){
                          sql_string  = {description: s2};
                          res_string = "更改设备名称完成"                          
                       }; 
                    var res = rows[0];
                    // have account,start query cmd
                    if(rows.length > 0){
                    mysql.query('SELECT sendState FROM PendingCommands WHERE accountID ="'+acc+'" AND deviceID = "'+s0+'" ORDER BY creationTime DESC LIMIT 1', function(err, fields) {
                        var res = fields[0];
                        console.log(res)
                      if (fields.length > 0){
                        var state = res['sendState'];
                        if (state == 0){
                           mysql.query('UPDATE PendingCommands SET ? WHERE accountID ="'+acc+'" AND deviceID = "'+s0+'" AND sendState = "0"',{commandArgs: cmd});    
                           next(null, res_string)                     
                          }
                        else {
                         var query = mysql.query('INSERT INTO PendingCommands SET ?', sql_string, function(err, result) {
                         next(null, res_string)    
                        })                            
                          } 

                            }// have cmd have sendstate
                         else {
                         var query = mysql.query('INSERT INTO PendingCommands SET ?', sql_string, function(err, result) {
                         next(null, res_string)    
                        })                            
                          }                        

                       }
                      )
                        if(s1== "改名"){
                          mysql.query('UPDATE Device SET ? WHERE accountID ="'+acc+'" AND deviceID = "'+s0+'"',sql_string);    
                           next(null, res_string)     
                        }
                          }// all group account vaild
                        else {
                           next(null, "无权限或设备名称错误:404")
                           console.log("update cmd to db")  
                        }  
                     }                      
                    )//sql query all account
                    }
                    else{
                      next(null, "无权限或设备名称错误")
                     } 
                     })

                    }                      
                    }) // end query grouplist
                    }//end account vaild
                    else{
                      next(null, "无权限或设备名称错误")
                     } 

                      })//end query account
}
})
  webot.set(/.*/, function(info){
    info.flag = true;
    return '你发送了「' + info.text + '」,系统无法识别.';
  });
// 接管消息请求
webot.watch(app, { token: 'junlegps', path: '/wechat' });
app.listen(3000);
app.listen(process.env.PORT);
app.enable('trust proxy');









