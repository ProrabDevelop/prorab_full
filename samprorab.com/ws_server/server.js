// process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

var WebSocket = require('ws').Server;

var https = require('https');
var fs = require('fs');

var bodyparser = require('body-parser');


//my libs and settings
var app = require('./app/config');
var api = require('./app/api');
var db = require('./app/db');
const {php_post} = require("./app/api");

var server = https.createServer({
    cert: fs.readFileSync(app.config.cert_path),
    key: fs.readFileSync(app.config.key_path),
    ca: [fs.readFileSync(app.config.ca_path)]
});


var dialogs_init = (async function (){
    var dialogs = await db.get_all_dialogs();
    init_wss(dialogs);
});

function init_wss(dialogs){

    var wss = new WebSocket({
        server: server
    });

    var users = {};

    wss.on('connection', function connection(ws) {
        AUTH(ws);
        ws.on('message', async function incoming(raw_message) {

            var message_data = JSON.parse(raw_message);

            //console.log(message);
            //console.log(ws['id']);

            if(ws['id'] !== undefined){ //Если пользователь авторизован (имеет ID)
                //var  message = raw_message.split('~'); // Разбиваем присланное сообщение

                if(message_data.action === 'update_readed') {

                    console.log(message_data);

                    var dialog_id = message_data.dialog_id;
                    var reader = message_data.reader;

                    if(dialogs[dialog_id] === undefined){
                        dialogs = await db.get_all_dialogs()
                    }

                    var mes_to;
                    if(parseInt(dialogs[dialog_id]['user_1']) === parseInt(reader)){
                        mes_to = dialogs[dialog_id]['user_2'];
                    }else{
                        mes_to = dialogs[dialog_id]['user_1'];
                    }

                    var data = {
                        action: 'messages_readed',
                        dialog_id: dialog_id,
                        reader: reader
                    };

                    data.path = 'messenger/read';

                    php_post(ws, data, function (data){

                        users[reader].send(JSON.stringify({
                            action: 'read_updated'
                        }));

                    });

                    if(users[mes_to] !== undefined){
                        users[mes_to].send(JSON.stringify(data));
                    }

                }

                if(message_data.action === 'send'){

                    var message = {
                        action: 'message',
                        sender: ws.id,
                        dialog_id: message_data.dialog_id,
                        type: message_data.type,
                        body: message_data.body,
                        time: new Date()
                    };

                    console.log('dialog: '+message.dialog_id, 'sender: '+message.sender, 'message: '+message.body);


                    if(dialogs[message.dialog_id] === undefined){
                        dialogs = await db.get_all_dialogs()
                    }

                    message.path = 'messenger/send';
                    php_post(ws, message, function (data){});

                    var mes_to;
                    if(parseInt(dialogs[message.dialog_id]['user_1']) === parseInt(message.sender)){
                        mes_to = dialogs[message.dialog_id]['user_2'];
                    }else{
                        mes_to = dialogs[message.dialog_id]['user_1'];
                    }

                    if(users[mes_to] !== undefined){
                        if(mes_to !== parseInt(message.sender)){
                            users[mes_to].send(JSON.stringify(message));
                        }
                    }

                }


            }
        });
        //Выход из чата
        ws.on('close', function() {

            //удаляем пользователя если создан
            if(ws['id']){
                console.log('User id: '+ws['id']+' left server');

                //remove user
                if(users[ws['id']] !== undefined){

                    var disconnect_tab = {};
                    disconnect_tab.type = 'disconnect_tab';
                    disconnect_tab.title = "Соединение сброшено";
                    disconnect_tab.message = "<p>У Вас уже имеется активное соединение!</p>" +
                        "<p>Для востановления соединения в этой вкладке, перезагрузите страницу.</p>";
                    disconnect_tab.status = 'error';

                    users[ws['id']].send(JSON.stringify(disconnect_tab));

                    delete users[ws['id']];
                }



            }

        });
    });

    server.listen(app.config.post_port, app.config.post_ip);

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
            path: 'auth/getid'
        };

        api.php_post(ws, auth, function (data) {

            //console.log(users);

            console.log('User id: '+data.id+' join to server');
            ws['id'] = data.id;

            users[data.id] = ws;

            var data = {
                action: 'auth',
                status: 'inited',
            };

            ws.send(JSON.stringify(data));

        });
    }
//END AUTH PHP

///////////////////////////////////////////////

}

dialogs_init();
















