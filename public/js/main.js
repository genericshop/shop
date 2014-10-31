var $sw;
$(window).on('load resize', function() { 

	$sw = $(this).width();
	
	// header quicknav
	
	$('.header .quick').css('line-height', $sw > 991 ? ($('.header .container').innerHeight() - 40) + 'px' : 'normal');
	
	// sticky footer
	
    var $fh = $('.footer').innerHeight();
    
    $('.push').css('height', $fh + 'px');
	$('.site-wrapper').css({'margin-bottom':'-' + $fh + 'px'});
	
});

$(function() {
	
	$(':text, :password').prop('autocomplete', 'off');
	
	$('[data-tooltip="tooltip"]').tooltip();
	
	if ($('.ellipsis').length) {
		$('.ellipsis').dotdotdot({
			watch: true,
		});
	}
	
	if ($('.navigation').length) {
		$('.navigation').find('.active').parents('ul').addClass('active');
		$('.navigation a').on('click', function(e) {
			$('.navigation a').removeClass('active');
			if ($(this).next('ul').length) {
				$('.navigation ul').removeClass('active');
				$(this).toggleClass('active');
				$(this).next('ul').toggleClass('active');
				e.preventDefault();
			}
		});
	}
	$('.btn-Back').on('click', function(e) {
		 window.history.back();
	});

	
	$('.cart-add').on('click', function(e) {

		e.preventDefault();
		
		var $this	 = $(this),
			$item	 = { type: $this.data('type'), sid: $this.data('id'), qty: 1 },
			$btnText = '';
		
        $.ajax('cart/add', {
            type: 'post',
        	data: JSON.stringify([$item]),
        	contentType: 'application/json; charset=UTF-8',
        	beforeSend: function() {
        	    $btnText = $this.text();
        		$this.prop('disabled', 'disabled').text('Please wait...');
        	} 
        }).done(function(data, status, xhr) {
            if (data.result === 1) {
                displayMessage(data.message, 'success');
            }
        }).always(function() {
        	$this.prop('disabled', false).text($btnText);
        });

        e.preventDefault();
		
	});
	
	$('form').on('submit', function() {
		$(this).find('[type="submit"]').prop('disabled', 'disabled');
	});
	
    $(document).on('hidden.bs.modal', '#modal', function(e) {
        $(this).removeData();
    });
	
	$(document).on('click', '#modal [type="submit"]', function(e) {
	    e.preventDefault();
	    $(this).closest('.modal').find('form').submit();
	});
	
});

function displayMessage(message, type)
{
	var $class;
	
	if (typeof type === 'undefined') {
		$class = 'site-message-default';
	} else {
		
		if (type === 'error')
			$class = 'site-message-error';
		else if (type === 'success')
			$class = 'site-message-success';
		
	}
	
	$('#site-message')
		.removeClass().addClass('site-message ' + $class).text(message)
		.stop().show().delay(2500).fadeOut();
}

/*
function cartSummary(show)
{
	show = typeof show !== 'undefined' ? show : false;
	$.getJSON('cart/summary', function(data) {
		$('#cart-count').text(data.count);
		cartModal(show);
	});
}

function cartModal(show)
{
	show = typeof show !== 'undefined' ? show : false;
	$('#modal-cart .modal-content').load('cart/view', function() {
		if (true === show) {
			$('#modal-cart').modal('show');
		}
	});
}
*/

function isInteger(string) 
{    
    return parseFloat(string) == parseInt(string, 10);
}

function initMap(obj)
{
	if (obj.is(':visible'))
		obj.css('height', '300px');
	
	if (!obj.hasClass('initialized')) {
	
		var ll = obj.data('latlng').split(',');
		var latLng = new google.maps.LatLng(ll[0], ll[1]);
		var mapOptions = {
			center: latLng,
			zoom: obj.data('zoom'),
		}
		
		map = new google.maps.Map(document.getElementById(obj.attr('id')), mapOptions);
		var marker = new google.maps.Marker({
		    position: latLng,
		    map: map
		});
		
		obj.addClass('initialized');
		
	} else {
	
		google.maps.event.trigger(map, 'resize');
		
	}
}

function evalCallback(callback)
{
	if ($.type(callback) === 'array') {
		$.each(callback, function(k, v) {
			eval(v);
		});
	} else {
		eval(callback);
	}
}
function printOrder()
{
var headstr = "<html><head><title>Shopping Cart Details</title></head><body>";
var footstr = "</body>";
var newstr = "";
//$("[style='display: block;']")
var oldstr = document.body.innerHTML;
$("[style='display: block;']").each(function() {
	  newstr  += $(this).html();
});
document.body.innerHTML = headstr+newstr+footstr;
window.print();
document.body.innerHTML = oldstr;
return false;
}

function printBuyBackBooks()
{
var headstr = "<html><head><title>Buy-back Books</title></head><body>";
var footstr = "</body>";
var newstr = document.getElementById("print-area").innerHTML;
var oldstr = document.body.innerHTML;
document.body.innerHTML = headstr+newstr+footstr;
window.print();
document.body.innerHTML = oldstr;
return false;
}

function goBack()
{
 window.history.back();
console.log("Done fireing");
}