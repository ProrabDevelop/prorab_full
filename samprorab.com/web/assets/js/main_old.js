$(function (){

    //auth
    {

    $('.phonemask').inputmask("+7(999)999-99-99");

    $('.im_customer').click(function (){
        $('#reg_field_role').attr('value','customer');
    })

    $('.im_master').click(function (){
        $('#reg_field_role').attr('value','master');
    })

    $('.get_modal').click(function (e){
        e.preventDefault();

        $(".modal").fadeOut();

        var target = $(this).attr('modal');
        var wrap_type = $(this).attr('wrap_type');

        $('.'+target).fadeIn();

        if(wrap_type !== undefined){
            $(".modal_wrapper."+wrap_type).fadeIn();
        }else{
            $(".modal_wrapper.std").fadeIn();
        }

        return false;
    });

    $(".modal_wrapper").click(function (){
        if(!$(this).hasClass('white_bg')){
            $(".modal").fadeOut();
            $(".modal_wrapper").fadeOut();
        }
    });

    $('.modal_close').click(function (){
        $(this).parent().parent().parent().fadeOut();
        $(".modal_wrapper").fadeOut();
    });

    }

    //profile
    {

        $('.select_city').select2({
            maximumSelectionSize: 1,
            ajax: {
                url: window.URL+'cityes/list/',
                method: 'POST',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        chars: params.term
                    }
                    console.log(window.URL+'cityes/list/');
                    console.log(query);
                    return query;
                },
            },
            "language": {
                "noResults": function(){
                    return "Начните вводить город";
                },
                searching: function() {
                    return 'Поиск...';
                }
            },

        });

        $('.select_city_finder').select2({
            maximumSelectionSize: 1,
            ajax: {
                url: window.URL+'cityes/list/',
                method: 'POST',
                dataType: 'json',
                data: function (params) {
                    var query = {
                        chars: params.term
                    }
                    console.log(window.URL+'cityes/list/');
                    console.log(query);
                    return query;
                },
            },
            "language": {
                "noResults": function(){
                    return "Начните вводить город";
                },
                searching: function() {
                    return 'Поиск...';
                }
            },

        });

        $(document).on('change', '.select_city_finder', function (){
            $.cookie('city_finder', $(this).val(),{ expires: 30, path: '/' });
            $.cookie('city_finder_for_search', true,{ expires: 30, path: '/' });
            location.reload();
        });


        $('.select_experience').select2({
            minimumResultsForSearch: -1,
            dropdownCssClass : "select_experience_dropdown"
        });






        $(document).on('submit', '.update_profile', function (e){
            e.stopPropagation();
            e.preventDefault();

            var data = pack_data($(this));

            if(typeof all_my_spec !== 'undefined'){
                data['spec'] = all_my_spec;
            }

            console.log(data);

            ajax_send_base(data, 'dashboard', function (res){

                //all_my_spec = {};
                $('#edit_pass').val('');
                $('#edit_pass_confirm').val('');

                if(res.status === 'ok'){
                    create_informer('Обновление профиля','Профиль успешно обнавлен', 'success', 3000);
                }

                if(res.status === 'error'){

                    if(res.code === 135){

                        console.log(res.data);

                        Object.keys(res.data).forEach(key => {
                            $('#'+key).addClass('error');
                        });

                    }

                    create_informer('Ошибка' ,res.message, res.status, 0);
                }

            })

            return false;
        });

        $(document).on('submit', '.update_custom_data', function (e){
            e.stopPropagation();
            e.preventDefault();

            var data = pack_data($(this));

            console.log(data);

            ajax_send_base(data, 'contacts', function (data){

                if(data.status === 'ok'){
                    create_informer('Обновление профиля','Профиль успешно обнавлен', 'success', 3000);
                }

                if(data.status === 'error'){
                    create_informer('Ошибка' ,data.message, data.status, 0);
                }

            })




            return false;
        })

        $(document).on('submit', '.update_settings', function (e){
            e.stopPropagation();
            e.preventDefault();

            var data = pack_data($(this));

            console.log(data);

            ajax_send_base(data, 'settings', function (data){
                console.log(data);
                if(data.status === 'ok'){
                    create_informer('Обновление профиля','Профиль успешно обнавлен', 'success', 3000);
                }

                if(data.status === 'error'){
                    create_informer('Ошибка' ,data.message, data.status, 0);
                }

            })

            return false;
        })

        $(document).on('submit', '.save_prices_info', function (e){
            e.stopPropagation();
            e.preventDefault();

            var form = $(this);

            var data = pack_data($(this));

            console.log(data);

            ajax_send_base(data, 'prices', function (data){

                if(data.status === 'ok'){
                    create_informer('Обновление данных','Средняя цена успешно обновлена', 'success', 3000);
                }

                if(data.status === 'error'){

                    if(data.code === 135){
                        $(form).find('input[name=price]').addClass('error');
                        create_informer('Ошибка поля ввода' ,'Средняя цена должна быть числом', data.status, 3000);
                    }

                    if(data.code === 446){
                        create_informer('Критическая ошибка' ,data.message, data.status, 3000);
                    }

                }

            })

            return false;
        })


    }


    $(function () {
        $(document).on('click', '.close_informer', function () {
            var parent = $(this).parent().parent();
            parent.animate({left:'-500px'},300);

            setTimeout(function (){

                parent.animate({height:'0px', padding:'0px', margin:'0px'},300);
                setTimeout(function (){
                    parent.remove();
                }, 300);

            }, 300);

        })
    });

});


function create_informer(title, content, type, time = 0, custom_class = ''){

    var random_class_name = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
    var random_class = '.'+random_class_name;

    var notify = '<div class="notification_item '+custom_class+' '+random_class_name+'">' +
                 '    <p class="title '+type+'">' +
                 '       '+title+
                 '        <i class="close_informer icon icon-cancel"></i>' +
                 '    </p>' +
                 '    <div class="content">' +
                 '       '+content+
                 '    </div>' +
                 '</div>';

    $('.notification_wrap').append(notify);

    if(time > 0){
        setTimeout(function (){
            $(random_class).animate({left:'-500px'},300);
            setTimeout(function (){
                $(random_class).animate({height:'0px', padding:'0px', margin:'0px'},300);
                setTimeout(function (){
                    $(random_class).remove();
                }, 300);
            }, 300);
        }, time);
    }

}


function pack_data(form){

    var formdata = $(form).serializeArray();
    var data = {};
    $(formdata).each(function(index, obj){
        data[obj.name] = obj.value;
    });

    return data;

}

$(document).on('submit', '.ajax_sender', function (e){
    e.stopPropagation();
    e.preventDefault();

    var action = $(this).attr('action_fn');
    var formdata = $(this).serializeArray();
    var data = {};
    $(formdata).each(function(index, obj){
        data[obj.name] = obj.value;
    });

    window[action](data);
    return false;
})

$('.finder_select .find_field').on('input',function (){

    var chars = $.trim($(this).val());

    var selector_wrap = $(this).parent().parent().find('.find_field_dropdown');

    if(chars.length > 0){

        var data = 'chars='+$(this).val();

        ajax_send(data, 'find', function (res){
            console.log(res);
            if(res.status === 'ok'){

                if(res.data !== undefined){

                    selector_wrap.html('');
                    $.each(res.data, function (index, value){
                        selector_wrap.append('<li class="find_item" find-id="'+value.id+'">'+value.name+'</li>')
                    });

                    selector_wrap.show();
                }else{
                    selector_wrap.html('');
                    selector_wrap.hide();
                }

                //window.location.href = window.URL+'dashboard';
            }else{
                selector_wrap.html('');
                selector_wrap.hide();
            }
        });

    }else{
        selector_wrap.html('');
        selector_wrap.hide();
    }

});

$(document).on('click', '.find_item', function (){

    $(this).parent().hide();

    var text = $(this).text();
    var id = $(this).attr('find-id')

    $('.find_by_id').attr('value', id);
    $('.find_field').val(text);

    $('.finder_form').trigger('submit');

});


function open_modal(target_modal){
    $(".modal").fadeOut();

    var modal = $('.'+target_modal);
    var wrap_type = modal.attr('wrap_type');

    modal.fadeIn();

    if(wrap_type !== undefined){
        $(".modal_wrapper."+wrap_type).fadeIn();
    }else{
        $(".modal_wrapper.std").fadeIn();
    }

}

function close_all_modals(fast = false, callback = null){



    if(fast){
        $(".modal").hide();
    }else{
        $(".modal").fadeOut();
    }

    $(".modal_wrapper").fadeOut();

    if(callback !== null){
        callback();
    }

}

$(document).on('change', 'input.error', function (){
    $(this).removeClass('error');
});

function login_form(data){

    var exp = 1;
    if(data.remember_me === 'on'){
        exp = 365;
    }

    ajax_send(data, 'auth/login', function (res){

        console.log(res);

        if(res.status === 'ok'){
            $.cookie('jwt', res.data.jwt_data.jwt,{ expires: exp, path: '/' });
            $.cookie('udata', JSON.stringify(res.data.jwt_data.udata),{ expires: exp, path: '/' });

            window.location.href = window.URL+'dashboard';
        }else{

            if(res.code === 170 || res.code === 135 ){
                //const iterator = res.data.keys();

                $('#login_form_phone').addClass('error');
                $('#login_form_pass').addClass('error');

                create_informer('Ошибка авторизации',res.message, res.status, 3000);

                return false;
            }

            create_informer('Ошибка авторизации',res.message,res.status,3000);
            clear_form_add_service();
            close_all_modals();
            return false;


            $('.modal_error_text').text(res.message);
            open_modal('modal_error');
        }
    });

}

window.temp_exp = 1;

function reg_form(data){

    if(data.remember_me === 'on'){
        window.temp_exp = 365;
    }

    ajax_send(data, 'auth/registration', function (res){
        console.log(res);
        if(res.status === 'ok'){

            $('#sms_hash').attr('value', res.data['sms_hash'])

            if(res.data['sms_debug']){
                $('.sms_debug').text(res.data['sms_debug'])
            }

            open_modal('modal_confirm_code');
            return false;
        }

        if(res.code === 171){
            $('#reg_form_phone').addClass('error');
            create_informer('Ошибка регистрации',res.message, res.status, 3000);
            return false;
        }

        if(res.code === 135 ){
            var keys = Object.keys(res.data)

            for (const key of keys) {
                $('#reg_form_'+key).addClass('error');
            }
            return false;
        }


        create_informer('Ошибка регистрации',res.message,res.status,3000);
        clear_reg_form();
        close_all_modals();
        return false;
    });

}

function clear_reg_form(){
    $('#reg_form_name').val('').removeClass('error');
    $('#reg_form_surname').val('').removeClass('error');
    $('#reg_form_mail').val('').removeClass('error');
    $('#reg_form_phone').val('').removeClass('error');
    $('#reg_form_pass').val('').removeClass('error');
}

function reg_sms_confirm(data){

    ajax_send(data, 'auth/regsms', function (res){
        console.log(res);
        if(res.status === 'ok'){
            $.cookie('jwt', res.data.jwt_data.jwt,{ expires: window.temp_exp, path: '/' });
            $.cookie('udata', JSON.stringify(res.data.jwt_data.udata),{ expires: window.temp_exp, path: '/' });
            window.location.href = window.URL+'dashboard';
        }else{
            $('#sms_code').addClass('error');
        }
    });

}

function lost_pass(data){

    ajax_send(data, 'auth/lostpass', function (res){

        console.log(res);

        if(res.status === 'ok'){
            //window.location.href = window.URL+'dashboard';
            $('#sms_hash_lost_pass').attr('value', res.data['sms_hash'])

            if(res.data['sms_debug']){
                $('.sms_debug').text(res.data['sms_debug'])
            }

            open_modal('modal_confirm_code_lostpass');
            return false;
        }


        if(res.code === 171){
            $('#lostpass_form_phone').addClass('error');
            create_informer('Ошибка сброса пароля', res.message, res.status, 3000);
        }

    });

}

function lost_pass_sms_confirm(data){

    ajax_send(data, 'auth/lostsms', function (res){
        console.log(res);
        if(res.status === 'ok'){

            $.cookie('jwt', res.data.jwt_data.jwt,{ expires: 7, path: '/' });
            $.cookie('udata', JSON.stringify(res.data.jwt_data.udata),{ expires: 7, path: '/' });

            window.location.href = window.URL+'dashboard';
        }




        if(res.code === 170){
            $('#pass').addClass('error');
            $('#pass_confirm').addClass('error');
            create_informer('Ошибка сброса пароля', res.message, res.status, 3000);
            return false;
        }

        if(res.code === 135){
            $('#pass').addClass('error');
            $('#pass_confirm').addClass('error');

            var keys = Object.keys(res.data)

            for (const key of keys) {
                if(key === 'code'){
                    $('#lost_sms_code').addClass('error');
                    create_informer('Ошибка сброса пароля', 'Неверный SMS код', res.status, 3000);
                }
                if(key === 'pass'){
                    $('#pass').addClass('error');
                    $('#pass_confirm').addClass('error');
                    create_informer('Ошибка сброса пароля', 'Пароль слишком короткий, минимум 8 символов', res.status, 3000);
                }
            }
            return false;
        }

        if(res.code === 195){
            $('#lost_sms_code').addClass('error');
            create_informer('Ошибка сброса пароля', res.message, res.status, 3000);
            return false;
        }


    });

}

function ajax_send(data, path, success){

    $.ajax({
        type: 'POST',
        method : "POST",
        url: window.URL+path+'/',
        data: data,
        dataType: 'json',
        success: function (res){
            success(res);
        },
        error: function (res){
            console.log(res.responseText)
        }

    });

}

async function ajax_send_base(data, path, success){

    $.ajax({
        type: 'POST',
        method : "POST",
        url: window.URL+path+'/',
        data: data,
        dataType: 'json',
        success: function (res){
            if(success){
                success(res);
            }
        },
        error: function (res){

            console.log(res.responseText)

            create_informer('Неизвестная Ошибка','Что-то пошло не так', 'error', 3000);

        }

    });

}

//tabs

$('.select_tab').click(function (){

    var tab_name = $(this).attr('tab');
    var role = $(this).attr('role');

    $(this).parent().find('.select_tab[role="'+role+'"]').removeClass('active');
    $(this).addClass('active');

    $(this).parent().parent().find('.tab_content[role="'+role+'"]').removeClass('active');
    $(this).parent().parent().find('.tab_content[tab="'+tab_name+'"]').addClass('active');

});


$(function (){
    $(document).on('click', '.close_tab', function (){

        var tab_name = $(this).parent().parent().attr('tab');
        var role = $(this).parent().parent().attr('role');

        console.log(tab_name, role);

        $(this).parent().parent().removeClass('active');
        $(this).parent().parent().parent().parent().find('.select_tab[tab="'+tab_name+'"]').removeClass('active');

    });
});



//SPEC selector
$('.spec_select .spec_field').on('input',function (){

    var chars = $.trim($(this).val());

    var selector_wrap = $(this).parent().parent().find('.find_field_dropdown');

    if(chars.length > 0){

        var data = 'chars='+$(this).val();

        ajax_send(data, 'find', function (res){
            console.log(res);
            if(res.status === 'ok'){

                if(res.data !== undefined){

                    selector_wrap.html('');
                    $.each(res.data, function (index, value){
                        selector_wrap.append('<li class="spec_find_item" find-id="'+value.id+'">'+value.name+'</li>')
                    });

                    selector_wrap.show();
                }else{
                    selector_wrap.html('');
                    selector_wrap.hide();
                }

                //window.location.href = window.URL+'dashboard';
            }else{
                selector_wrap.html('');
                selector_wrap.hide();
            }
        });

    }else{
        selector_wrap.html('');
        selector_wrap.hide();
    }

});

$(document).on('click', '.spec_find_item', function (){

    $(this).parent().hide();

    var text = $(this).text();
    var id = $(this).attr('find-id');

    $('.add_by_id').attr('value', id);
    $('.find_field').val(text);

});

$('.add_to_my_spec').click(function (){

    var spec_id = $('.find_by_id').val();
    var spec_name = $('.find_field').val();

    var style = '';

    if(spec_id === ''){
        style = 'red';
        create_informer('Такой специализации нет', 'В течении 24 часов, мы добавим ее в список, и вы сможете ее выбрать', 'error', 12000);



        //send to create

        var data = {};
        data.user_id = window.UID;
        data.spec_name = spec_name;
        ajax_send_base(data, 'users/add_spec');

    }

    if(all_my_spec[spec_id] === undefined){
        all_my_spec[spec_id] = spec_name;
        $('.tags_spec').append('<div class="tag_item '+style+'" spec_id="'+spec_id+'">' +
            '    <span>'+spec_name+'</span>' +
            '    <i class="icon icon-cancel delete_spec_item"></i>' +
            '</div>');
    }

    close_all_modals();

});

//todo Maybe delete?
$('.add_spec').click(function (){
    var spec = $('#spec_temp').val();
    if(spec.length > 0){

        all_my_spec.push(spec);
        //$('#all_spec').attr('value', JSON.stringify(all_spec_field));
        $('#spec_temp').val('');

        $('.tags_spec').append('<div class="tag_item">' +
            '    <span>'+spec+'</span>' +
            '    <i class="icon icon-cancel delete_spec_item"></i>' +
            '</div>');

        console.log(all_my_spec);

    }
});

$(document).on('click', '.delete_spec_item', function (){

    var item = $(this).parent();

    var spec_id = $(item).attr('spec_id');
    var spec = $(this).parent().find('span').text();



    //all_my_spec = all_my_spec.filter(e => e !== spec)

    delete all_my_spec[spec_id];
    item.remove();
})

$('.view_all_review_cont').click(function (){

    var answer = $(this).parent().find('.answer_cont');
    $(this).toggleClass('view');

    if($(this).hasClass('view')){
        $(this).html('<i class="icon icon-review_all"></i>Скрыть ответ');
        answer.slideDown();
    }else{
        $(this).html('<i class="icon icon-review_all"></i>Посмотреть ответ');
        answer.slideUp();
    }

});

$(document).on('click', '.view_all_review_cont_in_lk', function (){
    var answer = $(this).parent().parent().find('.answer_cont');
    $(this).toggleClass('view');

    if($(this).hasClass('view')){
        $(this).html('<i class="icon icon-review_all"></i>Скрыть ответ');
        answer.slideDown();
    }else{
        $(this).html('<i class="icon icon-review_all"></i>Посмотреть ответ');
        answer.slideUp();
    }
})

$('.set_spec').click(function (){
    var spec_id = $(this).attr('spec')
    $('#new_service_spec').attr('value', spec_id);
    $('#new_work_spec').attr('value', spec_id);
});

$(document).on('click', '.edit_service_trigger', function (){

    var data = {};
    data.id = $(this).attr('service');

    ajax_send(data, 'service/single', function (res){
        console.log(res);

        if(res.status === 'ok'){
            //res.data
            console.log(res.data);

            $('#edit_service_id').val(res.data.id);
            $('#edit_service_name').val(res.data.name);
            $('#edit_amount').val(parseInt(res.data.amount));

            $('input:radio[name="payment_type"][role="edit"]').attr('checked', false).filter('[value='+res.data.payment_type+']').attr('checked', true);
            $('input:radio[name="amount_type"][role="edit"]').attr('checked', false).filter('[value='+res.data.amount_type+']').attr('checked', true);

            open_modal('modal_edit_service');

        }else{
            close_all_modals();
        }


    });

})


function add_service(data){

    console.log(data);

    ajax_send(data, 'service/add', function (res){
        if(res.status === 'ok'){

            console.log(res);

            create_informer('Добавление услуги','Услуга успешно добавлена','success',3000);
            clear_form_add_service();
            close_all_modals();

            $('.price_table').append(`<div class="price_item" price_item="${res.data.id}">
                                <span class="name">${res.data.name}</span>
                                <span class="price">${res.data.correct_price}</span>
                                <span class="edit_service edit_service_trigger" service="${res.data.id}"><i class="icon icon-edit"></i></span>
                            </div>`)


            return false;
        }



        if(res.status === 'error'){

            if(res.code === 135){
                //const iterator = res.data.keys();

                var keys = Object.keys(res.data)

                for (const key of keys) {
                    $('.modal_add_service [name='+key+']').addClass('error');
                }
                return false;
            }

            create_informer('Ошибка Добавление услуги',res.message,res.status,3000);
            clear_form_add_service();
            close_all_modals();


            //$()


            return false;
        }
    });

}

function clear_form_add_service(){
    $('#service_name').val('').removeClass('error');
    $('#amount').val('').removeClass('error');
    $('label[for=pt1]').trigger('click');
    $('label[for=at1]').trigger('click');
}

function clear_form_edit_service(){
    $('#edit_service_name').val('').removeClass('error');
    $('#edit_amount').val('').removeClass('error');
    $('label[for=edit_pt1]').trigger('click');
    $('label[for=edit_at1]').trigger('click');
}

function edit_service(data){

    console.log(data);

    ajax_send(data, 'service/edit', function (res){
        console.log(res);
        if(res.status === 'ok'){

            var price_item = $('[price_item='+data.id+']');

            price_item.find('.name').text(data.name);
            price_item.find('.price').text(res.data.amount_text);
            close_all_modals();
            clear_form_edit_service();
        }

        if(res.status === 'error'){

            if(res.code === 135){
                //const iterator = res.data.keys();

                var keys = Object.keys(res.data)

                for (const key of keys) {
                    $('.modal_edit_service [name='+key+']').addClass('error');
                }
                return false;
            }

            create_informer('Ошибка Добавление услуги',res.message,res.status,3000);
            clear_form_edit_service();
            close_all_modals();
            return false;
        }




    });

}

///////////////////////////////

var work_medias = [];

$(document).on('click', '.add_new_work', function (){
    work_medias = [];
    open_modal($(this).attr('modal'));
});





function add_work(data) {

    data.medias = work_medias;

    if(data.medias.length === 0){
        data.medias = {};
    }

    ajax_send(data, 'finished', function (res){
        console.log(res);
        if(res.status === 'ok'){

            var image = window.URL+'uploads/std/no-photo-160x130.png';

            if(data.medias.length !== undefined){
                image = window.URL+'uploads/works/'+work_medias[0].name+'-160x130.png';
            }
            $('.tab_content.active[role="work_tabs"] .work_items').prepend('<div class="work_item">\n' +
                '                                <div class="img">\n' +
                '                                    <img src="'+image+'">\n' +
                '                                </div>\n' +
                '                                <div class="information">\n' +
                '                                    <div class="title_wrap">\n' +
                '                                        <span class="title">'+res.data.name+'</span>\n' +
                '                                        <span class="price">'+res.data.price+' ₽</span>\n' +
                '                                    </div>\n' +
                '                                    <div class="content">'+res.data.content+'</div>\n' +
                '                                </div>\n' +
                '                            </div>');

            close_all_modals(true, function (){
                work_medias = [];
                $('.gallery_upload_wrapper').find('.uploaded_item').remove()

                $('#work_name').val('');
                $('#work_description').val('');
                $('#work_price').val('');
            });



        }else{
            //$('.modal_error_text').text(res.message);
            //open_modal('modal_error');
        }
    });

}

function edit_work(data) {
    console.log(data);
    data.medias = work_medias;

    console.log('EDIT');
    console.log(work_medias);

    console.log(data.medias);

    if(data.medias.length === 0){
        data.medias = {};
    }


    ajax_send(data, 'works/'+data.id+'/save', function (res){
        console.log('------------------');
        console.log(res);

        if(res.status === 'ok'){

            var image = window.URL+'uploads/std/no-photo-160x130.png';

            if(data.medias.length !== undefined){
                image = window.URL+'uploads/works/'+work_medias[0].name+'-160x130.png';
            }


            $('.tab_content.active[role="work_tabs"] .work_items').find('.work_item[work_id='+data.id+']').html('<div class="img">' +
                '                                    <img src="'+image+'">' +
                '                                </div>' +
                '                                <div class="information">' +
                '                                    <div class="title_wrap">' +
                '                                        <span class="title">'+res.data.name+'</span>' +
                '                                        <span class="price">'+res.data.price+' ₽</span>' +
                '                                    </div>' +
                '                                    <div class="content">'+res.data.content+'</div>' +
                '                                </div>');


            close_all_modals(true, function (){
                work_medias = [];
                $('.gallery_upload_wrapper').find('.uploaded_item').remove()

                $('#work_name').val('');
                $('#work_description').val('');
                $('#work_price').val('');
            });



        }else{
            //$('.modal_error_text').text(res.message);
            //open_modal('modal_error');
        }
    });





}



$(document).on('click', '.add_work_image', function (){
    $('#media_upload_path').attr('value', 'works');
    $('#media_upload_sizes').attr('value', ['600x488', '160x130', '92x92']);
    $('#media_upload_cb').attr('value', 'work_uploaded_success');
    $('#media_upload_field').trigger('click');
});

function work_uploaded_success(data){
    console.log(data);
    //work_medias.push(data);

    var index = work_medias.push(data) - 1;


    //$('.tab_content.active[role="work_tabs"]')

    $('.add_work_image').before(
        '<div class="uploaded_item"><img src="'+window.URL+'uploads/'+data.path+data.name+'-92x92.'+data.type+'">' +
        '<span class="work_delete_img" index="'+index+'"><i class="icon icon-add"></i></span></div>'
    );

}


function add_old_uploaded_images(data){

    var index = work_medias.push(data) - 1;

    $('.modal_edit_work .add_work_image').before(
        '<div class="uploaded_item"><img src="'+window.URL+'uploads/'+medias.path+data.name+'-92x92.'+data.type+'">' +
        '<span class="work_delete_img" index="'+index+'"><i class="icon icon-add"></i></span></div>'
    );

}
$(document).on('click', '.work_delete_img', function (){
    var index = $(this).attr('index');
    work_medias.splice(index, 1);
    $(this).parent().remove();
});

$(document).on('click', '.add_doc', function (){

    $('#doc_upload_type').attr('value', $(this).attr('doc_type'));

    $('#doc_upload_path').attr('value', 'docs');
    $('#doc_upload_cb').attr('value', 