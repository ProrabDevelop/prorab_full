<?
namespace app\dashboard;

use app\std_models\catalog_model;
use app\std_models\dialog;
use app\std_models\dialogs_list;
use app\std_models\message;
use app\std_models\review;
use app\std_models\sort_rating_prices;
use app\std_models\user;
use app\std_models\work;
use core\engine\API;
use core\engine\AUTH;
use core\engine\city;
use core\engine\DATA;
use core\engine\rebuild_rating;
use core\engine\std_module;
use core\engine\view;

class dashboard extends std_module {

    public $layout = 'dashboard';

    protected user $user;
    public $forauth = true;

    protected $routes = [
        '/dashboard' => [
            '/' => 'main',
        ],
        '/messages' => [
            '/' => 'messages',
            '/{id:\d+}/' => 'messages',
        ],
        '/reviews' => [
            '/' => 'reviews',
            '/{id:\d+}/' => 'api_get_single_review',
            '/add_answer/' => 'review_add_answer',
            '/add_complaint/' => 'review_add_complaint',
            '/add_review/' => 'add_review',
        ],
        '/favorites' => [
            '/' => 'favorites',
        ],
        '/history' => [
            '/' => 'history',
        ],
        '/settings' => [
            '/' => 'settings',
        ],
        '/contacts' => [
            '/' => 'contacts',
        ],
        '/prices' => [
            '/' => 'prices',
        ],
        '/finished' => [
            '/' => 'finished',
        ],

    ];

    protected function before_init(){

        /* @var user $this->user */
        $this->user = DATA::get('USER');
    }

    //Основной лк
    public function main(){

        if(is_ajax()){

            $rules = [
                'name' => ['req', 'str'],
                'surname' => ['req', 'str'],
                'mail' => ['req', 'email'],

                'city' => ['int'],

                'pass' => ['str'],
                'pass_confirm' => ['str'],

                'email_subscribe' => ['checked'],

            ];

            if($this->user->is_master()){

                $rules['birthday'] = [['date','Y-m-d']];
                $rules['experience'] = ['int'];
                $rules['spec'] = ['arr'];
            }

            $fields = \validator::ALL_POST($rules);


            //не вказан город
            if(empty($fields->city)){
                $fields->delete_field('city');
            }
            //пароли не указаны (оставляем без изменения)
            if(empty($fields->pass)){
                $fields->delete_field('pass');
            }
            //пароли не совпадают
            if(!empty($fields->pass) && $fields->pass != $fields->pass_confirm){
                $fields->delete_field('pass');
                $fields->delete_field('pass_confirm');
            }

            API::auto_validate($fields, function ($fields){

                if($fields->email_subscribe == 'on'){
                    $fields->email_subscribe = 1;
                }else{
                    $fields->email_subscribe = 0;
                }

                /* @var user $this->user */

                if($this->user->city != $fields->city){
                    $sort_ratings = \ORM::for_table('sort_rating')->where('user_id', $this->user->id)->find_many();
                    if($sort_ratings){
                        foreach ($sort_ratings as $sort_rating){
                            $sort_rating->city_id = $fields->city;
                            $sort_rating->save();
                        }
                    }
                }


                $this->user->update_profile($fields);
                API::response();
            });

        }


    }

    //Сообщения
    public function messages($data = []){

        if(isset($data['id'])){
            $user_id = $data['id'];
            $dialog = new dialog();

            $dialog_id = $dialog->isset_dialog($this->auth->user->id, $user_id);

            if(!$dialog_id){
                $dialog->user_id_1 = $this->auth->user->id;
                $dialog->user_id_2 = $user_id;
                $dialog->last_update = (new \DateTime())->format('Y-m-d H:i:s');
                $dialog->create();
                $dialog_id = $dialog->id;
            }

            DATA::set('selected_dialog', $dialog_id);

        }

        $this->layout = 'dashboard_transperent_for_messanger';

        if(is_ajax()){
            if($_POST['action'] == 'get_messages'){ //update_readed
                $fields = \validator::ALL_POST([
                    'dialog_id' => ['req', 'int']
                ]);
                API::auto_validate($fields, function (\validator $fields){
                    $messages = (new message())->get_messages_by_dialog($fields->dialog_id);

                    $messages_html = '';

                    ob_start();
                    if($messages){
                        /* @var $message message*/
                       foreach ($messages as $message){

                           $mes_type = 'sended';
                           $avatar_id = $this->user->id;

                           if($this->user->id !== $message->sender){
                               $mes_type = 'incoming';
                               $avatar_id = $message->sender;
                           }
                           ?>
                           <div class="message <?= $mes_type?>">
                               <img class="avatar" src="<?= get_avatar($avatar_id)?>">
                               <p class="body">
                                   <?= $message->body ?>
                                   <? if($this->user->id == $message->sender){?>
                                       <span class="message_status <?= ($message->readed == 1)? ' readed' : ''; ?>"><i class="icon icon-check"></i><i class="icon icon-check"></i></span>
                                   <?}?>
                               </p>
                               <p class="time"><?= (new \DateTime($message->time))->format('d.m.Y H:i:s') ?></p>
                           </div>
                       <?}
                    }

                    $messages_html.= ob_get_clean();

                    API::response(['messages_htmlcc' => $messages_html, 'dialog_id' => $fields->dialog_id]);
                });
            }

            if($_POST['action'] == 'get_chat_list'){

                $dialogs_list = (new dialogs_list())->get_all($this->user->id);

                $all_unread_count = 0;
                $chat_list_html = '';

                ob_start();
                /* @var $dialog \app\std_models\dialog */
                foreach ($dialogs_list as $dialog) {
                    $all_unread_count+= $dialog->unread_count;

                    ?>
                    <div class="dialog_item" dialog_id="<?= $dialog->id?>">
                        <? if(is_online_by_date($dialog->companion->last_visit, 600)){?>
                            <span class="online"></span>
                        <?}?>
                        <img class="avatar" src="<?= get_avatar($dialog->companion->id)?>">
                        <div class="name_box">
                            <span class="name" uid="<?= $dialog->companion->id ?>"><?= $dialog->companion->name.' '.$dialog->companion->surname?></span>
                            <span class="profession">Профессия</span>
                        </div>
                        <span class="counter <?= ($dialog->unread_count < 1)? 'hide': '';?>"><?= $dialog->unread_count ?></span>
                    </div>
                <?}

                $chat_list_html.= ob_get_clean();

                $return_data = [
                    'chat_list_html' => $chat_list_html,
                    'all_unread_count' => $all_unread_count,
                ];

                API::response($return_data);


            }
            
        }

        $dialogs_list = (new dialogs_list())->get_all($this->user->id);
        DATA::set('dialogs_list', $dialogs_list);

    }

    public function dialog_init(){

    }


    //Отзывы
    public function reviews(){

    }

    public function api_get_single_review($data){

        $this->is_api = true;

        $id = $data['id'];

        $review = new review($id);

        API::response($review);

    }


    public function review_add_answer(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'answer' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $review = new review($fields->id);
            if($review){
                $review->update($fields);
                API::response($review);
            }

            API::error(165, 'review id='.$fields->id.' not exist');

        });

    }

    public function review_add_complaint(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'complaint' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $review = new review($fields->id);
            if($review){
                $review->update($fields);
                API::response($review);
            }

            API::error(165, 'review id='.$fields->id.' not exist');

        });


    }

    public function add_review(){
        $this->is_api = true;

        $fields = \validator::ALL_POST([
            'id' => ['req', 'int'],
            'spec_id' => ['req', 'int'],
            'content' => ['req', 'str'],
            'star' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            $review = new review();

            $review->spec_id = $fields->spec_id;
            $review->user_id = $this->auth->user->id;
            $review->master_id = $fields->id;
            $review->reviewer_name = $this->auth->user->name.' '.$this->auth->user->surname;
            $review->time = (new \DateTime())->format('Y-m-d H:i:s');
            $review->score = $fields->star;
            $review->content = $fields->content;
            $review->create();

            $master = new user($fields->id);

            $finished = $master->master_data->get('finished');
            $finished++;
            $master->master_data->set('finished', $finished);
            $master->master_data->save_data();

            (new rebuild_rating())->rebuild_user($fields->id);

            API::response();




        });


    }

    public function favorites(){
        $this->layout = 'dashboard_transperent';
        $catalog_status = (new catalog_model())->get_favorites($this->user->customer_data->get('favorites'));

    }

    //История заказов для заказчика
    public function history(){

        $catalog_status = (new catalog_model())->get_orders($this->user->customer_data->get('orders'));

    }

    //Настройки для мастера
    public function settings(){


        if(is_ajax()){

            $rules = [
                'week_1' => ['bool'],
                'week_2' => ['bool'],
                'week_3' => ['bool'],
                'week_4' => ['bool'],
                'week_5' => ['bool'],
                'week_6' => ['bool'],
                'week_7' => ['bool'],
                'open_time' => ['time'],
                'close_time' => ['time'],
                'contract' => ['bool'],
                'guarantee' => ['bool'],
            ];

            $fields = \validator::ALL_POST($rules);

            //API::response($fields);


            API::auto_validate($fields, function ($fields){

                $this->user->master_data->update($fields);
                (new rebuild_rating())->rebuild_user($this->user->id);

                API::response();
            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }


    }


    public function contacts(){

        if(is_ajax()){

            $rules = [
                'phone'     => ['req', 'phone'],
                'telegram'  => ['str'],
                'whatsapp'  => ['str', 'phone'],
                'viber'     => ['str', 'phone'],
                'vk'        => ['str'],
                'fb'        => ['str'],
                'inst'      => ['str'],
            ];

            $fields = \validator::ALL_POST($rules);

            if(strlen($fields->telegram) > 1){
                $fields->telegram = trim($fields->telegram, '@');
            }else{
                $fields->telegram = '';
            }



            API::auto_validate($fields, function ($fields){

                $this->user->master_data->update($fields);
                (new rebuild_rating())->rebuild_user($this->user->id);
                API::response();
            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }

    }

    public function prices(){

        if(is_ajax()){

            $rules = [
                'spec_id'  => ['req', 'int'],
                'price'     => ['req', 'int'],
            ];

            $fields = \validator::ALL_POST($rules);

            API::auto_validate($fields, function (\validator $fields){

                $fields->add_field('user_id', $this->user->id);

                $updated = (new sort_rating_prices())->update_price($fields);

                if($updated){
                    API::response(['price' => $fields->price]);
                }else{
                    API::error('446','spec_id and user_id are not related');
                }

            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }

    }

    public function finished(){

        if(is_ajax()){

            $rules = [
                'spec_id'  => ['req', 'int'],
                'name'     => ['req', 'str'],
                'content'  => ['req', 'str'],
                'price'     => ['req', 'int'],
                'medias' => ['arr'],
            ];

            $fields = \validator::ALL_POST($rules);

            API::auto_validate($fields, function (\validator $fields){

                $fields->add_field('user_id', $this->user->id);

                $work = (new work())->create($fields);
                (new rebuild_rating())->rebuild_user($this->user->id);

                API::response($work);

            });

        }

        if($this->user->is_customer()){
            $this->redirect('dashboard');
        }

    }







}