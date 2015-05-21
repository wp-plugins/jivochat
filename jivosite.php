<?php
/**
 * Plugin Name: JivoChat
 * Author: JivoChat
 * Author URI: www.jivochat.com
 * Plugin URI: http://jivochat.com/
 * Description: With JivoChat you can chat with visitors on your website to increase conversion and sales 
 * Version: 1.1
 *
 * Text Domain:   jivosite
 * Domain Path:   /
 */


if (!defined('ABSPATH')) die("go away!");

load_plugin_textdomain('jivosite', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
$lang = get_bloginfo("language");
if ($lang=="ru_RU") {
	$jivo_addr = 'http://www.jivosite.ru';
} else {
	$jivo_addr = 'https://www.jivochat.com';
}

define ("JIVO_LANG", substr($lang,0,2));

define("JIVOSITE_URL","https://admin.jivosite.com");
define("JIVOSITE_WIDGET_URL","code.jivosite.com");
define("JIVO_URL",$jivo_addr);
define("JIVO_INTEGRATION_URL",JIVOSITE_URL."/integration");
define("JIVO_PLUGIN_URL",plugin_dir_url(__FILE__));
define("JIVO_IMG_URL",plugin_dir_url(__FILE__)."/img/");
// //register hooks for plugin
register_activation_hook(__FILE__, 'jivositeInstall');
register_deactivation_hook(__FILE__, 'jivositeDelete');

//add plugin to options menu
function catalog_admin_menu(){
    load_plugin_textdomain('jivosite', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
    add_menu_page(__('JivoChat','jivosite'), __('JivoChat','jivosite'), 8, basename(__FILE__), 'jivositePreferences',JIVO_IMG_URL."icon.png");
}
add_action('admin_menu', 'catalog_admin_menu');

function jivosite_options_validate($args){
    return $args;
}

/*
 * Register the settings
 */
add_action('admin_init', 'jivosite_register_settings');
function jivosite_register_settings(){
    register_setting('jivosite_token', 'jivosite_token', 'jivosite_options_validate');
    register_setting('jivosite_widget_id', 'jivosite_widget_id', 'jivosite_options_validate');
}


add_action('wp_footer', 'jivositeAppend', 100000);

function jivositeInstall(){
    return jivosite::getInstance()->install();
}

function jivositeDelete(){
    return jivosite::getInstance()->delete();
}

function jivositeAppend(){
    echo jivosite::getInstance()->append(
        jivosite::getInstance()->getId()
    );
}

function jivositePreferences(){
    if(isset($_POST["widget_id"]))
        jivosite::getInstance()->save();

    load_plugin_textdomain('jivosite', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));

    wp_register_style('jivosite_style', plugins_url('jivosite.css', __FILE__));
    wp_enqueue_style('jivosite_style');

    echo jivosite::getInstance()->render();
}

class jivosite {

    protected static $instance, $db, $table, $lang;

    private function __construct(){
        $this->token = get_option( 'jivosite_token');
        $this->widget_id = get_option( 'jivosite_widget_id');
    }
    private function __clone()    {}
    private function __wakeup()   {}

    private $widget_id = '';
    private $token = '';

    public static function getInstance() {

        if ( is_null(self::$instance) ) {
            self::$instance = new jivosite();
        }
        self::$lang     = "en";
        if(isset($_GET["lang"])){
            switch ($_GET["lang"]) {
                case 'ru':  self::$lang     = "ru"; break;
                default:    self::$lang     = "en"; break;
            }
        }
        return self::$instance;
    }

    public function setID($id){
        $this->widget_id = $id;
    }

    public function setToken($token){
        $this->token = $token;
    }

    /**
     * Install
     */
    public function install() {

        if (!$this->widget_id) {
            $default_widget_id ='';
            if (file_exists(realpath(dirname(__FILE__))."/id") ){
                $default_widget_id = file_get_contents(realpath(dirname(__FILE__))."/id");
            }
        }
        $this->widget_id = $default_widget_id;
        $this->save();
    }

    public function catchPost(){
        if(isset($_GET['mode'])&&$_GET['mode']=='reset'){
            $this->widget_id = '';
            $this->token = '';
            $this->save();
        }
        if(isset($_POST['widget_id'])){
            $this->widget_id = $_POST['widget_id'];
            $this->save();
        }elseif(isset($_POST['email'])&&isset($_POST['userPassword'])){
            // получаем данные для запроса
            $query = $_POST;
            $query['siteUrl'] = get_site_url();
            $query['partnerId'] = "wordpress";
            $authToken = md5(time().get_site_url());
            $query['authToken'] = $authToken;
            if(!$query['agent_id']){
                $query['agent_id'] = 0;
            }
			$query['lang'] = JIVO_LANG;
            $content = http_build_query($query);
			
			if(ini_get('allow_url_fopen')){
				$useCurl = false;
			}elseif(!extension_loaded('curl')) {
				if (!dl('curl.so')) {
					$useCurl = false;
				} else {
					$useCurl = true;
				}
			} else {
				$useCurl = true;
			}
            // отправляем запрос
            try{
                $path = JIVO_INTEGRATION_URL."/install";
                if(!extension_loaded('openssl')){
                    $path = str_replace('https:','http:',$path);
                }
                if($useCurl){
                    if ( $curl = curl_init() ) {
                        curl_setopt($curl, CURLOPT_URL, $path);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                        $responce = curl_exec($curl);
                        curl_close($curl);
                    }
                } else {
                    $responce = file_get_contents(
                        $path,
                        false,
                        stream_context_create(
                            array(
                                'http' => array(
                                    'method' => 'POST',
                                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                                    'content' => $content
                                )
                            )
                        )
                    );
}
                if ($responce) {
                    if(strstr($responce,'Error')){
                        return array("error"=>$responce);
                    } else {
                        $this->widget_id = $responce;
                        $this->token = $authToken;
                        $this->save();
                        return true;
                    }
                }
            } catch (Exception $e) {
                _e("Connection error",'jivosite');
            }
        }

    }

    /**
     * delete plugin
     */
    public function delete(){

    }


    public function getId(){
        return $this->widget_id;
    }

    /**
     * render admin page
     */
    public function render(){
        $result = $this->catchPost();
        $error = '';
        $widget_id = $this->widget_id;
        if (is_array($result)&&isset($result['error'])) {
            $error = $result['error'];
        }		
		
		if (ini_get('allow_url_fopen')) {
			$requirementsOk = true;
		} elseif(!extension_loaded('curl')) {
			if (!dl('curl.so')) {
				$requirementsOk = false;
			} else {
				$requirementsOk = true;
			}
		} else {
			$requirementsOk = true;
		}
		
		if ($requirementsOk) {
			require_once "templates/page.php";
		}else{
			require_once "templates/error.php";
		}
    }

    public function append($widget_id = false){
        if($widget_id)
            require_once "templates/script.php";
    }

    public function save(){
        do_settings_sections( __FILE__ );

        update_option('jivosite_widget_id',$this->widget_id);
        update_option('jivosite_token',$this->token);
    }

}