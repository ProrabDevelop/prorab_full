var timeInMs = Date.now();

console.log(timeInMs);


function array_diff (array) {	// Computes the difference of arrays
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

    var arr_dif = [], i = 1, argc = arguments.length, argv = arguments, key, key_c, found=false;

    // loop through 1st array
    for ( key in array ){
        // loop over other arrays
        for (i = 1; i< argc; i++){
            // find in the compare array
            found = false;
            for (key_c in argv[i]) {
                if (argv[i][key_c] == array[key]) {
                    found = true;
                    break;
                }
            }

            if(!found){
                arr_dif[key] = array[key];
            }
        }
    }

    return arr_dif;
}




var users_all = [];

users_all['1'] = {'name' : 'max'};
users_all['2'] = {'name' : 'fil'};
users_all['3'] = {'name' : 'bob'};
users_all['4'] = {'name' : 'mary'};


var users_online = [];

users_online['1'] = {'name' : 'max'};
users_online['4'] = {'name' : 'mary'};
//console.log(arr_diff(users_all, users_online));


var test = array_diff(users_all, users_online);


//var test2 = JSON.parse(test);


console.log(test);
//console.log(test2);