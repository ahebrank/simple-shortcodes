<?php
class wufoo_shortcode extends plugin_shortcode {

  protected $param_defaults = array(
    'username' => null,
    'formhash' => null,
    'autoresize' => 'true',
    'height' => 1086,
    'header' => 'show',
    'ssl' => 'true');

  protected $params_required = array(
    'username', 'formhash');

  public function render() {
    $formhash = $this->params['formhash'];
    $username = $this->params['username'];
    $autoresize = $this->params['autoresize'];
    $height = $this->params['height'];
    $header = $this->params['header'];
    $ssl = $this->params['ssl'];

    $output = <<<EOF
<div id="wufoo-$formhash">
Fill out my <a href="https://$formhash.wufoo.com/forms/$formhash">online form</a>.
</div>
<div id="wuf-adv" style="font-family:inherit;font-size: small;color:#a7a7a7;text-align:center;display:block;">HTML Forms powered by <a href="http://www.wufoo.com">Wufoo</a>.</div>
<script type="text/javascript">var $formhash;(function(d, t) {
var s = d.createElement(t), options = {
'userName':'vtperformingarts',
'formHash':'$formhash',
'autoResize':$autoresize,
'height':'$height',
'async':true,
'host':'wufoo.com',
'header':'$header',
'ssl':$ssl};
s.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + 'www.wufoo.com/scripts/embed/form.js';
s.onload = s.onreadystatechange = function() {
var rs = this.readyState; if (rs) if (rs != 'complete') if (rs != 'loaded') return;
try { $formhash = new WufooForm();$formhash.initialize(options);$formhash.display(); } catch (e) {}};
var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);})(document, 'script');</script>

EOF;
    
    return $output;
  }

}
