// Small frontend interactions copied/adapted from original React app
$(function(){
  // language toggle: switch direction class on body
  $('.language-toggle').on('click', function(){
    var $b = $('body');
    if ($b.hasClass('rtl')) {
      $b.removeClass('rtl').addClass('ltr');
      $(this).text('العربية / English');
    } else {
      $b.removeClass('ltr').addClass('rtl');
      $(this).text('English / العربية');
    }
  });

  // add some floating particles for visual flair
  var $particles = $('<div class="particles"></div>').appendTo('body');
  for (var i = 0; i < 20; i++){
    var $p = $('<div class="particle"></div>');
    $p.css({ left: Math.random()*100+'%', top: Math.random()*100+'%'});
    $particles.append($p);
  }

  // Initial adjustment
  adjustBackButtonPosition();
});

function adjustBackButtonPosition(){
  try {
    var gap = 20; // px gap between content and sidebar
    var $sidebar = $('#app-sidebar');
    var isDocked = $('body').hasClass('sidebar-docked');
    var isCollapsed = $('body').hasClass('sidebar-collapsed');

    if ($sidebar.length && isDocked && !isCollapsed) {
      var sw = $sidebar.outerWidth();
      $back.css('right', (sw + gap) + 'px');
    } else {
      $back.css('right', '20px');
    }
  } catch(e) { /* ignore errors */ }
}
