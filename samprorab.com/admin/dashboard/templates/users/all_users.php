<h2>Пользователи</h2>

<?
$users = \core\engine\DATA::get('users');
if($users):?>

    <table class="table table-hover" style="table-layout: fixed;">
        <thead>
        <tr>
            <th>id</th>
            <th>Имя</th>
            <th>Телефон</th>
            <th>E-mail</th>
            <th>Документы</th>
            <th>Одобрить</th>
            <th>Редактирование</th>
        </tr>
        </thead>

        <tbody>

        <?
        /* @var $user \app\std_models\user */
        foreach ($users as $user){?>

            <tr>
                <td><?= $user->id ?></td>
                <td><a><?= $user->name ?> <?= $user->surname ?></a></td>
                <td><?= formatphone($user->phone) ?></td>
                <td><?= $user->mail ?></td>

                <td><?
                    $docs = [];

                    $docs_ip = (new \core\engine\media_list($user->master_data->get('docs_ip')))->get_all();
                    $docs_ooo = (new \core\engine\media_list($user->master_data->get('docs_ooo')))->get_all();

                    $docs = array_merge($docs_ip, $docs_ooo);
                    /* @var $media \core\engine\media */
                    ?>
                    <ul class="list-group">
                        <? foreach ($docs as $key => $media){?>
                            <li class="list-group-item"><a target="_blank" href="<? echo $media->get_url('default');?>">Документ №<?= $key+1?></a></li>
                        <?}?>
                    </ul>
                </td>
                <td>
                    <?
                    $ip_selected = '';
                    $ooo_selected = '';

                    if($user->master_data->get('type_ip')){
                        $ip_selected = 'checked';
                    }

                    if($user->master_data->get('type_ooo')){
                        $ooo_selected = 'checked';
                    }

                    ?>

                    <form class="" method="post">
                        <input type="hidden" name="user_id" value="<?=$user->id;?>">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="type_ip" id="ip_checkbox_<?=$user->id;?>" <?= $ip_selected?>>
                            <label class="form-check-label" for="ip_checkbox_<?=$user->id;?>">
                                Проверен ИП
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="type_ooo" id="ooo_checkbox_<?=$user->id;?>" <?= $ooo_selected?>>
                            <label class="form-check-label" for="ooo_checkbox_<?=$user->id;?>">
                                Проверен ООО
                            </label>
                        </div>
                        <button class="btn btn-success btn-sm">Сохранить</button>
                    </form>
                </td>


                <td>
                    <a href="<?= URL?>user/<?= $user->id ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Войти под пользователем</a>
                    <!--<a href="#" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>-->
                </td>
            </tr>
        <?}?>
    </table>

    <?php
    \Core\Engine\pagination::view();
endif;
?>