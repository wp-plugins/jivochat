<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" type="text/javascript"></script>
<div class="wrap">
    <h1>
        <a href="http://www.jivosite.com" target="_blank">
            <img src="<? echo JIVO_PLUGIN_URL; ?>img/<? _e('logo.png','jivosite');?>" />
        </a>
    </h1>
    <b><?php echo $error; ?></b>
        <div class="gray_form">
			<?php _e('Unfortunately, your server configuration does not allow the plugin to connect to JivoChat servers to create account. Please, go to <a target="_blank" href="https://admin.jivosite.com/autoreg?lang=en">https://admin.jivosite.com/autoreg?lang=en</a> and sign up. During the signup process you will be offered to download another Wordpress module that does not require to communicate over the network','jivosite'); ?>
        </div>
</div>