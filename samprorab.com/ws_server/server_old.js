var WebSocket = require('ws').Server;

var https = require('https');
var http = require('http');
var fs = require('fs');

var bodyparser = require('body-parser');


//my libs and settings
var app = require('./app/config');
var api = require('./app/api');


var server = https.Server({
    cert: fs.readFileSync(app.config.cert_path),
    key: fs.readFileSync(app.config.key_path),
    port: 8080,
});

var webSocketServer = new WebSocket({
    server: server
    //host: app.config.ws_server,
    //port: app.config.ws_port,
    //origin: 'https://samprorab.com',
});




///////////////////////////////////////////////


//все авторизованные пользователи разделенные на 2 группы
// users[user_type][php_session_id] = ws
var users = {};
users.customer = {};
users.master = {};

///////////////////////////////////////////////

webSocketServer.on('connection', function(ws) {

    AUTH(ws);
    
    ws.on('message', function incoming(raw_message) {

        var message = JSON.parse(raw_message);

        //console.log(message);
        //console.log(ws['id']);

        if(ws['id'] !== undefined){ //Если пользователь авторизован (имеет ID)
            //var  message = raw_message.split('~'); // Разбиваем присланное сообщение


            /*
            //сделать ставку
            if(message[0] === 'create_bet'){

                var data = {
                    'module': 'model_finance',
                    'action': 'api_create_bet',
                    'id': message[1],
                    'bet': message[2],
                };
                console.log(data);
                api.php_post(ws, data,  function (data) {

                    if(data !== 'error'){

                        ws.send(JSON.stringify(data));

                        if(data.status === 'success'){
                            //data.data.min_bet

                            Object.keys(users['actual_lot'][ws['actual_lot']]).forEach(function (item) {

                                if(item !== ws['id']){
                                    users['actual_lot'][ws['actual_lot']][item].send(JSON.stringify(data.data));
                                }

                            });

                        }

                        if(data.status === 'error'){

                        }

                    }






                });

            }

            //отслеживать лот или перестать его отслеживать
            if(message[0] === 'subscribe_toggle'){

                var data = {
                    'module': 'model_auction',
                    'action': 'api_subscribe_toggle',
                    'lot_id': message[1],
                };

                api.php_post(ws, data,  function (data) {

                    if(data !== 'error'){

                        ws.send(JSON.stringify(data));

                        if(data.status === 'success'){

                            console.log(data.data);

                            //ws.send(JSON.stringify(data.data));
                            //todo сделать доставку подписчикам.

                            //if(users['subs'][message[1]] !== undefined){
                            //    console.log('oook');
                            //    Object.keys(users['subs'][message[1]]).forEach(function (item) {
                            //        if(item !== ws['id']){
                            //            users['actual_lot'][ws['actual_lot']][item].send(JSON.stringify(data.data));
                            //        }
                            //    });
                            //}





                        }

                        if(data.status === 'error'){

                            ws.send(JSON.stringify(data));

                        }

                    }






                });

            }

            //пользователь сообщает серверу, что данный лот, он прямо сейчас просматривает
            //данный модуль необходим для реактивности изменений лота
            if(message[0] === 'actual_lot'){

                ws['actual_lot'] = message[1];

                if(!users['actual_lot'][message[1]]){
                    users['actual_lot'][message[1]] = {};
                    users['actual_lot'][message[1]][ws['id']] = ws;
                }else{
                    users['actual_lot'][message[1]][ws['id']] = ws;
                }

            }

            // TEST тестовый запрос от пользователя
            //test~getinphp~1117
            if(message[0] === 'test') {
                if(message[1] === 'getinphp'){
                    var test = {
                        'module': 'model_auction',
                        'action': 'api_get_lot',
                        'id': message[2], //1117
                    };
                    api.php_post(ws, test, function (data) {

                        if(data !== 'error'){

                        }

                    });
                }
            }

             */

        }
    });

    //Выход из чата
    ws.on('close', function() {

        //удаляем пользователя если создан
        if(ws['id']){
            console.log('User id: '+ws['id']+' left server');

            //remove user
            if(users[ws['user_type']][ws['id']] !== undefined){

                var disconnect_tab = {};
                disconnect_tab.type = 'disconnect_tab';
                disconnect_tab.title = "Соединение сброшено";
                disconnect_tab.message = "<p>У Вас уже имеется активное соединение!</p>" +
                    "<p>Для востановления соединения в этой вкладке, перезагрузите страницу.</p>";
                disconnect_tab.status = 'error';

                users[ws['user_type']][ws['id']].send(JSON.stringify(disconnect_tab));

                delete users[ws['user_type']][ws['id']];
            }



        }



    });


});





//AUTH PHP
//Авторизация на сессиях PHP
//Использовать только с HTTPS (Прямая передача кукисов)
function AUTH(ws){

    jwt_data = ws._protocol.split('~');


    if(jwt_data.length !== 3){
        return;
    }

    ws.jwt = jwt_data[0];
    ws.temp_id = jwt_data[1];
    ws.salt = jwt_data[2];

    console.log('USER GET AUTH: '+ws.jwt);
    if(typeof ws.jwt === 'undefined'){
        return;
    }

    var auth = {
        path: 'auth/getid',
        message: '12',
    };

    api.php_post(ws, auth, function (data) {

        //console.log(users);

        console.log('User id: '+data.id+' join to server');
        ws['id'] = data.id;
        ws['user_type'] = data.type;

        users[data.type][data.id] = ws;
        ws.send(JSON.stringify('User id: '+data.id+' join to WS server'));
        ws.send(JSON.stringify({status: 'inited'}));

    });
}
//END AUTH PHP



///////////////////////////////////////////////
console.log(' ');
console.log("\x1b[42m", "\x1b[30m", "         ╔═══════════════════════════╗          ", "\x1b[0m", "\x1b[37m");
console.log("\x1b[42m", "\x1b[30m", "         ║  WEBSOCKET SERVER ONLINE  ║          ", "\x1b[0m", "\x1b[37m");
console.log("\x1b[42m", "\x1b[30m", "         ╚═══════════════════════════╝          ", "\x1b[0m", "\x1b[37m");
///////////////////////////////////////////////

const api_server = https.createServer({
    cert: fs.readFileSync(app.config.cert_path),
    key: fs.readFileSync(app.config.key_path)
},function(request, response) {

    if (request.method === 'POST') {

        var body = '';

        request.on('data', function(request_data) {

            var data = JSON.parse(request_data.toString('utf8'));

            console.log(data);

            if(data.action !== undefined){

                if(data.action === 'debug'){
                    console.log(data);
                }

            }

            //response.write();

            //response.write(data);
        });
        request.on('end', function() {
            response.writeHead(200, {'Content-Type': 'application/json'});
            response.end(body);
        })
    } else {
        //На все get запросы отвечаем 404
        response.writeHead(404, {'Content-Type': 'application/json'});
        response.end();
    }
});

api_server.listen(app.config.post_port, app.config.post_ip);
///////////////////////////////////////////////
console.log(' ');
console.log("\x1b[42m", "\x1b[30m", "         ╔═══════════════════════════╗          ", "\x1b[0m", "\x1b[37m");
console.log("\x1b[42m", "\x1b[30m", "         ║   API POST SERVER ONLINE  ║          ", "\x1b[0m", "\x1b[37m");
console.log("\x1b[42m", "\x1b[30m", "         ╚═══════════════════════════╝          ", "\x1b[0m", "\x1b[37m");
console.log(' ');
///////////////////////////////////////////////










