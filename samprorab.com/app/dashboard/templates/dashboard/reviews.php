<?
use app\std_models\reviews_list;
use \core\engine\DATA;

$user = DATA::get('USER');
$user->reviews = (new reviews_list())->get_all($user->id);
/* @var $review  \app\std_models\review */
?>

<? if(!$user->reviews){?>
    <h2>Нет отзывов</h2>
    <p>После выполнения работы, не забывайте просить заказчиков, оставить отзыв о вашей работе.</p>
<?}?>



<div class="reviews_wrapper reviews_wrapper_xs">
    <div class="one_line_review">
        <? foreach ($user->reviews as $review){?>
            <div class="review_item" review_item="<?= $review->id?>">

                <div class="avatar_wrapper">
                    <img class="avatar" src="<?= get_avatar($review->user_id);?>">
                </div>

                <div class="cont_wrapper">

                    <div class="title_wrap">
                        <p class="name"> <?= $review->reviewer_name; ?></p>
                        <p class="time xs_hide"> <?= human_date($review->time); ?></p>
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

                        <div class="controls_and_time">
                            <? if(strlen($croped_content) < strlen($review->content)){?>
                                <p class="open_review">Развернуть</p>
                            <?}?>
                            <p class="time xs_only"> <?= human_date($review->time); ?></p>
                        </div>

                        <div class="review_controls">

                            <?if($review->complaint != null){?>
                                <span class="complain_info"><i class="icon icon-warning"></i>Жалоба оставлена</span>
                            <?}else{?>
                                <?if($review->answer != null){?>
                                    <p class="view_all_review_cont_in_lk"><i class="icon icon-review_all"></i>Посмотреть ответ</p>
                                <?}else{?>
                                    <a class="review_answer" review_id="<?= $review->id?>"><i class="icon icon-tg"></i>Ответить</a>
                                <?}?>
                                <a class="review_complain" review_id="<?= $review->id?>"><i class="icon icon-warning"></i>Пожаловаться</a>
                            <?}?>


                        </div>

                        <div class="answer_cont"><?= $review->answer;?></div>


                    </div>

                </div>


            </div>
        <?} ?>
    </div>
</div>


