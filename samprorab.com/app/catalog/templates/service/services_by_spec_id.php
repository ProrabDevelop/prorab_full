<?php

use core\engine\DATA;
use core\engine\media;
use core\engine\media_list;


/* @var $speciality \app\std_models\speciality */
/* @var $user \app\std_models\user */
$speciality = DATA::get('speciality');
$user = DATA::get('user');

//favorites chech
$favorite = 'hide';
if(DATA::has('USER')){
    $me = DATA::get('USER');
    $favorite = '';
    $order_button_text = 'Работаю с мастером';

    /* @var $me \app\std_models\user */
    if(in_array($user->service_id, $me->customer_data->get('favorites'))){
        $favorite = 'checked';
    }

    $orders = $me->customer_data->get('orders');
    $orders_ids = [];

    if($orders){
        foreach ($orders as $order){
            $orders_ids[] = $order['id'];
        }
    }

    if(in_array($user->service_id, $orders_ids )){
        $order_button_text = 'Прекратить работу';
    }

}

?>

<div class="service_single xs_hide">

    <div class="profile_info">

        <div class="avatar_wrapper">

            <i class="favorite icon icon-star <?= $favorite?>" service_id="<?= $user->service_id; ?>"></i>

            <div class="avatar">
                <img src="<?= get_avatar($user->id);?>">
                <? if(is_online_by_date($user->last_visit, 600)){?>
                    <span class="online"></span>
                <?}?>

            </div>

            <div class="rating_wrapper">

                <? $all_score = null;
                if(!empty($user->reviews)) {

                    $temp_score = 0;
                    foreach ($user->reviews as $review){
                        $temp_score+= $review->score;
                    }

                    $all_score = round($temp_score / count($user->reviews), 1);
                }

                ?>

                <span class="count"><?= ($all_score != null) ? $all_score : '0.0';?></span>
                <div class="rating_stars">

                    <? render_custom_stars($all_score); ?>

                </div>
            </div>

        </div>

        <div class="base_info">
            <p class="name"><?= $user->name.' '.mb_substr($user->surname, 0, 1).'.' ?></p>
            <p class="profession"><?= $speciality->name ?></p>

            <p class="age">Возраст: <span><?= get_age($user->birthday);?></span></p>
            <p class="city">Адрес: <span><?= get_city_name($user->city);?></span></p>

            <span class="xs_only reg_time_xs">На сайте с <?= r_date($user->reg_time)?> <?= get_age($user->reg_time, '(', ')')?></span>


        </div>

        <div class="second_info">

            <div class="intaractiv_panel">

                <? $phone = $user->master_data->get('phone');
                if(!empty($phone)){
                    echo '<a class="button see_phone" phone="'.formatphone($phone).'" phone_raw="'.$phone.'">Показать телефон</a>';
                } ?>

                <? if(isset($me) && $me->is_customer()){
                    echo '<a class="button start_dialog" href="'.URL.'messages/'.$user->id.'"><i class="icon icon-messenger"></i> Начать диалог</a>';
                }?>

                <? $whatsapp = $user->master_data->get('whatsapp');
                if(!empty($whatsapp)){
                    echo '<a class="button messengers" href="https://wa.me/'.$whatsapp.'" target="_blank"><i class="icon icon-whatsapp"></i></a>';
                } ?>


                <? $viber = $user->master_data->get('viber');
                if(!empty($viber)){
                    echo '<a class="button messengers" href="viber://chat?number=%2B'.$viber.'" target="_blank"><i class="icon icon-viber"></i></a>';
                } ?>

                <? $telegram = $user->master_data->get('telegram');
                if(!empty($telegram)){
                    echo '<a class="button messengers" href="https://t.me/'.$telegram.'" target="_blank"><i class="icon icon-tg"></i></a>';
                } ?>

            </div>

            <div class="info_panel">

                <div>
                    <span>Работа по договору:</span>
                    <img src="<?= URL.'assets/img/icons/'.(($user->master_data->get('contract') == 0) ? 'false' : 'true').'.svg'; ?>">
                </div>

                <div>
                    <span>Дает гарантию:</span>
                    <img src="<?= URL.'assets/img/icons/'.(($user->master_data->get('guarantee') == 0) ? 'false' : 'true').'.svg'; ?>">
                </div>

                <div>
                    <span>Проверен сервисом:</span>
                    <img src="<?= URL.'assets/img/icons/'.(($user->master_data->get('type_ooo') != 0 || $user->master_data->get('type_ip') != 0) ? 'true' : 'false').'.svg'; ?>">
                </div>

                <div>
                    <span>Выполненных заказов: <?= intval($user->master_data->get('finished')) ?></span>
                </div>

                <div>
                    <span class="regtime">На сайте с <?= r_date($user->reg_time)?> <?= get_age($user->reg_time, '(', ')')?></span>
                </div>

            </div>

            <? if(isset($me) && $me->is_customer()){?>
                <div class="user_controls">

                    <!--<div class="open_dialog button">Начать диалог</div>-->

                    <div class="work_with_master button" service_id="<?= $user->service_id; ?>"><?= $order_button_text;?></div>

                </div>
            <?} ?>



        </div>

    </div>

    <div class="service_tabs_wrapper">

        <ul class="tabs_titles">
            <li class="select_tab active" role="global_tabs" tab="master">О мастере</li>
            <? if(!empty($user->services)){?>
                <li class="select_tab" role="global_tabs" tab="prices">Услуги и цены (<?= count($user->services); ?>)</li>
            <?}?>
            <? if(!empty($user->works)) { ?>
                <li class="select_tab" role="global_tabs" tab="works">Выполненные работы (<?= count($user->works); ?>)</li>
            <?}?>
            <? if(!empty($user->reviews)) { ?>
                <li class="select_tab" role="global_tabs" tab="reviews">Отзывы (<?= count($user->reviews); ?>)</li>
            <?}?>
        </ul>


        <div class="service_tabs_content_wrapper">

            <div class="tab_content active" role="global_tabs" tab="master">
                <div class="row tab_master">

                    <div class="c_6 xs_c_12">
                        <h2>О мастере</h2>

                        <div class="table_master_info">
                            <div class="row">


                                <?if($user->master_data->get('open_time') != '00:00:00' && $user->master_data->get('close_time') != '00:00:00'){?>
                                    <p>Время работы:</p>
                                    <p>с <?= (new DateTime($user->master_data->get('open_time')))->format('H:i') ?> до <?= (new DateTime($user->master_data->get('close_time')))->format('H:i') ?> </p>
                                <?}?>

                            </div>
                            <div class="row">
                                <p>Дни работы:</p>
                                <p>
                                    <?= ($user->master_data->get('week_1') == 1) ? 'Пн, ' : '— , '; ?>
                                    <?= ($user->master_data->get('week_2') == 1) ? 'Вт, ' : '— , '; ?>
                                    <?= ($user->master_data->get('week_3') == 1) ? 'Ср, ' : '— , '; ?>
                                    <?= ($user->master_data->get('week_4') == 1) ? 'Чт, ' : '— , '; ?>
                                    <?= ($user->master_data->get('week_5') == 1) ? 'Пт, ' : '— , '; ?>
                                    <?= ($user->master_data->get('week_6') == 1) ? 'Сб, ' : '— , '; ?>
                                    <?= ($user->master_data->get('week_7') == 1) ? 'Вс' : '—'; ?>
                                </p>
                            </div>
                            <!--
                            <div class="row">
                                <p>Районы Выезда:</p>
                                <p>Районы (УТОЧНИТЬ)</p>
                            </div>
                            -->
                            <div class="row">
                                <p>Опыт работы:</p>
                                <p><?= EXP_TEXTS[$user->master_data->get('experience')]?></p>
                            </div>



                            <?
                            $company_type = false;
                            if($user->master_data->get('type_ip') == 1){
                                $company_type = 'ИП';
                            }
                            if($user->master_data->get('type_ooo') == 1){
                                $company_type = 'ООО';
                            }

                            if($company_type){?>
                                <div class="row">
                                    <p>Зарегистрирован в качестве юр. лица:</p>
                                    <p><?= $company_type ?></p>
                                </div>
                            <?}?>




                        </div>

                    </div>

                    <div class="c_6 xs_c_12">
                        <h2>Работы выполняемые мастером</h2>

                        <div class="tags">

                            <? $specs = $user->get_specs();

                            $servises_ids_by_spec = $user->get_servises_ids_by_spec();

                            foreach ($specs as $spec){
                                echo '<a class="tag_item" href="'.URL.'service/'.$servises_ids_by_spec[$spec->id].'">'.$spec->name_cat.'</a>';
                            }
                            ?>

                        </div>

                        <br>

                        <?
                        $vk = $user->master_data->get('vk');
                        $inst = $user->master_data->get('inst');
                        $fb = $user->master_data->get('fb');

                        if(!empty($vk) || !empty($inst) || !empty($fb)){?>
                            <h2>Дополнительная информация</h2>

                            <div class="buttons_social">
                                <?if(!empty($vk)){?>
                                    <a href="https://vk.com/<?=$vk?>" class="button" target="_blank">VKontakte</a>
                                <?}?>
                                <?if(!empty($inst)){?>
                                    <a href="https://www.instagram.com/<?=$inst?>" class="button" target="_blank">Instagram</a>
                                <?}?>
                                <?if(!empty($fb)){?>
                                    <a href="https://www.facebook.com/<?=$fb?>" class="button" target="_blank">Facebook</a>
                                <?}?>

                            </div>

                        <?}?>

                    </div>

                </div>
            </div>

            <? if(!empty($user->services)){?>
                <div class="tab_content " role="global_tabs" tab="prices">
                    <h2>Услуги и цены</h2>
                    <div class="price_table">
                        <?
                        /* @var $service \app\std_models\user_service*/
                        foreach ($user->services as $service){?>
                            <div class="price_item">
                                <span class="name"><?= $service->name ?></span>
                                <span class="price"><?= $service->get_correct_price() ?></span>
                            </div>
                        <?}?>
                    </div>
                </div>
            <?}?>

            <? if(!empty($user->works)) { ?>
                <div class="tab_content" role="global_tabs" tab="works">

                    <div>
                        <h2>Выполненные работы</h2>
                    </div>


                    <div class="works_tabs_wrapper">
                        <div class="work_items">
                            <?
                            /* @var $work \app\std_models\work */
                            foreach ($user->works as $work) {?>
                                <div class="work_item get_modal set_work_item" modal="modal_work_item" wrap_type="white_bg" work_id="<?= $work->id?>">

                                    <div class="img">
                                        <? $media = $work->get_first_media();
                                        /* @var $media media */?>
                                        <img src="<?= $media->get_url('160x130'); ?>">
                                    </div>

                                    <div class="information">
                                        <div class="title_wrap">
                                            <span class="title"><?= $work->name ?></span>
                                            <span class="price"><?= money_beautiful($work->price);?> ₽</span>
                                        </div>

                                        <div class="content">
                                            <?= crop_text(WORK_WORD_COUNT, $work->content); ?>
                                        </div>
                                    </div>

                                </div>
                            <? }?>

                        </div>
                    </div>
                </div>
            <?}?>

            <? if(!empty($user->reviews)) { ?>
                <div class="tab_content" role="global_tabs" tab="reviews">

                    <h2>Отзывы</h2>


                    <div class="reviews_wrapper">

                        <?
                        /* @var $review  \app\std_models\review */
                        foreach ($user->reviews as $review){?>
                            <div class="review_item">

                                <div class="avatar_wrapper">
                                    <img class="avatar" src="<?= get_avatar($review->user_id);?>">
                                </div>

                                <div class="cont_wrapper">

                                    <div class="title_wrap">
                                        <p class="name"> <?= $review->reviewer_name; ?></p>
                                        <p class="time"> <?= human_date($review->time); ?></p>
                                    </div>
                                    <div class="rating_stars">
                                        <? $review->render_stars();?>
                                    </div>

                                    <div class="review_content_wrapper">

                                        <? $croped_content = crop_text(REVIEW_WORD_COUNT, $review->content) ?>

                                        <div class="all_cont review_data_cont get_cuted">
                                            <div class="content_cuted">
                                                <? if(strlen($croped_content) < strlen($review->content)){
                                                    echo $croped_content.' ...';
                                                }else{
                                                    echo $review->content;
                                                }?>
                                            </div>
                                            <? if(strlen($croped_content) < strlen($review->content)){?>
                                                <div class="content_full">
                                                    <?= $review->content;?>
                                                </div>
                                            <?}?>
                                        </div>

                                        <? if(strlen($croped_content) < strlen($review->content)){?>
                                            <p class="open_review">Развернуть</p>
                                        <?}?>




                                        <?if($review->answer != null){?>
                                            <p class="view_all_review_cont"><i class="icon icon-review_all"></i>Посмотреть ответ</p>
                                            <div class="answer_cont"><?= $review->answer;?></div>
                                        <?}?>

                                    </div>

                                </div>


                            </div>
                        <?} ?>

                    </div>

                </div>
            <?}?>

        </div>

    </div>

</div>

<? // XS /////////////////////////////////////////////// ?>

<div class="service_single_xs xs_only">

    <div class="profile_info">

        <div class="avatar_wrapper">

            <i class="favorite icon icon-star <?= $favorite?>" service_id="<?= $user->service_id; ?>"></i>

            <div class="avatar">
                <img src="<?= get_avatar($user->id);?>">
                <? if(is_online_by_date($user->last_visit, 600)){?>
                    <span class="online"></span>
                <?}?>
            </div>

            <div class="rating_wrapper">

                <? $all_score = null;
                if(!empty($user->reviews)) {

                    $temp_score = 0;
                    foreach ($user->reviews as $review){
                        $temp_score+= $review->score;
                    }

                    $all_score = round($temp_score / count($user->reviews), 1);
                }

                ?>

                <span class="count"><?= ($all_score != null) ? $all_score : '0.0';?></span>
                <div class="rating_stars">

                    <? render_custom_stars($all_score); ?>

                </div>
            </div>

        </div>

        <div class="base_info">
            <p class="name"><?= $user->name.' '.mb_substr($user->surname, 0, 1).'.' ?></p>
            <p class="profession"><?= $speciality->name ?></p>

            <p class="regtime">На сайте с <?= r_date($user->reg_time)?> <?= get_age($user->reg_time, '(', ')')?></p>

        </div>

        <div class="service_tabs_wrapper_xs">

            <ul class="tabs_titles">
                <li class="select_tab active" role="global_tabs" tab="master">О мастере <i class="icon icon-right-open"></i></li>
                <? if(!empty($user->services)){?>
                    <li class="select_tab" role="global_tabs" tab="prices">Услуги <i class="icon icon-right-open"></i></li>
                <?}?>
                <? if(!empty($user->works)) { ?>
                    <li class="select_tab" role="global_tabs" tab="works">Работы <i class="icon icon-right-open"></i></li>
                <?}?>
                <? if(!empty($user->reviews)) { ?>
                    <li class="select_tab" role="global_tabs" tab="reviews">Отзывы <i class="icon icon-right-open"></i></li>
                <?}?>
            </ul>

            <div class="service_tabs_content_wrapper">

                <div class="tab_content" role="global_tabs" tab="master">

                    <!--<div class="back_step_tabs">
                        <p class="close_tab"><i class="icon icon-left-open"></i><span>Назад</span></p>
                    </div>-->

                    <div class="row tab_master">

                        <div class="xs_c_12">
                            <h2>О мастере</h2>

                            <div class="table_master_info">
                                <div class="row">
                                    <?if($user->master_data->get('open_time') != '00:00:00' && $user->master_data->get('close_time') != '00:00:00'){?>
                                        <p>Время работы:</p>
                                        <p>с <?= (new DateTime($user->master_data->get('open_time')))->format('H:i') ?> до <?= (new DateTime($user->master_data->get('close_time')))->format('H:i') ?> </p>
                                    <?}?>
                                </div>
                                <div class="row">
                                    <p>Дни работы:</p>
                                    <p>
                                        <?= ($user->master_data->get('week_1') == 1) ? 'Пн, ' : '— , '; ?>
                                        <?= ($user->master_data->get('week_2') == 1) ? 'Вт, ' : '— , '; ?>
                                        <?= ($user->master_data->get('week_3') == 1) ? 'Ср, ' : '— , '; ?>
                                        <?= ($user->master_data->get('week_4') == 1) ? 'Чт, ' : '— , '; ?>
                                        <?= ($user->master_data->get('week_5') == 1) ? 'Пт, ' : '— , '; ?>
                                        <?= ($user->master_data->get('week_6') == 1) ? 'Сб, ' : '— , '; ?>
                                        <?= ($user->master_data->get('week_7') == 1) ? 'Вс' : '—'; ?>
                                    </p>
                                </div>
                                <div class="row">
                                    <p>Опыт работы:</p>
                                    <p><?= EXP_TEXTS[$user->master_data->get('experience')]?></p>
                                </div>

                                <?
                                $company_type = false;
                                if($user->master_data->get('type_ip') == 1){
                                    $company_type = 'ИП';
                                }
                                if($user->master_data->get('type_ooo') == 1){
                                    $company_type = 'ООО';
                                }

                                if($company_type){?>
                                    <div class="row">
                                        <p>Зарегистрирован в качестве юр. лица:</p>
                                        <p><?= $company_type ?></p>
                                    </div>
                                <?}?>


                                <h2>Работы выполняемые мастером</h2>

                                <div class="tags">

                                    <? $specs = $user->get_specs();

                                    $servises_ids_by_spec = $user->get_servises_ids_by_spec();

                                    foreach ($specs as $spec){
                                        echo '<a class="tag_item" href="'.URL.'service/'.$servises_ids_by_spec[$spec->id].'">'.$spec->name_cat.'</a>';
                                    }
                                    ?>

                                </div>

                                <?
                                $vk = $user->master_data->get('vk');
                                $inst = $user->master_data->get('inst');
                                $fb = $user->master_data->get('fb');
                                ?>

                                <div class="buttons_social">

                                    <? $whatsapp = $user->master_data->get('whatsapp');
                                    if(!empty($whatsapp)){
                                        echo '<a class="button messengers" href="https://wa.me/'.$whatsapp.'" target="_blank"><i class="icon icon-whatsapp"></i></a>';
                                    } ?>

                                    <? $viber = $user->master_data->get('viber');
                                    if(!empty($viber)){
                                        echo '<a class="button messengers" href="viber://chat?number=%2B'.$viber.'" target="_blank"><i class="icon icon-viber"></i></a>';
                                    } ?>

                                    <? $telegram = $user->master_data->get('telegram');
                                    if(!empty($telegram)){
                                        echo '<a class="button messengers" href="https://t.me/'.$telegram.'" target="_blank"><i class="icon icon-tg"></i></a>';
                                    } ?>

                                    <?if(!empty($vk)){?>
                                        <a href="https://vk.com/<?=$vk?>" class="button" target="_blank"><i class="icon icon-vk"></i></a>
                                    <?}?>
                                    <?if(!empty($inst)){?>
                                        <a href="https://www.instagram.com/<?=$inst?>" class="button" target="_blank"><i class="icon icon-insta_1"></i></a>
                                    <?}?>
                                    <?if(!empty($fb)){?>
                                        <a href="https://www.facebook.com/<?=$fb?>" class="button" target="_blank"><i class="icon icon-facebook_1"></i></a>
                                    <?}?>

                                </div>



                            </div>

                        </div>



                    </div>
                </div>

                <? if(!empty($user->services)){?>
                    <div class="tab_content " role="global_tabs" tab="prices">

                        <!--<div class="back_step_tabs">
                            <p class="close_tab"><i class="icon icon-left-open"></i><span>Назад</span></p>
                        </div>-->

                        <h2>Услуги и цены</h2>
                        <div class="price_table">
                            <?
                            /* @var $service \app\std_models\user_service*/
                            foreach ($user->services as $service){?>
                                <div class="price_item">
                                    <span class="name"><?= $service->name ?></span>
                                    <span class="price"><?= $service->get_correct_price() ?></span>
                                </div>
                            <?}?>
                        </div>
                    </div>
                <?}?>

                <? if(!empty($user->works)) { ?>
                    <div class="tab_content" role="global_tabs" tab="works">

                        <!--<div class="back_step_tabs">
                            <p class="close_tab"><i class="icon icon-left-open"></i><span>Назад</span></p>
                        </div>-->

                        <h2>Выполненные работы</h2>

                        <div class="works_tabs_wrapper">
                            <div class="work_items">
                                <?
                                /* @var $work \app\std_models\work */
                                foreach ($user->works as $work) {?>
                                    <div class="work_item get_modal set_work_item" modal="modal_work_item" wrap_type="white_bg" work_id="<?= $work->id?>">

                                        <div class="img">
                                            <? $media = $work->get_first_media();
                                            /* @var $media media */?>
                                            <img src="<?= $media->get_url('160x130'); ?>">
                                        </div>

                                        <div class="information">
                                            <div class="title_wrap">
                                                <span class="title"><?= $work->name ?></span>
                                                <span class="price"><?= money_beautiful($work->price);?> ₽</span>
                                            </div>

                                            <div class="content">
                                                <?= crop_text(WORK_WORD_COUNT, $work->content); ?>
                                            </div>
                                        </div>

                                    </div>
                                <? }?>

                            </div>
                        </div>
                    </div>
                <?}?>

                <? if(!empty($user->reviews)) { ?>
                    <div class="tab_content" role="global_tabs" tab="reviews">

                        <!--<div class="back_step_tabs">
                            <p class="close_tab"><i class="icon icon-left-open"></i><span>Назад</span></p>
                        </div>-->

                        <h2>Отзывы</h2>


                        <div class="reviews_wrapper">

                            <?
                            /* @var $review  \app\std_models\review */
                            foreach ($user->reviews as $review){?>
                                <div class="review_item">

                                    <div class="avatar_wrapper">
                                        <img class="avatar" src="<?= get_avatar($review->user_id);?>">
                                    </div>

                                    <div class="cont_wrapper">

                                        <div class="centered_review_data">
                                            <p class="name"> <?= $review->reviewer_name; ?></p>

                                            <div class="rating_stars">
                                                <? $review->render_stars();?>
                                            </div>
                                        </div>

                                        <div class="review_content_wrapper">

                                            <? $croped_content = crop_text(REVIEW_WORD_COUNT, $review->content) ?>

                                            <div class="all_cont review_data_cont get_cuted">
                                                <div class="content_cuted">
                                                    <? if(strlen($croped_content) < strlen($review->content)){
                                                        echo $croped_content.' ...';
                                                    }else{
                                                        echo $review->content;
                                                    }?>
                                                </div>
                                                <? if(strlen($croped_content) < strlen($review->content)){?>
                                                    <div class="content_full">
                                                        <?= $review->content;?>
                                                    </div>
                                                <?}?>
                                            </div>


                                            <div class="controls_and_time">
                                            <? if(strlen($croped_content) < strlen($review->content)){?>
                                                <p class="open_review">Развернуть</p>
                                            <?}?>
                                                <p class="time"> <?= human_date($review->time); ?></p>
                                            </div>

                                                <?if($review->answer != null){?>
                                                    <p class="view_all_review_cont"><i class="icon icon-review_all"></i>Посмотреть ответ</p>
                                                    <div class="answer_cont"><?= $review->answer;?></div>
                                                <?}?>






                                        </div>

                                    </div>


                                </div>
                            <?} ?>

                        </div>

                    </div>
                <?}?>

            </div>

        </div>

        <div class="custom_info">
            <p class="city">Город <span><?= get_city_name($user->city);?></span></p>
            <p class="age">Возраст <span><?= get_age($user->birthday);?></span></p>
        </div>

        <div class="info_panel">

            <div>
                <span>Работа по договору</span>
                <img src="<?= URL.'assets/img/icons/'.(($user->master_data->get('contract') == 0) ? 'false' : 'true').'.svg'; ?>">
            </div>

            <div>
                <span>Дает гарантию</span>
                <img src="<?= URL.'assets/img/icons/'.(($user->master_data->get('guarantee') == 0) ? 'false' : 'true').'.svg'; ?>">
            </div>

            <div>
                <span>Проверен сервисом</span>
                <img src="<?= URL.'assets/img/icons/'.(($user->master_data->get('type_ooo') != 0 || $user->master_data->get('type_ip') != 0) ? 'true' : 'false').'.svg'; ?>">
            </div>

            <div>
                <span>Заказов</span>
                <b><?= intval($user->master_data->get('finished')) ?></b>
            </div>
        </div>

        <div class="contact_fixed_panel">

            <div class="intaractiv_panel">

                <? $phone = $user->master_data->get('phone');
                if(!empty($phone)){
                    echo '<a class="button" href="tel:'.formatphone($phone).'" phone_raw="'.$phone.'">Позвонить</a>';
                } ?>

                <? if(isset($me) && $me->is_customer()){
                    echo '<a class="button start_dialog" href="'.URL.'messages/'.$user->id.'">Написать</a>';
                }?>
            </div>

            <? if(isset($me) && $me->is_customer()){?>
                <div class="user_controls">
                    <div class="work_with_master button" service_id="<?= $user->service_id; ?>"><?= $order_button_text;?></div>
                </div>
            <?} ?>

        </div>

    </div>



</div>


