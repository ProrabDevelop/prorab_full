
<div class="lp_main">
    <div class="wrap">
      <div class="main_header">

         <div class="text_box">
             <h1 class="title"><span>БЕЗ ПОСРЕДНИКОВ</span> подбери надежных мастеров<br>
                 для строительства и ремонта</h1>




             <p class="description">Без посредников и сэкономьте на строительстве до <span>40%</span></p>

             <div class="header_buttons">

                 <a class="button" href="<?= URL?>find">Найти мастера</a>

                 <? if(!\core\engine\AUTH::init()->is_auth()){?>
                     <a class="button_white get_modal im_master" modal="modal_reg_two">Стать подрядчиком <i class="icon icon-right-open"></i></a>
                 <?}?>

             </div>

         </div>

          <div class="img_box">
              <img src="<?= URL?>assets/img/lp/main_header.png">
          </div>

      </div>
    </div>


</div>