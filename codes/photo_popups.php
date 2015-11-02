<?php
/**
 * Photo popups are specified by:
 *
 * [photo_popups]
 *   ( HTML list of links to images )
 * [/photo_popups]
 *
 * Note: requires jQuery
 * 
 */
class photo_popups_shortcode extends plugin_shortcode {

  public function render() {
    $this->parse_photo_popups();
    $this->append_photo_popup_script();
    return '<div class="photo-popup-wrapper">' . $this->interior . '</div>';
  }

  private function parse_photo_popups() {
    preg_match_all('/<a (.*)>/U', $this->interior, $matches);
    $replace = '<a %s data-photo-popup>';
    for ($i = 0; $i < count($matches[0]); $i++) {
      $this->interior = str_replace($matches[0][$i], sprintf($replace, $matches[1][$i]), $this->interior);
    }
  }

  private function append_photo_popup_script() {
    $script = <<<EOS
<script>
  $('[data-photo-popup]').on({
    'mouseenter': function(e) {
      var div = $('<div class="photo-popup" style="position: absolute;">');
      var imageUrl = $(this).attr('title');
      if (typeof imageUrl !== typeof undefined && imageUrl !== false) {
        $(this).removeAttr('title');
        $(this).data('image-src', imageUrl);
      } else {
        imageUrl = $(this).data('image-src');
        if (typeof imageUrl === typeof undefined || imageUrl === false) {
          imageUrl = $(this).attr('href');
        }
      }
      var img = $('<img width="200" src="' + imageUrl + '" alt="' + escape($(this).text()) + '">')
      div.append(img);
      var offset = $(this).offset();
      div.css({
        top: offset.top + 30,
        left: offset.left + 30
      });
      $('body').append(div)
    },
    'mouseleave': function(e) {
      $('body > .photo-popup').remove();
    }
  });
</script>
EOS;
    $this->interior .= $script;
  }

}