$(function() {
  $('#imrp_ill0').width(MRP_blockHeight);
  $('#imrp_ill0').height(MRP_blockHeight);

  var randomPict = MRP_Opt['pictures'];
  if (randomPict.length > 1 && $("#mbRandomPhoto").length > 0) {
    $('#imrp_ill0 a').attr("href", randomPict[0]['link']);
    $('#imrp_ill0 img').attr("src", randomPict[0]['thumb']);

    var nextFaderPos = 1;
    var duration = 400;
    var pause = false;

    $('#imrp_ill0 img').after('<img id="irmbRPicImg">');
    $('#imrp_ill0 img').eq(1).hide();
    $('#imrp_ill0 img')[1].src = randomPict[1]['thumb'];
    $('#imrp_ill0 img').css('position', 'absolute');

    $('#imrp_ill0').hover(function() {
      pause = true;
    },function() {
      pause = false;
    });

    function doRotate() {
      if ($('#imrp_ill0 img')[1].complete) {
        if(!pause) {
          $('#imrp_ill0 img').first().fadeOut(duration, function() {
            nextFaderPos = (nextFaderPos + 1) % randomPict.length;
            $('#imrp_ill0 img')[1].src = randomPict[nextFaderPos]['thumb'];
          });
          $('#imrp_ill0 img').first().insertAfter($('#imrp_ill0 img').eq(1)); // swap
          $('#imrp_ill0 img').first().fadeIn(duration);
          setTimeout(function() {
            $('#imrp_ill0 a').attr("href", randomPict[nextFaderPos]['link']);
          }, duration/3);
        }
      }
    }

    var rotate = setInterval(doRotate, MRP_delay);
  }
});
