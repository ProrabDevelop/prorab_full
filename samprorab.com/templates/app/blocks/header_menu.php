<?php

use core\engine\AUTH;
use core\engine\DATA;
$AUTH = \core\engine\AUTH::init();


?>
<?
if(isset($_COOKIE['city_finder'])){
    if(!isset($_COOKIE['city_finder_for_search'])){
        $city = (new \core\engine\city($_COOKIE['city_finder']));
    }else{
        $city = ORM::for_table('cityes_for_search')->find_one($_COOKIE['city_finder']);
    }
}else{
    $city = (new \core\engine\IP_to_city(getIp()))->get_city_data();
    setcookie('city_finder', $city->id, -1, '/');
}?>
<header class="main_header_nav">
    <div class="wrap">
        <div class="header_nav-mobile">
            <input id="header-menu__toggle" type="radio" />
            <label class="header-menu__btn" for="header-menu__toggle">
                <div>
                    <span></span>
                </div>
            </label>
            <div class="header_nav-mobile-menu">
                <a class="button" href="https://samprorab.com/find">Поиск мастера</a>
                <div class="mobile-menu-droplist">
                   <h3>О сервисе</h3>
                   <ul>
                    <li><a href="#">О сервисе</a></li>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Пользовательское соглашание</a></li>
                </ul>
                </div>
                <div class="mobile-menu-droplist">
                    <h3>Застройщику</h3>
               <ul>
                    <li><a href="#">Вопрос и ответ</a></li>
                    <li><a href="#">Все специальности</a></li>
                </ul>
                </div>
                <div class="mobile-menu-droplist">
                     <h3>Подрядчику</h3>
                <ul>
                    <li><a href="#">Вопрос и ответ</a></li>
                    <li><a href="#">Как пройти регистрацию</a></li>
                </ul>
                </div>
                <div class="mobile-menu-droplist">
                     <h3>Помощь</h3>
                     <ul>
                    <li><a class="get_modal" modal="modal_backcall">Написать нам</a></li>
                </ul>
                </div>
                <!----<a href="#" class="mobile-menu-master-link get_modal" modal="modal_login">Войти как мастер</a>--->
            </div>
            
            <? if($AUTH->is_auth()){?>
                    <div class="profile">
                        <div class="lk_header_dropdown_menu">
                            <div class="dropdown_title">
                                <button class="dropbtn">
                                    <a href="<?= URL?>dashboard" style="text-decoration: none; color: #252525; font-weight: bold;" onClick="return false;">
                                        <i class="icon-lk"></i>
                                        <span><?= $AUTH->user->name.' '.mb_substr($AUTH->user->surname, 0, 1).'.' ?></span>
                                    </a>
                                </button>
                            </div>
                            <div class="dropdown_content">
                                <?
                                $switch_text = 'Мастер';
                                if($AUTH->user->is_master()){
                                    $switch_text = 'Заказчик';
                                }
                                ?>
                                <a href="<?= URL?>dashboard">Личный кабинет</a>
                                <a href="<?= URL?>auth/switch">Войти как <?= $switch_text?></a>
                                <a href="<?= URL?>auth/logoutall">Выйти везде</a>
                                <a href="<?= URL?>auth/logout">Выход</a>
                            </div>
                        </div>
                    </div>
                <?}else{?>
            <a class="login get_modal" modal="modal_login">
                <i class="icon-auth"></i>
            </a>
            <div class="opacity-mobile"></div>
                <?}?>
        </div>
        <div class="header_nav">
            <a href="<?= URL?>" class="logo"></a>
            <div class="main_panel">
                <p class="description"><span>Бесплатный сервис</span>, который помогает находить<br>строительных подрядчиков за 3 простых шага</p>
                <div class="city_changer">
                    <i class="icon-marker"></i>

                        <select id="city_finder" name="city" class="select_city_finder">
                            <option><?= $city->name; ?></option>
                            <?
                            if(!empty( $user->city )){
                                echo '<option value="'.$user->city.'">'.get_city_name($user->city).'</option>';
                            }
                            ?>
                        </select>
                </div>
                <? if($AUTH->is_auth()){?>
                    <div class="profile">
                        <div class="lk_header_dropdown_menu">
                            <div class="dropdown_title">
                                <button class="dropbtn">
                                    <a href="<?= URL?>dashboard" onClick="return false;">
                                        <i class="icon-lk"></i>
                                        <span><?= $AUTH->user->name.' '.mb_substr($AUTH->user->surname, 0, 1).'.' ?></span>
                                    </a>
                                </button>
                            </div>
                            <div class="dropdown_content">
                                <?
                                $switch_text = 'Мастер';
                                if($AUTH->user->is_master()){
                                    $switch_text = 'Заказчик';
                                }
                                ?>
                                <a href="<?= URL?>dashboard">Личный кабинет</a>
                                <a href="<?= URL?>auth/switch">Войти как <?= $switch_text?></a>
                                <a href="<?= URL?>auth/logoutall">Выйти везде</a>
                                <a href="<?= URL?>auth/logout">Выход</a>
                            </div>
                        </div>
                    </div>
                <?}else{?>
                <a class="login get_modal" modal="modal_login">
                    <i class="icon-auth"></i>
                    <span>Войти</span>
                </a>
                <a class="registration button get_modal" modal="modal_reg_one">Зарегистрироваться</a>
                <?}?>
            </div>
        </div>
    </div>
</header>