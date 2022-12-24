<?

namespace app\users;

use app\std_models\slim_user;
use core\engine\API;
use core\engine\APP;
use core\engine\DATA;
use core\engine\std_module;

class user extends std_module{

    public $active = true;
    public $forauth = true;
    public $is_api = true;

    protected $routes = [
        '/users' => [
            '/' => 'main',
            '/favorite/' => 'favorite',
            '/workwith/' => 'work_with',
            '/getinfo/' => 'main',
            '/getinfo/{id:\d+}/' => 'getinfo',

            '/add_spec/' => 'add_spec',
            '/last_visit/' => 'main',
            '/last_visit/{id:\d+}/' => 'last_visit',

        ],
    ];

    public function main(){
        API::response(['message' => 'users main api']);
    }


    public function last_visit($data){
		
        $time = (new slim_user($data['id']))->last_visit;

        if($time){
            $last_visit_text = 'онлайн '.human_date($time, 600);
        }else{
            $last_visit_text = 'Давно не был в сети';
        }



        API::response([
            'last_visit' => $last_visit_text,
        ]);

    }

    public function favorite(){
        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            /* @var $user \app\std_models\user*/
            $user = DATA::get('USER');

            $favorites = $user->customer_data->get('favorites');

            if(empty($favorites)){
                $favorites = [];
            }

            $subscribe = false;
            if(false !== $key = array_search($fields->id, $favorites)){
                unset($favorites[$key]);
            }else{
                $subscribe = true;
                $favorites[] = $fields->id;
            }

            $user->customer_data->set('favorites', $favorites);
            $user->customer_data->save_data();

            API::response(['id' => $fields->id, 'subscribe' => $subscribe]);

        });

    }

    public function work_with(){
        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
        ]);



        API::auto_validate($fields, function (\validator $fields){

            /* @var $user \app\std_models\user*/
            $user = DATA::get('USER');



            $orders = $user->customer_data->get('orders');
            if(empty($orders)){
                $orders = [];
            }


            $ids = [];
            foreach ($orders as $key => $order){
                if(isset($order['id'])){
                    $ids[$key] = $order['id'];
                }
            }

            $worked = false;
            if(false !== $key = array_search($fields->id, $ids)){
                unset($orders[$key]);
            }else{
                $worked = true;
                $orders[] = [
                    'id' => $fields->id,
                    'date' => (new \DateTime())->format('d.m.Y'),
                ];
            }

            $user->customer_data->set('orders', $orders);
            $user->customer_data->save_data();

            API::response(['id' => $fields->id, 'worked' => $worked]);

        });

    }

    public function getinfo($data){

        $id = $data['id'];

        $user = new slim_user($id);
        if($user){

            $res = [
                'id' => $user->id,
                'name' => $user->name.' '.$user->surname,
                'avatar' => get_avatar($user->id)
            ];

            API::response($res);
        }

        API::response();


    }

    public function add_spec(){

        $fields = \validator::ALL_POST([
            'user_id' => ['req', 'int'],
            'spec_name' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $new_spec = \ORM::for_table('new_specs')->create();
            $new_spec->user_id = $fields->user_id;
            $new_spec->spec_name = $fields->spec_name;
            $new_spec->save();

            API::response();

        });






    }

}