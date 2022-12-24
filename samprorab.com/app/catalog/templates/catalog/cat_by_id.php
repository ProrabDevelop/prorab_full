<?php

use core\engine\DATA;




/* @var $speciality \app\std_models\speciality */
/* @var $user \app\std_models\user */
$speciality = DATA::get('speciality');
$services = DATA::get('services');
$count = DATA::get('count');

//favorites chech
$favorite = 'hide';
if(DATA::has('USER')){
    /* @var $me \app\std_models\user */
    $me = DATA::get('USER');
    $favorite = '';
}


?>


<h2 class="catalog_title catalog_title_xs">
    Каталог

    <span><?= $speciality->name_of ?>
        <b class="counter xs_only"><?=$count?></b>
    </span>
</h2>

<div class="count_and_sort_panel">
    <div class="xs_hide">
        <div class="services_count">Найдено: <?=$count?> мастеров</div>
    </div>

    <span class="sort_description">Сортировать:</span>
    <? get_block('sorter_filter');?>
</div>


<div class="service_items xs_hide">

    <?foreach ($services as $service){

        $user = $service->user;

        if($favorite != 'hide'){
            $favorite = '';
            if(in_array($user->service_id, $me->customer_data->get('favorites'))){
                $favorite = 'checked';
            }
        }
        ?>
        <div class="service_item">
            <div class="avatar_wrapper">
                <i class="favorite icon icon-star <?= $favorite?>" service_id="<?= $user->service_id; ?>"></i>
                <div class="avatar">
                    <? if(file_exists(WEB.'uploads/avatars/'.$user->id.'.png')){?>
                        <img src="<?= URL?>uploads/avatars/<?= $user->id; ?>.png">
                    <?}else{?>
                        <img src="<?= URL?>assets/img/no-photo.jpg">
                    <?}?>
                    <? if(is_online_by_date($user->last_visit, 600)){?>
                        <span class="online"></span>
                    <?}?>
                </div>

            </div>

            <div class="base_info">
                <p class="name"><?= $user->name.' '.mb_substr($user->surname, 0, 1).'.' ?></p>

                <p class="profession"><?= $speciality->name ?></p>

                <p class="status"><?= human_date_status($user->last_visit, 600)?></p>
            </div>

            <div class="second_info">

                <div class="intaractiv_panel">

                    <a class="rating_wrapper" href="<?= URL.'service/'.$user->service_id; ?>?tab=reviews">
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
                        <span class="reviews_count"><?= count($user->reviews).' '.num2word(count($user->reviews), array('отзыв', 'отзыва', 'отзывов'));?></span>

                    </a>



                    <a class="button" href="<?= URL.'service/'.$user->service_id; ?>">Связаться с мастером</a>




                </div>

                <div class="info_panel">

                    <div>
                        <p>Опыт мастера</p>
                        <span><?= EXP_TEXTS[$user->master_data->get('experience')]?></span>
                    </div>

                    <div>
                        <p>Выполненных проектов</p>
                        <span><?= intval($user->master_data->get('finished')) ?></span>
                    </div>

                    <div>
                        <p>Цена</p>
                        <span><?= intval($user->range_price); ?>₽</span>
                    </div>

                </div>

            </div>


        </div>
    <? }?>



</div>


<div class="service_items_xs xs_only">

    <?foreach ($services as $service){

        $user = $service->user;

        if($favorite != 'hide'){
            $favorite = '';
            if(in_array($user->service_id, $me->customer_data->get('favorites'))){
                $favorite = 'checked';
            }
        }
        ?>

            <div class="service_item">
                <div class="base_data">
                    <div class="avatar_wrapper">
                        <i class="favorite icon icon-star <?= $favorite?>" service_id="<?= $user->service_id; ?>"></i>

                        <a href="<?= URL.'service/'.$user->service_id; ?>">
                            <div class="avatar">
                                <? if(file_exists(WEB.'uploads/avatars/'.$user->id.'.png')){?>
                                    <img src="<?= URL?>uploads/avatars/<?= $user->id; ?>.png">
                                <?}else{?>
                                    <img src="<?= URL?>assets/img/no-photo.jpg">
                                <?}?>
                                <? if(is_online_by_date($user->last_visit, 600)){?>
                                    <span class="online"></span>
                                <?}?>
                            </div>
                        </a>

                    </div>

                    <div class="base_info">

                        <a href="<?= URL.'service/'.$user->service_id; ?>">
                            <p class="name"><?= $user->name.' '.mb_substr($user->surname, 0, 1).'.' ?></p>
                        </a>
                        <p class="profession"><?= $speciality->name ?></p>

                        <p class="status"><?= human_date_status_miminal($user->last_visit, 600)?></p>
                    </div>

                    <a class="rating_wrapper" href="<?= URL.'service/'.$user->service_id; ?>?tab=reviews">
                        <? $all_score = null;
                        if(!empty($user->reviews)) {
                            $temp_score = 0;
                            foreach ($user->reviews as $review){
                                $temp_score+= $review->score;
                            }
                            $all_score = round($temp_score / count($user->reviews), 1);
                        }
                        ?>
                        <div class="rating_one_line">
                            <span class="count"><?= ($all_score != null) ? $all_score : '0.0';?></span>
                            <div class="rating_stars">
                                <i class="icon icon-star"></i>
                            </div>
                        </div>
                        <span class="reviews_count"><?= count($user->reviews).' '.num2word(count($user->reviews), array('отзыв', 'отзыва', 'отзывов'));?></span>

                    </a>
                </div>


                <div class="info_panel">

                    <div>
                        <p>Опыт</p>
                        <span><?= EXP_TEXTS[$user->master_data->get('experience')]?></span>
                    </div>

                    <div>
                        <p>Проектов:</p>
                        <span><?= intval($user->master_data->get('finished')) ?></span>
                    </div>

                </div>


            </div>

    <? }?>

</div>



