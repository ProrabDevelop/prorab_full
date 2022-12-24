<?
use \core\engine\DATA;

/* @var $user \app\std_models\user */
$users = DATA::get('users');

/* @var $me \app\std_models\user */
$me = DATA::get('USER');

?>


<div class="history_wrapper">

    <? if(!empty($users)){?>

    <h2 class="xs_hide">Всего заказов <? if(!empty($users)){?><span class="counter"><?= count($users);?></span><?}?></h2>

    <table class="history_table xs_hide">
        <thead>
            <tr class="history_head">
                <th>Аватар</th>
                <th class="sort_name">ФИО мастера</th>
                <th class="sort_spec">Специализация</th>
                <th class="sort_date" data-sorter="shortDate" data-date-format="ddmmyyyy">Дата</th>
                <th>Опции</th>
                <th></th>
                <th></th>
            </tr>
        </thead>

        <tbody class="history_tbody">
        <? foreach ($users as $user){?>
            <tr class="history_item" service_id="<?= $user->service_id; ?>">
                <td class="avatar_td">
                    <img class="avatar" src="<?= get_avatar($user->id)?>">
                    <? if(is_online_by_date($user->last_visit, 600)){?>
                        <span class="online"></span>
                    <?}?>
                </td>

                <td class="name"><?= $user->name.' '.mb_substr($user->surname, 0, 1).'.' ?></td>
                <td><?= $user->speciality->name ?></td>
                <td class="date"><?= $user->history_date ?></td>
                <td class="history_controls_wrapper">
                    <div class="history_controls">
                        <?
                        $time = (new DateTime($user->history_date))->add(new DateInterval('P1D'));
                        if($time <= new DateTime()){?>
                            <a class="add_review_get_modal" user_id="<?= $user->id; ?>" spec_id="<?= $user->speciality->id; ?>" service_id="<?= $user->service_id; ?>"><i class="icon icon-reviews"></i>Оставить отзыв</a>
                        <?} ?>

                       <a href="<?= URL?>messages/<?= $user->id?>"><i class="icon icon-messenger"></i>Посмотреть диалог</a>
                    </div>
                </td>
                <td class="button_wrap"><a class="button" href="<?= URL?>service/<?= $user->service_id; ?>">Связаться с мастером</a></td>
                <td class="delete_wrap"><i class="icon icon-delete delete_history" service_id="<?= $user->service_id; ?>"></i></td>
            </tr>
        <? } ?>
        </tbody>
    </table>

        <script>
            $(function() {
                $(".history_table").tablesorter({

                    theme : 'blue',

                    dateFormat : "mmddyyyy", // set the default date format

                    headers: {
                        0: { sorter: false },
                        1: { sorter: true},
                        2: { sorter: true},
                        3: { sorter: "shortDate" },
                        4: { sorter: false},
                        5: { sorter: false},
                        6: { sorter: false},
                    }
                });
            });

        </script>

        <div class="header_nav_steps_xs_orders xs_only">
            <div class="page_title">
                <span>Заказы</span>
            </div>
        </div>

        <div class="history_items_xs xs_only">
            <? foreach ($users as $user){?>
                <div class="history_item" service_id="<?= $user->service_id; ?>">

                    <div class="base_info">
                        <div class="avatar_wrapper">
                            <div class="avatar">
                                <img class="avatar" src="<?= get_avatar($user->id)?>">
                                <? if(is_online_by_date($user->last_visit, 600)){?>
                                    <span class="online"></span>
                                <?}?>
                            </div>
                        </div>

                        <div class="info">
                            <p class="name"><?= $user->name.' '.mb_substr($user->surname, 0, 1).'.' ?></p>
                            <p class="profession"><?= $user->speciality->name ?></p>
                            <p class="date">Дата: <span><?= $user->history_date ?></span></p>
                        </div>


                        <div class="delete_wrap">
                            <i class="icon icon-delete delete_history" service_id="<?= $user->service_id; ?>"></i>
                        </div>
                    </div>

                    <div class="history_controls_wrapper">
                        <div class="history_controls">
                            <?
                            $time = (new DateTime($user->history_date))->add(new DateInterval('P1D'));
                            if($time <= new DateTime()){?>
                                <a class="add_review_get_modal" user_id="<?= $user->id; ?>" spec_id="<?= $user->speciality->id; ?>" service_id="<?= $user->service_id; ?>"><i class="icon icon-reviews"></i>Оставить отзыв</a>
                            <?} ?>

                            <a class="dialog_item" href="<?= URL?>messages/<?= $user->id?>"><i class="icon icon-messenger"></i>Посмотреть диалог</a>
                        </div>
                    </div>

                    <div class="button_wrap">
                        <a class="button" href="<?= URL?>service/<?= $user->service_id; ?>">Связаться с мастером</a>
                    </div>

                </div>
            <? } ?>
        </div>

    <?}else{?>
        <h2>Список заказов пуст</h2>
    <? } ?>
</div>


