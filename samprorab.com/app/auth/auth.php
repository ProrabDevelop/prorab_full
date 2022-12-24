<?
namespace app\auth;

use app\std_models\user;
use core\engine\API;
use core\engine\sms;
use core\engine\std_module;
use http\Cookie;

class auth extends std_module {

    public $active = true;
    public $forauth = false;

    protected $routes = [
        '/auth' => [
            '/' => 'main',
            '/login/' => 'login',
            '/registration/' => 'registration',
            '/regsms/' => 'regsms',
            '/lostpass/' => 'lostpass',
            '/lostsms/' => 'lostsms',
            '/getid/' => 'getid',

            '/switch/' => 'switch_user_type',
            '/logoutall/' => 'logoutall',
            '/logout/' => 'logout',
        ],
    ];

    public function main(){
        echo 'this is main auth system api';
    }

    public function getid(){

        $user = new user();

        $res = [
            'id' => $this->auth->user->id,
            'type' => $this->auth->user->role,
        ];

        API::response($res);

    }

    public function login(){

        $fields = \validator::ALL_POST([
            'phone' => ['req', 'phone'],
            'pass' => ['req', 'str'],
            'remember_me' => ['bool'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $user_model = new user();
            $user = $user_model->auth($fields);

            if($user){

                $jwt_data = $this->auth->create_jwt($user->id);

                API::response(['jwt_data' => $jwt_data]);

            }

            API::error(170, 'Телефон или пароль не верны');

        });

    }

    public function registration(){

        $fields = \validator::ALL_POST([
            'name' => ['req', 'str'],
            'surname' => ['req', 'str'],
            'mail' => ['req', 'email'],
            'phone' => ['req', 'phone'],
            'pass' => ['req', 'str', ['min', 8], ['max', 20]],
            'role' => ['req', ['enum', ['customer','master']]],
            'remember_me' => ['bool'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $user_model = new user();

            $response = $user_model->create($fields->get_fields());

            if($response){

                //$jwt_data = $this->auth->create_jwt($response['id']);

                $res = [];
                $res['sms_hash'] = $response['sms_hash'];
                if(sms_debug){
                    $res['sms_debug'] = $response['sms_debug'];
                }

                API::response($res);
            }

            API::error(171, 'Пользователь с таким номером уже зарегистрирован');

        });

    }

    public function regsms(){

        $fields = \validator::ALL_POST([
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
            'code' => ['req', 'str', ['min', 6], ['max', 6]],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $confirm_data = sms::confirm($fields->sms_hash, $fields->code);

            if(is_array($confirm_data) && $confirm_data['action'] == 'reg'){
                $user = new user($confirm_data['user_id']);
                $user->confirm_reg();

                $jwt_data = $this->auth->create_jwt($user->id);

                API::response(['jwt_data' => $jwt_data]);

            }

            API::error(170, 'Регистрация не удалась');

        });

    }

    public function lostpass(){

        $fields = \validator::ALL_POST([
            'phone' => ['req', 'phone'],
        ]);

        if($fields->POST()){

            if($fields->errors){
                API::error_validator($fields);
            }else{

                $user = new user();

                $response = $user->lostpass($fields->phone);

                if($response){

                    $res = [];
                    $res['sms_hash'] = $response['sms_hash'];
                    if(sms_debug){
                        $res['sms_debug'] = $response['sms_debug'];
                    }

                    API::response($res);
                }

                API::error(171, 'Пользователь с таким номером не найден');

            }


        }else{
            API::no_post_data();
        }

    }

    public function lostsms(){

        $fields = \validator::ALL_POST([
            'sms_hash' => ['req', 'str', ['min', 13], ['max', 13]],
            'code' => ['req', 'str', ['min', 6], ['max', 6]],
            'pass' => ['req', 'str', ['min', 8]],
            'pass_confirm' => ['req', 'str', ['min', 8]],
        ]);


        API::auto_validate($fields, function (\validator $fields){

            $confirm_data = sms::confirm($fields->sms_hash, $fields->code);

            if(is_array($confirm_data) && $confirm_data['action'] == 'lostpass'){
                $user = new user($confirm_data['user_id']);

                if($fields->pass === $fields->pass_confirm){
                    $user->change_passwors($fields->pass);


                    $jwt_data = $this->auth->create_jwt($user->id);
                    API::response(['jwt_data' => $jwt_data]);


                }else{
                    API::error(170, 'Пароли не совпадают');
                }

            }

            API::error(195, 'Не верный SMS код');

        });

    }


    public function switch_user_type(){

        if($this->auth->is_auth()){
            $this->auth->user->switch_role();
            $this->redirect('dashboard');
        }
        $this->redirect();


    }

    public function logoutall(){
        $this->auth->destroy_all_user_session($this->auth->user->id);
        $this->redirect();
    }

    public function logout(){
        $this->auth->destroy_session();
        $this->redirect();
    }

}