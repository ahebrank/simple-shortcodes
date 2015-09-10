<?php
class youtube_shortcode extends plugin_shortcode {
  protected $param_defaults = array(
    'id' => null,
    'showinfo' => 1,
    'controls' => 1,
    'rel' => 0,
    'height' => 360,
    'width' => 640);

  protected $params_required = array(
    'id');

  public function render() {
    $params = $this->params;
    $output = <<<EOF
<div class="embed-wrapper">
  <iframe width="${params['width']}" height="${params['height']}" 
          src="https://www.youtube-nocookie.com/embed/${params['id']}?rel=${params['rel']}&amp;controls=${params['controls']}&amp;showinfo=${params['showinfo']}" 
          frameborder="0"
          allowfullscreen>
  </iframe>
</div>
EOF;
    return $output;
  }

}