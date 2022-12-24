<?php

use core\engine\DATA;
use app\std_models\user;
use core\engine\media;

/**
 * @var user $user
 */
$user = DATA::get('USER');





?>


<div class="lk_content">

    <div class="avatar_wrapper">
        <div class="avatar my_avatar">

            <?
            if(file_exists(WEB.'uploads/avatars/'.$user->id.'.png?v='.rand(100000,999999))){?>
                <img src="<?= URL?>uploads/avatars/<?= $user->id; ?>.png">
            <?}else{?>
                <img src="<?= URL?>assets/img/no-photo.jpg">
            <?}?>

        </div>
        <div class="edit_avatar"><i class="icon icon-edit"></i></div>
    </div>

    <div class="lk_data">
        <p class="name"><?= $user->name.' '.mb_substr($user->surname, 0, 1).'.' ?></p>

        <? if($user->is_master()){?>
            <p class="profession"><?= $user->master_data->get('profession') ?></p>
        <?}?>


        <h2>Личная информация</h2>

        <form class="update_profile" method="post">

            <div class="row">
                <div class="c_6">
                    <div class="field_wrap">
                        <label for="surname">Фамилия</label>
                        <input type="text" id="surname" name="surname" class="field field_full" placeholder="Фамилия" value="<?= $user->surname; ?>">
                    </div>
                </div>
                <div class="c_6">
                    <div class="field_wrap">
                        <label for="name">Имя</label>
                        <input type="text" id="name" name="name" class="field field_full" placeholder="Имя" value="<?= $user->name; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="c_6">
                    <div class="field_wrap">
                        <label for="mail">Email</label>
                        <input type="text" id="mail" name="mail" class="field field_full" placeholder="Ваш E-mail" value="<?= $user->mail; ?>">
                    </div>
                </div>
                <div class="c_6">
                    <div class="field_wrap">
                        <label for="city">Город</label>
                        <select id="city" name="city" class="select_full select_city">
                            <?
                            if(!empty( $user->city )){
                                echo '<option value="'.$user->city.'">'.get_city_name($user->city).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="c_6">
                    <div class="field_wrap">
                        <label for="edit_pass">Новый пароль</label>
                        <input type="text" id="edit_pass" name="pass" class="field field_full" placeholder="Пароль">
                    </div>
                </div>
                <div class="c_6">
                    <div class="field_wrap">
                        <label for="edit_pass_confirm">Повторите пароль</label>
                        <input type="password" id="edit_pass_confirm" name="pass_confirm" class="field field_full" autocomplete="on">
                    </div>
                </div>
            </div>

            <? if($user->is_master()){?>
                <div class="row">
                    <div class="c_3">
                        <div class="field_wrap">
                            <label for="birthday">Дата рождения</label>
                            <input type="date" id="birthday" name="birthday" class="field field_full" value="<?= $user->birthday?>">
                        </div>
                    </div>
                    <div class="c_3">
                        <div class="field_wrap">
                            <label for="experience">Опыт работы</label>

                            <? $experience = $user->master_data->get('experience');?>

                            <select id="experience" class="select_full" name="experience">
                                <option value="">Выберите</option>
                                <? foreach (EXP_TEXTS as $key => $text) {

                                    if($key == $experience){
                                        echo '<option value="'.$key.'" selected>'.$text.'</option>';
                                        continue;
                                    }

                                    echo '<option value="'.$key.'">'.$text.'</option>';

                                }?>
                            </select>
                        </div>
                    </div>
                </div>
            <?}?>


            <div class="field_wrap">
                <div class="row">
                    <div class="checkbox_input">
                        <input type="checkbox" id="email_subscribe" name="email_subscribe" checked>
                        <label for="email_subscribe">Получать рассылку новостей о сервисе на почту</label>
                    </div>
                </div>
            </div>

            <? if($user->is_master()){?>

                <script>window.all_my_spec = <? echo (!empty($user->master_data->get('spec')) ? $user->master_data->get('spec') : '[]')?>;</script>

                <h2>Специализация</h2>
                <div class="specialization">

                    <div class="spec_selector_form">
                        <div class="c_6">

                            <div class="field_wrap spec_select">
                                <label for="spec_temp">Добавить специализацию</label>
                                <div class="micro_form">
                                    <input class="spec_temp" type="hidden" name="find">
                                    <input type="text" id="spec_temp" class="field spec_field" placeholder="Новая специализация">
                                    <!--  <div class="button add_spec"><i class="icon icon-add"></i></div> -->
                                </div>

                                <ul class="find_field_dropdown"></ul>
                            </div>
                        </div>

                        <div class="tags tags_float tags_spec">
                            <?
                            $specs = $user->get_specs();
                            /* @var $spec \app\std_models\speciality */
                            foreach ($specs as $spec){?>

                                <div class="tag_item">
                                    <span><?= $spec->name?></span>
                                    <i class="icon icon-cancel delete_spec_item"></i>
                                </div>
                            <?}?>

                        </div>

                    </div>

                </div>
            <?}?>

            <div class="row">
                <div class="c_12">
                    <input class="button button_save save_profile" type="submit" value="Сохранить изменения">
                    <a class="button_delete_profile delprofile"><i class="icon icon-delete"></i>Удалить профиль</a>
                </div>
            </div>

        </form>

    </div>


</div>



