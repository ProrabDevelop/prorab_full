<?
namespace api\cityes;

use app\std_models\user;
use core\engine\API;
use core\engine\sms;
use core\engine\std_module;

class cityes extends std_module {

    public $active = true;
    public $forauth = false;

    protected $routes = [
        '/cityes' => [
            '/' => 'main',
            '/list/' => 'citys_list',
        ],
    ];

    public function main(){
        echo 'this is main cityes api';
    }

    public function citys_list(){

        $fields = \validator::ALL_POST(['chars' => ['req','str']]);


        API::auto_validate($fields, function ($fields){

            $cityes = \ORM::for_table('cityes_for_search')->where_like('name', '%'.$fields->chars.'%')->order_by_desc('id')->limit(10)->find_many();

            $response_data = [];
            $results = [];

            if($cityes){

                foreach ($cityes as $city){
                    $results[] = [
                        'id' => $city->id,
                        'text' => $city->name,
                    ];
                }
            }

            $response_data['results'] = $results;

            echo json_encode($response_data, JSON_UNESCAPED_UNICODE);
            exit();

        });



    }

}










