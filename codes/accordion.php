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
    $this->strip_interior('<br />');
    $this->strip_interior('&nbsp;');
    $this->parse_accordion_interior();
    return '<ul class="accordion" data-accordion>'.$this->interior.'</ul>';
  }

  /**
   * parse the interior for item title and content
   */
  private function parse_accordion_interior() {
    // strip out all the stuff between the tags
    $this->interior = preg_replace('/^(.*)(\\[item\\])/sU', '${2}', $this->interior);
    $this->interior = preg_replace('/(\\[item\\]).*(\\[title\\])/sU', '${1}${2}', $this->interior);
    $this->interior = preg_replace('/(\\[\\/title\\]).*(\\[content\\])/sU', '${1}${2}', $this->interior);
    $this->interior = preg_replace('/(\\[\\/content\\]).*(\\[\\/item\\])/sU', '${1}${2}', $this->interior);
    $this->interior = preg_replace('/(\\[\\/item\\]).*(\\[item\\])/sU', '${1}${2}', $this->interior);
    $this->interior = preg_replace('/(\\[\\/item\\]).*(\\[\\/accordion\\])/sU', '${1}${2}', $this->interior);

    $this->wrap_content('title', '<a href="#%s%d">', '</a>', array($this->params['id'], 'COUNTER'));
    $this->wrap_content('content', '<div id="%s%d" class="content">', '</div>', array($this->params['id'], 'COUNTER'));
    $this->wrap_content('item', '<li class="accordion-navigation">', '</li>');
    
    
    // remove extra paragraphs
    // $this->interior = preg_replace('/(<div.*>)(</p>)/sU', '${1}', $this->interior);
    // $this->interior = preg_replace('/(<p>)(<ul.*>|<div.*>|<li.*>)/sU', '${2}', $this->interior);
    
  }

}