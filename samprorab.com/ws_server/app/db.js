var mysql = require('mysql');

var connection = mysql.createConnection({
    host     : '127.0.0.1',
    port     : 3306,
    database : 'prorab',
    user     : 'prorab',
    password : 'qjBGElEI3nSgrMbf'//eR1jS7uJ
});


async function get_query(query) {
    return new Promise((resolve, reject) => {
        connection.query(query,
            (err, res) => {
                if (err) reject(err);
                resolve(res);
            }
        );
    })
}

async function get_all_dialogs(){

    //connection.connect();
    var ret = {};
    var query = await mysql.format('SELECT * FROM dialogs');
    var rows = await get_query(query);

    for (var item of rows){
        ret[item.id] = {};
        ret[item.id]['user_1'] = item.user_id_1;
        ret[item.id]['user_2'] = item.user_id_2;
    }

    //connection.end();

    return ret;
};

var save_message = function(login, pass, callback){

    //query = mysql.format('SELECT * FROM users WHERE login=? AND pass=?', [login, pass]);

    //query = mysql.format('INSERT INTO messages (from, from_type, to, time, readed, type, body)', [login, pass]);


    //INSERT INTO `table_name`(column_1,column_2,...) VALUES (value_1,value_2,...);

    connection.query(query, function(err, results, fields) {
        if (err) throw err;

        if(results[0] !== undefined){
            callback(JSON.parse(JSON.stringify(results[0])));
        }else{
            callback('nodata');
        }


    });
    //connection.end();
};






module.exports.save_message = save_message;
module.exports.get_all_dialogs = get_all_dialogs;


