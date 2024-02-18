<?php
/**
 * ID: light_theme_admin
 * Name: Light theme Admin
 * Description: Tema personalizzato per l'area di amministrazione
 * Icon: dashicons-admin-customizer
 * Version: 1.0
 * 
 */
require plugin_dir_path( __FILE__ ) ."Color.php";
use Mexitek\PHPColors\Color;
class bc_theme_admin {
	private $color_theme_options;
    private $primary_color;
    private $background_color;
    private $menu_color;
    private $menu_text_color;
    private $notification_color;


	public function __construct(){
        
		$this->color_theme_options = get_option( 'bc_theme_admin_options' ); 
        $this->primary_color = ( ! empty( $this->color_theme_options['primary'] ) ) ? "#{$this->color_theme_options['primary']}" : '#007bd3';
        $this->background_color = ( ! empty( $this->color_theme_options['background'] ) ) ? "#{$this->color_theme_options['background']}" : '#f2f4f8';
        $this->menu_color = ( ! empty( $this->color_theme_options['menu'] ) ) ? "#{$this->color_theme_options['menu']}" : '#ffffff';
        $this->menu_text_color = ( ! empty( $this->color_theme_options['menu_text'] ) ) ? "#{$this->color_theme_options['menu_text']}" : '#3b4b5d';
        $this->notification_color = ( ! empty( $this->color_theme_options['notification'] ) ) ? "#{$this->color_theme_options['notification']}" : '#48ad10';

        add_action( 'admin_enqueue_scripts', array( $this, 'light_theme_assets'),0 );
        add_action( 'admin_init', array($this, 'b_admin_admin_color_scheme'),2);
        add_filter( 'get_user_option_admin_color', array($this, 'update_user_option_admin_color') );
        add_action( 'admin_print_footer_scripts', array($this, 'margin_top_menu' ));
        add_action( 'admin_menu', array( $this, 'bc_theme_admin_settings_add_plugin_page' ) );
        add_action('admin_menu', array( $this, '_admin_userbox' ), 99);
		add_action( 'admin_init', array( $this, 'bc_theme_admin_settings_page_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'bc_theme_admin_load_scripts_admin' ));
        
        global $pagenow;
        if($pagenow=='admin.php' && $_GET['page']=='light_theme_admin'){
            add_action( 'admin_print_footer_scripts', array($this, 'init_wp_color' ), 20 );
        }
        
    }

    public function bc_theme_admin_settings_add_plugin_page() {
        

		add_submenu_page(
            'bweb-component',
			'Theme Admin', // page_title
			'Theme Admin', // menu_title
			'manage_options', // capability
			'light_theme_admin', // menu_slug
			array( $this, 'bc_theme_admin_settings_create_admin_page' ) // function
		);

	}

    public function bc_theme_admin_settings_create_admin_page() {
		
        ?>

		<div class="wrap">
			<h2 class="wp-heading-inline">Theme Admin</h2>
			<p></p>
			<?php settings_errors(); ?>

			<div class="upload">
                
                <form method="post" action="options.php">
                    <?php
                    
					settings_fields( 'bc_theme_admin_options_group' );
					do_settings_sections( 'bc_theme_admin-settings-admin' );
					submit_button();
				    ?>
                </form>
                
            </div>
		</div>
	<?php }


    public function bc_theme_admin_settings_page_init() {
        register_setting(
			'bc_theme_admin_options_group', // option_group
			'bc_theme_admin_options', // option_name
			array( $this, 'bc_theme_admin_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'bc_theme_admin_settings_setting_section', // id
			'', // title
			function(){}, // callback
			'bc_theme_admin-settings-admin' // page
		);

		add_settings_field(
			'color_theme', // id
			'Color', // title
			array( $this, 'color_theme_callback' ), // callback
			'bc_theme_admin-settings-admin', // page
			'bc_theme_admin_settings_setting_section' // section
		);

    }
    public function bc_theme_admin_settings_sanitize($input){
        $sanitary_values = array();
        if ( isset( $input['primary'] ) ) {
            $sanitary_values['primary'] = sanitize_hex_color_no_hash($input['primary'] );
        }
        if ( isset( $input['background'] ) ) {
            $sanitary_values['background'] = sanitize_hex_color_no_hash($input['background'] );
        }
        if ( isset( $input['menu'] ) ) {
            $sanitary_values['menu'] = sanitize_hex_color_no_hash($input['menu'] );
        }
        if ( isset( $input['menu_text'] ) ) {
            $sanitary_values['menu_text'] = sanitize_hex_color_no_hash($input['menu_text'] );
        }
        if ( isset( $input['notification'] ) ) {
            $sanitary_values['notification'] = sanitize_hex_color_no_hash($input['notification'] );
        }
        return $sanitary_values;
    }
    public function color_theme_callback(){
        ?>
        <table>
            <tr>
                <td><label for="primary_color">Primary:</label></td>
                <td>
                    <input name="bc_theme_admin_options[primary]" value="<?php echo $this->primary_color; ?>" class="colorpicker" id="primary_color" />
                </td>
            </tr>
            <tr>
                <td><label for="background_color">Background:</label></td>
                <td>
                    <input name="bc_theme_admin_options[background]" value="<?php echo $this->background_color; ?>" class="colorpicker" id="background_color" />
                </td>
            </tr>
            <tr>
                <td><label for="menu_color">Menu:</label></td>
                <td>
                    <input name="bc_theme_admin_options[menu]" value="<?php echo $this->menu_color; ?>" class="colorpicker" id="menu_color" />
                </td>
            </tr>
            <!--<tr>
                <td><label for="menu_color">Menu Text:</label></td>
                <td>
                    <input name="bc_theme_admin_options[menu_text]" value="<?php echo $this->menu_text_color; ?>" class="colorpicker" id="menu_text_color" />
                </td>
            </tr>-->
            <tr>
                <td><label for="menu_color">Notification:</label></td>
                <td>
                    <input name="bc_theme_admin_options[notification]" value="<?php echo $this->notification_color; ?>" class="colorpicker" id="notification_color" />
                </td>
            </tr>
        </table>
            
        <?php
    }

    public function b_admin_admin_color_scheme() {  
        //b_admin
        wp_admin_css_color( 'b_admin', __( 'b_admin' ),
        plugin_dir_url( __file__ ) . 'b_admin.css',
        array( $this->menu_color, $this->menu_text_color, $this->notification_color , $this->primary_color ),//#3791d3
        array( 'base' => $this->primary_color, 'focus' => '#fff', 'current' => '#fff' )
        );
    }


    public function update_user_option_admin_color( $color_scheme ) {
        $color_scheme = 'b_admin';

        return $color_scheme;
    }

    public function margin_top_menu() {
    ?>
        <script type="text/javascript">
            //jQuery('#adminmenu').css('margin-top',jQuery('#wpadminbar').height()+30)
            
            jQuery('#adminmenu a.adminmenu-container').click(function (e) { 
                e.preventDefault();
            });
            jQuery('.show_admin_bar').click(function () { 
                jQuery('#wpadminbar').toggleClass('open');
                jQuery('.show_admin_bar').toggleClass('dashicons-arrow-right-alt2 dashicons-no-alt');
            });
        </script>
    <?php
    }
    public function init_wp_color() {
        ?>
            <script type="text/javascript">
                jQuery( '#primary_color' ).wpColorPicker({
                    defaultColor: '#007bd3'
                });
                jQuery( '#background_color' ).wpColorPicker({
                    defaultColor: '#f2f4f8'
                });
                jQuery( '#menu_color' ).wpColorPicker({
                    defaultColor: '#ffffff'
                });
                jQuery( '#menu_text_color' ).wpColorPicker({
                    defaultColor: '#3b4b5d'
                });
                jQuery( '#notification_color' ).wpColorPicker({
                    defaultColor: '#48ad10'
                });
            </script>
        <?php
    }

    public function bc_theme_admin_load_scripts_admin($hook){
        if($hook == 'bweb-component_page_light_theme_admin'){
            // Colorpicker Scripts
            wp_enqueue_script( 'wp-color-picker' );

            // Colorpicker Styles
            wp_enqueue_style( 'wp-color-picker' );

            wp_enqueue_style( 'settings-css', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ).'light_theme_admin/settings.css');
        }
        
    }
    public function light_theme_assets() {
        $obj_primary = new Color($this->primary_color);
        $primary_hover = '#'.$obj_primary->darken();

        $obj_menu = new Color($this->menu_color);
        $color_submenu = '#'.$obj_menu->darken(3);
        $menu_text = '#'.$obj_menu->lighten(100);
        if($obj_menu->isLight()) $menu_text = '#'.$obj_menu->darken(100);

        $rgb_menu_shadow = $obj_primary->getRgb();
        $menu_shadow = 'rgba('.$rgb_menu_shadow['R'].','.$rgb_menu_shadow['G'].','.$rgb_menu_shadow['B'].',0.2)';

        $obj_submenu_text = new Color($color_submenu);
        $submenu_text = '#'.$obj_submenu_text->lighten(100);
        if($obj_submenu_text->isLight()) $submenu_text = '#'.$obj_submenu_text->darken(100);
        
        $obj_background = new Color($this->background_color);
        $text = '#fff';
        if($obj_background->isLight()) $text = '#3c434a';

        wp_register_style( 'light_theme_css_root', false );
        wp_enqueue_style( 'light_theme_css_root' );
        wp_add_inline_style( 'light_theme_css_root', ':root {
            --text: '.$text.';
            --primary: '.$this->primary_color.';
            --primary_hover: '.$primary_hover.';
            --background: '.$this->background_color.';
            --menu: '.$this->menu_color.';
            --menu_text: '.$menu_text.';
            --menu_shadow: '.$menu_shadow.';
            --submenu: '.$color_submenu.';
            --submenu_text: '.$submenu_text.';
            --notification: '.$this->notification_color.';
            --svg_chk: url("data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M14.83%204.89l1.34.94-5.81%208.38H9.02L5.78%209.67l1.34-1.25%202.57%202.4z%27%20fill%3D%27%23'.sanitize_hex_color_no_hash($this->primary_color).'%27%2F%3E%3C%2Fsvg%3E");
          }' );
    }

    public function _admin_userbox() {

		global $menu, $user_id, $scheme;

        if(!wp_is_mobile()):
            
            $current_user = wp_get_current_user();
            $user_name = $current_user->display_name ;
            //$user_avatar = get_avatar( $current_user->user_email, 74, '', '', array('scheme' => 'https') );

            $user_avatar = '<img alt="" src="'.esc_url( get_avatar_url( 9999999999 ) ).'" class="avatar avatar-74 photo" height="74" width="74" loading="lazy">';
            if( !empty(get_site_icon_url())){
            $user_avatar = '<img alt="" src="'.get_site_icon_url(74).'" class="avatar avatar-74 photo" height="74" width="74" loading="lazy">';
            }
            
            $html = '<div><span class="dashicons dashicons-arrow-right-alt2 show_admin_bar"></span><br><a href="'.get_home_url().'"><span class="dashicons dashicons-admin-home"></span>Visita il sito</a></div>';
            
            $html .= '<div class="adminmenu-avatar">' . $user_avatar . '</div><div class="adminmenu-user-name"><span>Ciao, ' . esc_html__( $user_name ) . '</span></div><br>';
            
            $menu[0] = array( $html, 'read', '#', 'user-box', 'adminmenu-container');
            
        endif;

	}
}

new bc_theme_admin();


	


