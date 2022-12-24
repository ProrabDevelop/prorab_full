<?
$user = \core\engine\DATA::get('USER');
$all_unreaded_count = (new \app\std_models\message())->get_all_unread_count($user->id);
//print_r($user);
?>
<ul class="lk_menu">
    <li>
        <a href="<?= URL?>dashboard">
            <i class="icon icon-lk"></i>
            <span><span class="xs_hide">Личный</span> кабинет</span>
        </a>
    </li>

    <li>
        <a href="<?= URL?>messages">
            <i class="icon icon-messenger"></i>
            <span>Сообщения</span><span class="counter <?= ($all_unreaded_count < 1)? 'hide': '';?> all_unread_messages_counter"><?= $all_unreaded_count;?></span>
        </a>
    </li>

    <li>
        <a href="<?= URL?>favorites">
            <i class="far fa-star"></i>
            <span>Избранное</span>
        </a>
    </li>

    <li>
        <a href="<?= URL?>history">
            <i class="far fa-clock"></i>
            <span>Заказы</span>
        </a>
    </li>

    <li class="xs_hide">
        <a class="button button_sidebar" href="<?= URL?>find">Поиск мастеров</a>
    </li>

</ul>


