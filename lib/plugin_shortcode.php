<?php
/**
 * Parent plugin class
 */

class plugin_shortcode {
  protected $param_defaults = array();
  protected $params_required = array();
  protected $params = array();

  protected $interior = null;
  
  protected $replace_counter;

  public function set_params($params) {
    // load in the rest as needed
    foreach ($this->param_defaults as $k => $v) {
      if (!isset($params[$k])) {
        $params[$k] = $v;
      }
    }

    // check for required settings
    $shortcode_err = false;
    foreach ($this->params_required as $r) {
      if (!isset($params[$r])) {
        return false;
        break;
      }
    }

    $this->params = $params;
    return true;
  }

  public function set_interior($interior) {
    $this->interior = $interior;
  }

  public function render() {
    return $this->return_value;
  }

  // subclass helpers
  protected function wrap_content($tag, $prefix, $suffix, $prefix_replace = null, $suffix_replace = null) {
    $counter = 0;
    preg_match_all("|\[" . $tag . "\](.*)\[/".$tag."\]|Us", $this->interior, $tag_matches);
    for ($i = 0; $i < count($tag_matches[0]); $i++) {
      $counter += 1;

      $wrapper = $tag_matches[0][$i];
      $interior = $tag_matches[1][$i];
      $this_prefix = (!is_null($prefix_replace))? $this->replace_replace($prefix, $prefix_replace, array('COUNTER' => $counter)) : $prefix;
      $this_suffix = (!is_null($suffix_replace))? $this->replace_replace($suffix, $suffix_replace, array('COUNTER' => $counter)) : $suffix;
      $this->interior = str_replace($wrapper, $this_prefix  . $interior . $this_suffix, $this->interior);
    }
  }

  protected function strip_interior($tag) {
    $this->interior = str_replace($tag, "", $this->interior);
  }

  private function replace_replace($string, $vars, $meta_vars) {
    foreach ($meta_vars as $key => $val) {
      $vars = str_replace($key, $val, $vars);
    }
    return vsprintf($string, $vars);
  }
}