<?
namespace admin\dashboard;

use app\std_models\user;
use app\std_models\users_list;
use core\engine\AUTH;
use core\engine\DATA;
use Core\Engine\pagination;
use core\engine\rebuild_rating;
use core\engine\std_module;
use core\engine\std_module_admin;
use core\engine\view;

class users extends std_module_admin {

    public $forauth = true;

    protected $routes = [
        '/users' => [
            '/' => 'all_users',
        ],
        '/user' => [
            '/{id:\d+}/' => 'auth_with_user',
        ],
    ];

    public function all_users(){


        $fields = \validator::ALL_POST([
            'user_id' => ['req', 'int'],
            'type_ip' => ['checked'],
            'type_ooo' => ['checked'],
        ]);

        if($fields->POST()){

            if(!$fields->errors){

                $user = new user($fields->user_id);

                if($fields->type_ip){
                    $fields->type_ip = 'true';
                }else{
                    $fields->type_ip = 'false';
                }
                if($fields->type_ooo){
                    $fields->type_ooo = 'true';
                }else{
                    $fields->type_ooo = 'false';
                }
                $user->master_data->update($fields);

                (new rebuild_rating())->rebuild_user($user->id);

                view::set_notification('success', [
                    'title' => 'Сохранения',
                    'content' => 'Профиль пользователя обновлен и перепросчитан'
                ]);

            }

        }




                //$user->master_data->get('type_ooo')
                //$user->master_data->get('type_ip')






        $this->get_users();
    }


    public function auth_with_user($data){

        $user_id = $data['id'];
        AUTH::init();
        $ses_id = AUTH::admin_with_user(intval($user_id));

        header('Location: '.BASE_URL.'login_with_user/'.$ses_id.'/');

    }


    protected function get_users(){
        $count = \ORM::for_table('users')->count();

        pagination::set_limit(50);
        pagination::set_total($count);
        $users_raw = \ORM::for_table('users')->limit(pagination::get_limit())->offset(pagination::get_offset())->find_many();

        $users = [];
        $users_ids = [];
        foreach ($users_raw as $user){
            $users_ids[] = $user->id;
        }
        $users = (new users_list())->get($users_ids);

        DATA::set('users', $users);
    }


}