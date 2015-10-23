<?php
if (!defined('APP_VER')) {
    exit('No direct script access allowed');
}
// define the old-style EE object
if (!function_exists('ee')) {
    function ee()
    {
        static $EE;
        if (! $EE) {
          $EE = get_instance();
        }
        return $EE;
    }
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'plugin_shortcode.php');

class Simple_shortcodes_ext {

  public $settings = array();
  public $name = 'Simple Shortcodes';
  public $version = '0.1';
  public $description = 'Pluggable shortcodes';
  public $settings_exist = 'n';
  public $docs_url = '';
  private $plugins = array();

  /**
   * Constructor
   *
   * @param mixed Settings array or empty string if none exist.
   */
  public function __construct($settings = array()) {
    $this->settings = $settings;

    // find available plugins
    $plugin_directory = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'codes' . DIRECTORY_SEPARATOR;
    $plugin_files = glob($plugin_directory . '*.php');

    foreach ($plugin_files as $f) {
      $pp = pathinfo($f);
      $this->plugins[$pp['filename']] = $f;
    }
  }

  /**
   * Activate Extension
   *
   * This function enters the extension into the exp_extensions table
   *
   * @see http://codeigniter.com/user_guide/database/index.html for more information on the db class.
   *
   * @return void
   */
  public function activate_extension() {
    $hooks = array(
      'template_post_parse' => 'template_post_parse'
    );
    foreach($hooks as $hook => $method) {
      $data = array(
        'class' => __CLASS__,
        'method' => $method,
        'hook' => $hook,
        'priority' => 10,
        'version' => $this->version,
        'enabled' => 'y',
        'settings' => ''
      );
      ee()->db->insert('exp_extensions', $data);
    }

    return true;
  }

  /**
   * Update Extension
   *
   * This function performs any necessary db updates when the extension page is visited.
   *
   * @return mixed void on update / false if none
   */
  public function update_extension($current = '') {
    if($current == '' || $current == $this->version)
      return FALSE;

    ee()->db->where('class', _CLASS__);
    ee()->db->update(
      'extensions',
      array('version' => $this->version)
    );
  }

  /**
   * Disable Extension
   *
   * This method removes information from the exp_extensions table
   *
   * @return void
   */
  public function disable_extension() {
    ee()->db->where('class', __CLASS__);
    ee()->db->delete('extensions');
  }

  /* Hook the template post parsing */
  public function template_post_parse($final_template, $is_partial, $site_id) {
    // play nice with others
    if (isset(ee()->extensions->last_call) && ee()->extensions->last_call) {
     $final_template = ee()->extensions->last_call;
    }

    // don't run if we're in a partial
    if ($is_partial !== false) {
      return $final_template;
    }

    // find all the shortcodes
    foreach (array_keys($this->plugins) as $plugin) {
      preg_match_all("|<p>\[" . $plugin . " *(?<params>.*) *\](?<interior>.*)\[/" . $plugin . "\]</p>|Us", $final_template, $shortcode_matches); 
      //echo "|\[" . $plugin . " +(.*) *\](.*)\[/" . $plugin . "\]|s"; exit();
      if (count($shortcode_matches[0])) {
        $closing_tag = TRUE;
      }
      else {
        // try again without a closing tag
        preg_match_all("|(<p>)*\[" . $plugin . " *(.*) *\](</p>)*|", $final_template, $shortcode_matches);
        $closing_tag = FALSE;
      }

      for ($i = 0; $i < count($shortcode_matches[0]); $i++) {
        $shortcode = $shortcode_matches[0][$i];
        $params = $this->get_params($shortcode_matches['params'][$i]);
        $interior = "";
        if ($closing_tag) {
          $interior = $shortcode_matches['interior'][$i];
        }

        $snippet = $this->plugin_replacement($plugin, $params, $interior);
        $final_template = str_replace($shortcode, $snippet, $final_template);
      }
    }

    return $final_template;
  }

  private function plugin_replacement($plugin, $params, $interior = null) {
    $plugin_file = $this->plugins[$plugin];
    require_once($plugin_file);
    $classname = $plugin . "_shortcode";
    $p = new $classname();
    if (!$p->set_params($params)) {
      return "Missing required param(s).";
    }

    if (!is_null($interior)) {
      $p->set_interior($interior);
    }

    return $p->render();
  }

  /**
   * return a key -> value array of parameters
   * @param  [str] $paramstr  params from the template
   * @return [array]          
   */
  private function get_params($paramstr) {
    if (empty($paramstr)) return array();

    preg_match_all("/([a-z]+) *= *[\'\"]([a-zA-Z0-9\-_]+)[\'\"]/", $paramstr, $param_matches);
    $params = array();
    for ($j = 0; $j < count($param_matches[0]); $j++) {
      $params[$param_matches[1][$j]] = $param_matches[2][$j];
    }
    return $params;
  }


}
?>