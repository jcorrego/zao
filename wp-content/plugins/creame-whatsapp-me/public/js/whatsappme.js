(function ($) {
  'use strict';

  $(function () {
    var delay_on_start = 3000;
    var $whatsappme = $('.whatsappme');
    var $badge = $whatsappme.find('.whatsappme__badge');
    var wame_settings = $whatsappme.data('settings');

    // In some strange cases data settings are empty
    if (typeof (wame_settings) == 'undefined') {
      try {
        wame_settings = JSON.parse($whatsappme.attr('data-settings'));
      } catch (error) {
        wame_settings = undefined;
      }
    }

    // only works if whatsappme is defined
    if ($whatsappme.length && !!wame_settings && !!wame_settings.telephone) {
      whatsappme_magic();
    }

    function whatsappme_magic() {
      var is_mobile = !!navigator.userAgent.match(/Android|iPhone|BlackBerry|IEMobile|Opera Mini/i);
      var has_cta = wame_settings.message_text !== '';
      var message_hash, is_viewed, timeoutID;

      // stored values
      var messages_viewed = (localStorage.whatsappme_hashes || '').split(',').filter(Boolean);
      var is_second_visit = localStorage.whatsappme_visited == 'yes';

      if (has_cta) {
        message_hash = hash(wame_settings.message_text).toString();
        is_viewed = messages_viewed.indexOf(message_hash) > -1;
      }

      localStorage.whatsappme_visited = 'yes';

      if (!wame_settings.mobile_only || is_mobile) {
        // show button
        setTimeout(function () { $whatsappme.addClass('whatsappme--show'); }, delay_on_start);

        if (has_cta && !is_viewed) {
          if (wame_settings.message_badge) { // show badge
            setTimeout(function () { $badge.addClass('whatsappme__badge--in'); }, delay_on_start + wame_settings.message_delay);
          } else if (is_second_visit) { // show dialog
            setTimeout(function () { $whatsappme.addClass('whatsappme--dialog'); }, delay_on_start + wame_settings.message_delay);
          }
        }
      }

      if (has_cta && !is_mobile) {
        $('.whatsappme__button')
          .mouseenter(function () { timeoutID = setTimeout(show_dialog, 1500); })
          .mouseleave(function () { clearTimeout(timeoutID); });
      }

      $('.whatsappme__button').click(function () {
        var link = whatsapp_link(wame_settings.telephone, wame_settings.message_send);

        if (has_cta && !$whatsappme.hasClass('whatsappme--dialog')) {
          show_dialog();
        } else {
          $whatsappme.removeClass('whatsappme--dialog');
          save_message_viewed();
          send_event(link);
          // Open WhatsApp link
          window.open(link, 'whatsappme');
        }
      });

      $('.whatsappme__close').click(function () {
        $whatsappme.removeClass('whatsappme--dialog');
        save_message_viewed();
      });

      function show_dialog() {
        $whatsappme.addClass('whatsappme--dialog');

        if (wame_settings.message_badge && $badge.hasClass('whatsappme__badge--in')) {
          $badge.removeClass('whatsappme__badge--in').addClass('whatsappme__badge--out');
          save_message_viewed();
        }
      }

      function save_message_viewed() {
        if (has_cta && !is_viewed) {
          messages_viewed.push(message_hash)
          localStorage.whatsappme_hashes = messages_viewed.join(',');
          is_viewed = true;
        }
      }
    }
  });

  // Return a simple hash (source https://gist.github.com/iperelivskiy/4110988#gistcomment-2697447)
  function hash(s) {
    for (var i = 0, h = 1; i < s.length; i++) {
      h = Math.imul(h + s.charCodeAt(i) | 0, 2654435761);
    }
    return (h ^ h >>> 17) >>> 0;
  };

  // Return WhatsApp link with optional message
  function whatsapp_link(phone, message) {
    var link = 'https://api.whatsapp.com/send?phone=' + phone;
    if (typeof (message) == 'string' && message != '') {
      link += '&text=' + encodeURIComponent(message);
    }

    return link;
  }

  // Trigger Google Analytics event
  function send_event(link) {
    if (typeof gtag == 'function') { // Send event (Global Site Tag - gtag.js)
      gtag('event', 'click', {
        'event_category': 'WhatsAppMe',
        'event_label': link,
        'transport_type': 'beacon'
      });
    } else if (typeof ga == 'function') { // Send event (Universal Analtics - analytics.js)
      ga('send', 'event', {
        'eventCategory': 'WhatsAppMe',
        'eventAction': 'click',
        'eventLabel': link,
        'transport': 'beacon'
      });
    }
  }

}(jQuery));