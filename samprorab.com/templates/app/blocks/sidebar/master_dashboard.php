<?
$user = \core\engine\DATA::get('USER');
$all_unreaded_count = (new \app\std_models\message())->get_all_unread_count($user->id);
?>

<ul class="lk_menu">

    <li>
        <a href="<?= URL?>dashboard">
            <i class="icon icon-lk"></i>
            <span><span class="xs_hide">Личный</span> Кабинет</span>
        </a>
    </li>

    <li>
        <a href="<?= URL?>messages">
            <i class="icon icon-messenger"></i>
            <span>Сообщения</span><span class="counter <?= ($all_unreaded_count < 1)? 'hide': '';?> all_unread_messages_counter"><?= $all_unreaded_count;?></span>
        </a>
    </li>

    <li>
        <a href="<?= URL?>reviews">
            <i class="icon icon-reviews"></i>
            <span>Отзывы</span>
        </a>
    </li>

    <li>
        <a class="has_child">
            <i class="icon icon-settings"></i>
            <span>Настройки</span>
        </a>

        <ul><li>

        <div class="back_step_tabs" style="position:absolute; top:20px; left:20px; cursor:pointer;">
                            <p class="close_tab" onClick="$('.icon-settings').parent().click();"><i class="icon icon-left-open"></i></p>
                        </div>

        </li>
            <li><a class="child" href="<?= URL?>settings"><span>О мастере</span><i class="icon icon-right-open"></i></a></li>
            <li><a class="child" href="<?= URL?>contacts"><span>Способы связи</span><i class="icon icon-right-open"></i></a></li>
            <li><a class="child" href="<?= URL?>prices"><span>Услуги и цены</span><i class="icon icon-right-open"></i></a></li>
            <li><a class="child" href="<?= URL?>finished"><span>Выполненные работы</span><i class="icon icon-right-open"></i></a></li>
        </ul>

    </li>

</ul>

<div class="xs_menu_header">
    <p class="menu_back"><i class="icon icon-left-open"></i>Назад</p>
    <p class="name">Настройки</p>
</div>
