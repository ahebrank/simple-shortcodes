<?php
/**
 * Accordions are specified by:
 *
 * [accordion]
 *   [item]
 *     [title]Tab 1 title[/title]
 *     [content]Tab 1 content[/content]
 *   [/item]
 *   [item]
 *     [title]Tab 2 title[/title]
 *     [content]Tab 2 content[/content]
 *   [/item]
 * [/accordion]
 * 
 */
class accordion_shortcode extends plugin_shortcode {

  protected $param_defaults = array(
    'id' => 'accordion');

  public function render() {
    $this->strip_interior("<br />");
    $this->strip_interior("&nbsp;");
    $this->parse_accordion_interior();
    return '<ul class="accordion" data-accordion>'.$this->interior.'</ul>';
  }

  /**
   * parse the interior for item title and content
   */
  private function parse_accordion_interior() {
    $this->wrap_content('item', '<li class="accordion-navigation">', '</li>');
    $this->wrap_content('title', '<a href="#%s%d">', '</a>', array($this->params['id'], 'COUNTER'));
    $this->wrap_content('content', '<div id="%s%d" class="content">', '</div>', array($this->params['id'], 'COUNTER'));
  
    // strip out all the stuff between the tags
    $this->interior = preg_replace('/^(.*)(<li)/sU', '${2}', $this->interior);
    $this->interior = preg_replace('/(<li.*>).*(<a)/sU', '${1}${2}', $this->interior);
    $this->interior = preg_replace('/(<a.*>.*<\\/a>).*(<div id.*>)<\\/p>/sU', '${1}${2}', $this->interior);
    $this->interior = preg_replace('/(<div id.*>)(<\\/p>)/sU', '${1}', $this->interior);
    $this->interior = preg_replace('/<p> *<\\/div>/sU', '</div>', $this->interior);
    $this->interior = preg_replace('/<p> *<\\/li> *<\\/p>/sU', '</li>', $this->interior);
    $this->interior = preg_replace('/(<\\/li>).*(<li.*>)/sU', '${1}${2}', $this->interior);
    $this->interior = preg_replace('/<p>$/s', '${1}', $this->interior);
  }

}