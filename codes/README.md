# Plugins

## Format

At minimum, plugins should extend class `plugin_shortcode`, specify default and required parameters (if any), and override the `render()` function.  For example:

```php
class youtube_shortcode extends plugin_shortcode {
  private $param_defaults = array(
    'id' => null,
    'showinfo' => 1,
    'controls' => 1,
    'rel' => 0,
    'height' => 360,
    'width' => 640);

  private $params_required = array(
    'id');

  public function render() {
    $params = $this->params;
    $output = <<<EOF
<div class="embed-wrapper">
  <iframe width="$params['width']" height="$params['height']" 
          src="https://www.youtube-nocookie.com/embed/$params['id']?rel=$params['rel']&amp;controls=$params['controls']&amp;showinfo=$params['showinfo']" 
          frameborder="0"
          allowfullscreen>
  </iframe>
</div>
EOF;
  }
```