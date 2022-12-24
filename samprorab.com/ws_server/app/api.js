var request = require('request');
var FormData = require('form-data');
var fs = require('fs');
var app = require('./config');



function IsJsonString(str) {
    try {
        return JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


function getFormData(object) {
    const formData = new FormData();
    Object.keys(object).forEach(key => formData.append(key, object[key]));
    return formData;
}

function php_post(ws, message, callback){

    var udata = {};
    udata.id = ws.temp_id;
    udata.salt = ws.salt;

    request.post({
            headers: {'content-type' : 'application/json', 'Cookie': 'jwt='+ws.jwt+';udata='+JSON.stringify(udata)},
            url: app.config.api_url+message.path+'/',
            form: message,
        },
        function (error, response, body) {

            console.log(response.statusCode);
            console.log(app.config.api_url+message.path);
            console.log(body);
            if(response.statusCode === 302){
                console.log("\x1b[41m", "\x1b[37m", "RESPONSE 302 REDIRECT", "\x1b[0m");
            }

            if (!error && response.statusCode === 200) {

                if(app.config.debug === true){
                    console.log("\x1b[44m", '=== PHP RESPONSE: ',body,"\x1b[0m");
                }

                if(body){
                    var data = IsJsonString(body);

                    if(data === false){

                        console.log("\x1b[41m", "\x1b[37m", 'PHP ВЕРНУЛ ОШИБКУ', "\x1b[0m");
                        console.log("\x1b[41m", "\x1b[37m", body, "\x1b[0m");

                        callback('false');

                    }else{

                        if(data.status === 'ok'){
                            callback(data.data);
                        }else{
                            console.log("\x1b[41m", "\x1b[37m", data.message, "\x1b[0m");
                        }

                    }



                }


                //return body;

            }
        }
    );


}



function php_post_server(message, callback){
    request.post({
            headers: {'content-type' : 'application/x-www-form-urlencoded', 'Cookie': 'SERVER=server'},
            url: app.config.api_url,
            body:    "api_message="+JSON.stringify(message)
        },
        function (error, response, body) {

            if (!error && response.statusCode === 200) {

                if(app.config.debug === true){
                    console.log("\x1b[44m", '=== PHP RESPONSE: ',body,"\x1b[0m");
                }

                if(body){

                    var data = IsJsonString(body);

                    if(data === false){

                        console.log("\x1b[41m", "\x1b[37m", 'PHP ВЕРНУЛ ОШИБКУ', "\x1b[0m");
                        console.log("\x1b[44m", '=== PHP RESPONSE: ',body,"\x1b[0m");

                        callback('false');

                    }else{

                        if(!data.error){
                            callback(data);
                        }else{
                            console.log("\x1b[41m", "\x1b[37m", data.message, "\x1b[0m");
                        }

                    }

                }


                //return body;

            }
        }
    );
}



module.exports.php_post = php_post;
module.exports.php_post_server = php_post_server;
