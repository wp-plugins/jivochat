<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" type="text/javascript"></script>
<div class="wrap">
    <h1>
        <a href="http://www.jivosite.com" target="_blank">
            <img src="<? echo JIVO_PLUGIN_URL; ?>img/<? _e('logo.png','jivosite');?>" />
        </a>
    </h1>
    <b><?php echo $error; ?></b>
    <?php if(!$this->widget_id){ ?>
        <p style="margin-bottom: 0px;"><?php _e('To install JivoChat, please create a new account, or use your existing one','jivosite'); ?></p>
        <p class="gray" style="margin-top: 0px;"><?php echo str_replace('%JIVO_URL%',JIVO_URL,__('If you need help, please chat with us on <a href="%JIVO_URL%">jivochat.com</a> or use <a href="%JIVO_URL%/support">our forum</a>','jivosite')); ?></p>

        <div class="gray_form">
            <form method="POST">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="email"><?php _e('Your Email (login name)','jivosite'); ?></label>
                            </th>
                            <td class="input">
                                <input id="email" class="regular-text" type="text" value="" name="email">
                            </td>
                            <td class="gray"><div><?php _e('Please specify the email you will use to login to the agent’s app and admin panel. If you already have a JivoChat account, please enter the email address you use for it here.','jivosite'); ?></div></td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="userPassword"><?php _e('JivoChat Password','jivosite'); ?></label>
                            </th>
                            <td class="input">
                                <input id="userPassword" class="regular-text" type="password" value="" name="userPassword">
                            </td>
                            <td class="gray"><div><?php _e('Please create a new JivoChat account password. If you already have an account, please enter the password for it here.','jivosite'); ?></div></td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="userDisplayName"><?php _e('Agent Name','jivosite'); ?></label>
                            </th>
                            <td class="input">
                                <input id="userDisplayName" class="regular-text" type="text" value="" name="userDisplayName">
                            </td>
                            <td class="gray"><div><?php _e('The agent name that will be displayed to website visitors in the JivoChat chat window.','jivosite'); ?></div></td>
                        </tr>
                        <tr>
                            <td colspan="3"><input class="button button-primary" type="submit" value="<?php _e('Install JivoChat Now','jivosite'); ?>"></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    <?php }else{
    ?>
        <div class="success">
            <?php _e('Congratulations! You have successfully installed JivoChat on your website. Now you need to install the agent’s app on your computer and customize the chat window in the admin panel.','jivosite'); ?>
        </div>
        <div class="gray_form">
            <h3>1. <?php _e('Install Agent’s App','jivosite'); ?></h3>
            <a class="button button-primary" href="http://<?php _e('www.jivochat.com','jivosite'); ?>/apps" target="_blank"><?php _e('Install Agent’s App','jivosite'); ?></a>
            <h3>2. <?php _e('Customize Settings and add Agents in the Admin Panel','jivosite'); ?></h3>
            <p><?php _e('After you have installed the agent’s app, please login to the admin panel to add more agents’ accounts, customize the chat window settings and set up proactive invitations to get the most from your new live chat!','jivosite'); ?></p>
            <a  class="button button-primary"  href='<?php if($this->token){ echo JIVO_INTEGRATION_URL.'/login?token='.$this->token.(JIVO_LANG!='ru'?"&lang=".JIVO_LANG:''); }else{echo JIVOSITE_URL;} ?>' target="_blank"><?php _e('Go to JivoChat Admin Panel','jivosite'); ?></a>
            <p><a href="?<?php echo http_build_query($_GET) ?>&mode=reset"><?php _e('Reset account info','jivosite'); ?></a></p>
        </div>
        <p class="gray"><?php echo str_replace('%JIVO_URL%',JIVO_URL,__('If you need help, please chat with us on <a href="%JIVO_URL%">jivochat.com</a> or use <a href="%JIVO_URL%/support">our forum</a>','jivosite')); ?></p>
    <?php } ?>
</div>