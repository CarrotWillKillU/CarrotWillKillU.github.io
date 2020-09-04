// This is to fetch the player count
$(document).ready(()=>{
  const ip = $(".real-online").attr("data-ip");
  const port = $(".real-online").attr("data-port");

  $.get(`https://mcapi.us/server/status?ip=${ip}&port=${port}`, (result)=>{
    if (result.online) {
      $(".real-online").html(result.players.now + "/" + result.players.max);
      setOnlines([[result.players.now, result.players.max]]);
    } else {
      $(".real-online").html("Server isn't online!");
    }
  });

  setInterval(()=>{
    $.get(`https://mcapi.us/server/status?ip=${ip}&port=${port}`, (result)=>{
      if (result.online) {
        $(".real-online").html(result.players.now + "/" + result.players.max);
        setOnlines([[result.players.now, result.players.max]]);
      } else {
        $(".real-online").html("Server isn't online!");
      }
    });
  }, 3000);
});

let t;
$(document).ready(()=>{
	tserver = $(".ip").html();
})
$(document).on("click",".startGame",()=>{
	let copy = document.createElement("textarea");
	copy.style.position = "absolute";
	copy.style.left = "-99999px";
	copy.style.top = "0";
	copy.setAttribute("id", "ta");
	document.body.appendChild(copy);
	copy.textContent = tserver;
	copy.select();
	document.execCommand("copy");
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}
  toastr["success"]("IP Copied!")
});

$(function() {
    /* Выбор сервера */

    var buyBlock = $('#buyBlock');
    let payBlock = $('#payBlock');
    buyBlock.find('.closeBtn').click(function() {
        buyBlock.parent().removeClass('active');
        $('.donate').attr('style','display:none !important');
    });
    $('#listServers .server:not(.preparing)').click(function() {
        var id = $(this).data('id');
        var v = $(this).find('.rhomb').text();
        buyBlock.find('h1 .v').text(v);
        $('.donate').attr('style','display:none !important');
        $('#donate-' + id).show();
        buyBlock.parent().addClass('active');
    });
    /* Мобильный скролл */

    if (window.matchMedia('(max-width: 768px)').matches) {
        $('#listServers .server:not(.preparing)').click(function() {
            $("html, body").animate({
                scrollTop: $('.block.servers').offset().top - 10 + "px"
            }); //$(e.target).attr('href')
        });
    }
});