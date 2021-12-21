<?php 
/*
  Plugin Name: Reading Time Plugin
  Description: Reading time plugin
  Version: 1.0
  Author: Nadar Rosano
  Author URI: https://nosite.org
  Text Domain: readdomin
  Domain Path: /languages
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/**
* enqueue css stylesheet for admin.
*/
function enqueue_custom_style() {
  wp_register_style( 'custom_wp_css', plugin_dir_url( __FILE__ ) . 
  './css/style.css', false, '1.0.0' );

  wp_enqueue_style( 'custom_wp_css' );
}
add_action( 'admin_print_styles', 'enqueue_custom_style' ); 
/**
* shortcode
*/
include('init.php');

//Creating class readingTimePlugin
class readingTimePlugin {

  function __construct() {
    add_action('admin_menu', array($this, 'admin_page'));
    add_action('admin_init', array($this, 'setting'));
    add_filter('the_content', array($this, 'show_word_count'));
    add_action('init', array($this, 'languages'));
  }
  /**
  * languages
  */
  public function languages() {
    load_plugin_textdomain('readdomin', false, 
    dirname(plugin_basename(__FILE__)) . '/languages');
  }
  /**
   * show_world_count
   * @param string $content
   * @return string
   */
  public function show_word_count($content) {
    if( is_main_query() && is_single() && is_singular() && get_option('word_read_time', '1')) {
      return $this-> content_html($content);
    }
    return $content;
  }
  /**
   * render_output_html
   * @param int $post_id
   * @return string
   */
  public function render_output_html($post_id = null ) {
    
    $post_id = $post_id ? $post_id : get_the_ID();
    $this_post = get_post( $post_id );
    $content = $this_post->post_content; 

    if(in_array($this_post->post_type, get_option('supported_post_types', '1'))) {
      
      $html = '<hr><h3>' . esc_html(get_option('word_headline', 'Post Information Time Reading') ) . '</h3> <p>';
      $wordCount = str_word_count(strip_tags($content));

      if(get_option('word_read_time', '1') == '1' && get_option('rounding_behavior', '1') == '1' && get_option('supported_post_types', '1') ==  $this_post ) {
        $html .=  __('ROUND DOWN: This post will take', 'readdomin') . ' ' . floor($wordCount/200)  . ' ' .
        __('minute(s) to read.', 'readdomin') . '</p><br> <hr>';
      } 

      else {
        $html .=  __('ROUND UP: This post will take', 'readdomin') . ' ' .  ceil($wordCount/200)  . ' ' .
        __('minute(s) to read.', 'readdomin') . '</p><br> <hr>';
      } 
    }

    return $html;
  }
  /**
   * content_html
   * @param int $content
   * @return string
   */
  public function content_html($content) {
    return $content . $this->render_output_html();
  }
  /**
   * setting
   * @param int $type
   * @return string
   */
  public function setting($s) {
    add_settings_section('first_section', null, null, 'word-count-settings-page');

    // Headline title field
    add_settings_field('word_headline', __('Headline Title', 'readdomin'), array($this, 'head_line_html'), 'word-count-settings-page', 'first_section');
    register_setting('wordcount','word_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Time Reading' ));

    // word reading time checkbox field
    add_settings_field('word_read_time', 'Read Time', array($this, 'checkbox_html'), 'word-count-settings-page', 'first_section', array('theName' => 'word_read_time'));
    register_setting('wordcount','word_read_time', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1' ));

    // Rounding behavior
    add_settings_field('rounding_behavior', 'Rounding behavior', array($this, 'rounding_behavior_html'), 'word-count-settings-page', 'first_section');
    register_setting('wordcount','rounding_behavior', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1' ));

    add_settings_field( "supported_post_types" , "Supported Post Types", array($this, 'supported_post_types_html'), 'word-count-settings-page', 'first_section');
    register_setting('wordcount','supported_post_types', array( [],  'default' => '1' ));
  }

  /**
   * supported_post_types_html
   */
  public function supported_post_types_html() {
    $supportedPostTypes = get_option('supported_post_types', ['post']);
    $exclude_post_types = array(
      'attachment', 
      'revision', 
      'revision', 
      'wp_template',
      'wp_block', 
      'user_request', 
      'nav_menu_item', 
      'oembed_cache', 
      'customize_changeset', 
      'custom_css');
    foreach( get_post_types() as $type ):
      if( in_array( $type, $exclude_post_types ) ) {
        continue;
      }
      $name_id = "supported_post_types_" . $type;
    ?>
    <div class="label-flex">
      <label for="<?php echo $name_id;?>"><?php echo $type; ?></label>
      <input 
        type="checkbox"
        id="<?php echo $name_id;?>"
        name="supported_post_types[]"
        value="<?php echo $type; ?>" 
        <?php echo in_array($type, $supportedPostTypes) ? 'checked' : '';?>
      >
    </div>
    <?php endforeach;
  }

  /** 
  * sanitize_location_default
  * @param int $input
  * @return string
  */
  public function sanitize_location_default($input) {
    if($input != '0' && $input != '1') {
      add_settings_error('supported_post_types','rounding_behavior_error', 'Rounding Behavior can be up OR dowm');
      return get_option('rounding_behavior');
    }
    return $input;
  }

  /** 
  * head_line_html
  */
  public function head_line_html() { ?>
    <input type="text" 
      name="word_headline" 
      value="<?php echo esc_attr( get_option('word_headline') );?>">
  <?php }

  /** 
  * checkbox_html
  * @param int $args
  * @return string
  */
  function checkbox_html($args) { ?>
    <input 
      type="checkbox" 
      name="<?php echo $args['theName']; ?>" 
      value="1" <?php checked(get_option($args['theName']), '1')?>>
  <?php }

  /** 
  * rounding_behavior_html
  */
  public function rounding_behavior_html() { ?>
  <select name="rounding_behavior">
    <option value="0" <?php selected(get_option('rounding_behavior'), '0')?>> 
      <?php echo __('Round up', 'readdomin');?> 
    </option>
    <option value="1" <?php selected(get_option('rounding_behavior'), '1')?>> 
      <?php echo __('Round Down', 'readdomin');?> 
    </option>
  </select>

  <?php }

  /** 
  * admin_page
  */
  public function admin_page() {
    add_options_page('Reading Time Settings', __('Reading Time Posts', 'readdomin'),'manage_options', 'word-count-settings-page', array($this, 'admin_page_html'));
  }

  /** 
  * admin_page_html
  */
  public function admin_page_html() { ?>
      <div class="wrap">
        <h1 class="title"> 
          <?php echo __('Reading Time Settings:','readdomin');  ?>
          </h1>
        <form class="form-plugin" action="options.php" method="POST">
          <?php 
            settings_fields('wordcount');
            do_settings_sections('word-count-settings-page'); 
            submit_button();
          ?>
        </form>
      </div>
  <?php }

}

$ReadingTimePlugin = new ReadingTimePlugin();