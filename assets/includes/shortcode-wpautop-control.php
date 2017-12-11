<?php
/*
 * Name: Shortcode WPautop Control
 * Description: Allows you to turn off autop in individual shortcodes. It is inteded to be used within
 * WordPress plugins and should not collide with other plugins that use it also.
 * Version: 1.0.0
 * Author: Chiedo
 * Git URI: https://github.com/chiedolabs/shortcode-wpautop-control
 * Author URI: https://labs.chie.do
 * License: GPLv2 or later
 * @param {array} array_of_shortcodes Array of shortcodes that shouldn't use wpautop
 */
if(!function_exists('chiedolabs_shortcode_wpautop_control')){
  function chiedolabs_shortcode_wpautop_control($array_of_shortcodes) {

    global $chiedolabs_wpautop_shortcodes_to_ignore;

    // Make sure that if the global variable has already been set, it doesn't get overwritten and instead
    // merges the shortcodes passed to it.
    if(is_array($chiedolabs_wpautop_shortcodes_to_ignore)) {
      $chiedolabs_wpautop_shortcodes_to_ignore = array_unique(array_merge($array_of_shortcodes, $chiedolabs_wpautop_shortcodes_to_ignore));
    } else {
      $chiedolabs_wpautop_shortcodes_to_ignore = $array_of_shortcodes;
    }

    // Remove the default WordPress wpautop filter
    remove_filter( 'the_content', 'wpautop' );
    remove_filter( 'the_excerpt', 'wpautop' );

    // add our new more advanced filter
    add_filter( 'the_content', 'chiedolabs_shortcode_autop' );
    add_filter( 'the_excerpt', 'chiedolabs_shortcode_autop' );

    // First check to see if the function exists since multiple plugins could
    // be using this
    if(!function_exists('chiedolabs_shortcode_autop')){
      function chiedolabs_shortcode_autop( $pee, $br = true) {
        global $chiedolabs_wpautop_shortcodes_to_ignore;
        $pre_tags = array();
        $shortcode_tags = array();

        if ( trim($pee) === '' )
          return '';

        // Just to make things a little easier, pad the end.
        $pee = $pee . "\n";

        /*
         * Pre tags shouldn't be touched by autop.
         * Replace pre tags with placeholders and bring them back after autop.
         */
        if ( strpos($pee, '<pre') !== false ) {
          $pee_parts = explode( '</pre>', $pee );
          $last_pee = array_pop($pee_parts);
          $pee = '';
          $i = 0;

          foreach ( $pee_parts as $pee_part ) {
            $start = strpos($pee_part, '<pre');

            // Malformed html?
            if ( $start === false ) {
              $pee .= $pee_part;
              continue;
            }

            $name = "<pre wp-pre-tag-$i></pre>";
            $pre_tags[$name] = substr( $pee_part, $start ) . '</pre>';

            $pee .= substr( $pee_part, 0, $start ) . $name;
            $i++;
          }

          $pee .= $last_pee;
        }

        /*
         * Shortcodes shouldn't be touched by autop.
         * Replace pre tags with placeholders and bring them back after autop.
         */
        foreach($chiedolabs_wpautop_shortcodes_to_ignore as $shortcode) {
          if ( strpos($pee, "[$shortcode") !== false ) {
            $pee_parts = explode( "[/$shortcode]", $pee );
            $last_pee = array_pop($pee_parts);
            $pee = '';
            $i = 0;

            foreach ( $pee_parts as $pee_part ) {
              $start = strpos($pee_part, "[$shortcode");

              // Malformed html?
              if ( $start === false ) {
                $pee .= $pee_part;
                continue;
              }

              $name = "[$shortcode wp-$shortcode-tag-$i][/$shortcode]";
              $shortcode_tags[$name] = substr( $pee_part, $start ) . "[/$shortcode]";

              $pee .= substr( $pee_part, 0, $start ) . $name;
              $i++;
            }

            $pee .= $last_pee;
          }
        }
        // Change multiple <br>s into two line breaks, which will turn into paragraphs.
        $pee = preg_replace('|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee);

        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

        // Add a single line break above block-level opening tags.
        $pee = preg_replace('!(<' . $allblocks . '[\s/>])!', "\n$1", $pee);

        // Add a double line break below block-level closing tags.
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);

        // Standardize newline characters to "\n".
        $pee = str_replace(array("\r\n", "\r"), "\n", $pee);

        // Find newlines in all elements and add placeholders.
        $pee = wp_replace_in_html_tags( $pee, array( "\n" => " <!-- wpnl --> " ) );

        // Collapse line breaks before and after <option> elements so they don't get autop'd.
        if ( strpos( $pee, '<option' ) !== false ) {
          $pee = preg_replace( '|\s*<option|', '<option', $pee );
          $pee = preg_replace( '|</option>\s*|', '</option>', $pee );
        }

        /*
         * Collapse line breaks inside <object> elements, before <param> and <embed> elements
         * so they don't get autop'd.
         */
        if ( strpos( $pee, '</object>' ) !== false ) {
          $pee = preg_replace( '|(<object[^>]*>)\s*|', '$1', $pee );
          $pee = preg_replace( '|\s*</object>|', '</object>', $pee );
          $pee = preg_replace( '%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee );
        }

        /*
         * Collapse line breaks inside <audio> and <video> elements,
         * before and after <source> and <track> elements.
         */
        if ( strpos( $pee, '<source' ) !== false || strpos( $pee, '<track' ) !== false ) {
          $pee = preg_replace( '%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee );
          $pee = preg_replace( '%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee );
          $pee = preg_replace( '%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee );
        }

        // Remove more than two contiguous line breaks.
        $pee = preg_replace("/\n\n+/", "\n\n", $pee);

        // Split up the contents into an array of strings, separated by double line breaks.
        $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);

        // Reset $pee prior to rebuilding.
        $pee = '';

        // Rebuild the content as a string, wrapping every bit with a <p>.
        foreach ( $pees as $tinkle ) {
          $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }

        // Under certain strange conditions it could create a P of entirely whitespace.
        $pee = preg_replace('|<p>\s*</p>|', '', $pee);

        // Add a closing <p> inside <div>, <address>, or <form> tag if missing.
        $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);

        // If an opening or closing block element tag is wrapped in a <p>, unwrap it.
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

        // In some cases <li> may get wrapped in <p>, fix them.
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);

        // If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);

        // If an opening or closing block element tag is preceded by an opening <p> tag, remove it.
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);

        // If an opening or closing block element tag is followed by a closing <p> tag, remove it.
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

        // Optionally insert line breaks.
        if ( $br ) {
          // Replace newlines that shouldn't be touched with a placeholder.
          $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee);

          // Normalize <br>
          $pee = str_replace( array( '<br>', '<br/>' ), '<br />', $pee );

          // Replace any new line characters that aren't preceded by a <br /> with a <br />.
          $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);

          // Replace newline placeholders with newlines.
          $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
        }

        // If a <br /> tag is after an opening or closing block tag, remove it.
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);

        // If a <br /> tag is before a subset of opening or closing block tags, remove it.
        $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
        $pee = preg_replace( "|\n</p>$|", '</p>', $pee );

        // Replace placeholder <pre> tags with their original content.
        if ( !empty($pre_tags) )
          $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

        // Replace placeholder shortcode tags with their original content.
        if ( !empty($shortcode_tags) )
          $pee = str_replace(array_keys($shortcode_tags), array_values($shortcode_tags), $pee);

        // Restore newlines in all elements.
        if ( false !== strpos( $pee, '<!-- wpnl -->' ) ) {
          $pee = str_replace( array( ' <!-- wpnl --> ', '<!-- wpnl -->' ), "\n", $pee );
        }

        return $pee;
      }
    }
  }
}
