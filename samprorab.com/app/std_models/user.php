<?php

namespace app\std_models;

use core\engine\API;
use core\engine\AUTH;
use core\engine\debugger;
use core\engine\sms;
use core\engine\view;

class user{

    public $id, $activity_state, $name, $surname, $pass, $mail, $phone, $city, $role, $status, $birthday, $email_subscribe, $reg_time, $last_visit, $last_ip;

    protected $ormdata = null;

    public ?customer_data $customer_data;
    public ?master_data $master_data;
    protected $spec_list = [];
    protected $services_ids_by_spec = [];




    public function __construct($id = null){

        if($id != null){
            $this->get_user($id);
        }

        return $this;

    }

    public function update_city($city_id){
        if($this->ormdata){
            $this->ormdata->city = $city_id;
            $this->ormdata->save();
        }
    }

    public function auth(\validator $data){

        $user = $this->get_user_by_phone($data->phone);
        if($user){
            if(password_verify($data->pass, $user->pass)){
                $this->remap_data($user);
                return $this;
            }
        }
        return false;
    }

    protected function get_user_by_phone($phone){
        $user = \ORM::for_table('users')->where('phone', $phone)->find_one();

        if($user){
            return $user;
        }
        return false;

    }

    public function update_last_visit(){

        $current_ip = getIp();

       if(!in_array($current_ip, micro_services_ip)){
           $this->last_visit = (new \DateTime())->format('Y-m-d H.i.s');
           $this->last_ip = $current_ip;

           $this->update();
       }

    }

    public function lostpass($phone){

        $temp_user = $this->get_user_by_phone($phone);

        if($temp_user){

            $user = $this->set_user($temp_user);

            $auth = AUTH::init();
            $auth->destroy_all_user_session($user->id);


            $user->activity_state = 1;
            $user->update();

            $sms = sms::init($user, 'lostpass');

            $response = [
                'id' => $user->id,
                'sms_hash' => $sms['hash'],
            ];



            if(sms_debug){
                $response['sms_debug'] = $sms['code'];
            }

            return $response;

        }

        return false;


    }

    public function change_passwors($pass){
        $this->activity_state = 100;
        $this->pass = password_hash($pass, PASSWORD_DEFAULT);
        $this->update();
    }

    public function switch_role(){
        if($this->role == 'master'){
            $this->role = 'customer';
        }else{
            $this->role = 'master';
        }
        $this->update();
    }

    public function create($data){

        $temp_user = \ORM::for_table('users')->where('phone', $data['phone'])->find_one();

        if(!$temp_user){

            $cols = $this->get_columns();

            $new_user = \ORM::for_table('users')->create();

            foreach ($cols as $key => $empty_val){
                if(isset($data[$key])){
                    $field = $data[$key];
                    if(is_array($this->$key)){
                        $field = json_encode($this->$key, JSON_UNESCAPED_UNICODE);
                    }

                    if($key == 'pass'){
                        $field = password_hash($field, PASSWORD_DEFAULT);
                    }

                    $new_user->$key = $field;
                }
            }

            $new_user->activity_state = 1;
            $new_user->status = 1;
            $new_user->set_expr('reg_time', "NOW()");

            $new_user->save();


            $master_data = new master_data();
            $customer_data = new customer_data();

            $master_data->create_empty_row($new_user->id);
            $customer_data->create_empty_row($new_user->id);

            $user = $this->set_user($new_user);

            $sms = sms::init($user, 'reg');

            $response = [
                'id' => $new_user->id,
                'sms_hash' => $sms['hash'],
            ];

            if(sms_debug){
                $response['sms_debug'] = $sms['code'];
            }

            return $response;
        }


        return false;

    }

    public function confirm_reg(){
        $this->activity_state = 100;
        $this->update();
    }

    public function update_profile($fields){

        $cols = $this->get_columns();

        foreach ($cols as $key => $col){
            if(isset($fields->$key)){
                if($fields->$key instanceof \DateTime){
                    $this->$key = $fields->$key->format('Y-m-d');
                    continue;
                }

                if($key == 'pass'){
                    $hash = password_hash($fields->$key, PASSWORD_DEFAULT);
                    $fields->$key = $hash;
                    $this->$key = $hash;
                }

                $this->$key = $fields->$key;
            }
        }

        if(isset($fields->spec) && is_array($fields->spec)){

            $specs_in_rating = \ORM::for_table('sort_rating')->where('user_id', $this->id)->find_many();
            $specs_temp = [];
            if($specs_in_rating){
                foreach ($specs_in_rating as $spec_row){
                    $specs_temp[$spec_row->spec_id] = $spec_row;
                    if(!isset($fields->spec[$spec_row->spec_id])){
                        $spec_row->enabled = 0;
                        $spec_row->save();
                    }
                }
            }

            foreach ($fields->spec as $id => $spec_name){
                if(isset($specs_temp[$id])){

                    if($specs_temp[$id]->enabled == 0){
                        $specs_temp[$id]->enabled = 1;
                        $specs_temp[$id]->save();
                    }

                }else{
                    $new_row = \ORM::for_table('sort_rating')->create();
                    $new_row->user_id = $this->id;
                    $new_row->spec_id = $id;
                    $new_row->city_id = $this->city;
                    $new_row->enabled = 1;
                    $new_row->save();
                }

            }

        }



        if($this->is_master()){
            $custom_data = \ORM::for_table('master_data')->where('user_id', $this->id)->find_one();


            if(!$custom_data){
                $custom_data = (new master_data())->create_empty_row($this->id, true);
            }

            foreach ($this->master_data->data_arrmap as $key){
                if(isset($fields->$key)){
                    $custom_data->$key = $fields->$key;
                }
            }
            $custom_data->save();
        }

        if($this->is_customer()){
            $custom_data = \ORM::for_table('customer_data')->where('user_id', $this->id)->find_one();

            if(!$custom_data){
                $custom_data = (new customer_data())->create_empty_row($this->id, true);
            }

            //API::response($this->master_data);
            foreach ($this->customer_data->data_arrmap as $key){
                if(isset($fields->$key)){
                    $custom_data->$key = $fields->$key;
                }
            }
            $custom_data->save();
        }

        $this->update();

    }

    protected function get_custom_data(){

        $this->customer_data = new customer_data($this->id);
        $this->master_data = new master_data($this->id);

    }

    public function get_specs(){
        return $this->spec_list;
    }

    public function get_servises_ids_by_spec(){
        return $this->services_ids_by_spec;
    }




    protected function set_spec_list(){

        $services = \ORM::for_table('sort_rating')
            ->where([
                'user_id' => $this->id,
                'enabled' => 1
            ])
            ->find_many();

        if(!$services){
            return false;
        }

        $spec_ids = [];
        foreach ($services as $service){

            $this->services_ids_by_spec[$service->spec_id] = $service->id;

            $spec_ids[] = $service->spec_id;
        }

        $this->spec_list = (new speciality_list())->get_by_ids($spec_ids);

    }







    protected function get_user($id){
        $user = \ORM::for_table('users')->find_one($id);
        if($user){
            $this->remap_data($user);
        }
        return false;
    }

    public function set_user($orm_data){
        //$this->ormdata = $orm_data;

        $this->remap_data($orm_data);

        return $this;
    }

    public function is_master(){
        if($this->role == 'master'){
            return true;
        }
        return false;
    }

    public function is_customer(){
        if($this->role == 'customer'){
            return true;
        }
        return false;
    }

    protected function remap_data($user){

        $this->ormdata = $user;

        foreach ($user->_data as $key => $val){
            $this->$key = $val;
        }

        $this->get_custom_data();
        $this->set_spec_list();

    }

    public function get_columns(array $ignore_cols = null){
        //always ignore ID
        $ignore_cols[] = 'id';
        $table_name = 'users';

        $t = \ORM::raw_execute("SHOW columns FROM $table_name;");
        $statement = \ORM::get_last_statement();

        $cols = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            if(!in_array($row['Field'], $ignore_cols)){
                $cols[$row['Field']] = '';
            }
        }

        return $cols;

    }

    public function update(){
        foreach ($this->ormdata->_data as $key => $val){
            $this->ormdata->$key = $this->$key;
        }
        $this->ormdata->save();
    }

}