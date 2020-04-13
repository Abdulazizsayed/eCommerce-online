$(function(){
	'use strict';

	// live preview

	function live(name, el) {
		let price = '';
		$('.create-ad [name=' + name + ']').keyup(function() {
			if(name == 'price'){
				price = '$';
			} 
				$('.create-ad .live-preview ' + el).text(price + $(this).val());
		});
	}

	live('name', 'h3');
	live('desc', 'p');
	live('price', '.price-tag');
	
	// switch between signup and login
	$('.login-page h1 span').click(function() {
		let add;
		let remove;
		if($(this).hasClass('login-title')) {
			add = ' text-primary';
			remove = ' text-success';
		} else {
			add = ' text-success';
			remove = ' text-primary';
		}
		$(this).addClass(add).siblings().removeClass(remove);
		$('.login-page form').hide();
		$('.' + $(this).data('class')).fadeIn(100);
	});

	// hide placeholder on form focus
	$('[placeholder]').focus(function(){
		$(this).attr('data-text', $(this).attr('placeholder'));
		$(this).attr('placeholder', '');
	});

	$('[placeholder]').blur(function(){
		$(this).attr('placeholder', $(this).attr('data-text'));
	});

	// add astrisk on required fields
	$('input, select').each(function(){
		if($(this).attr('required')) {
			$(this).parent().prev().append('<span class="text-danger"> *</span>');
		}
	});

	// convert password field to text field
	$('.show-pass').hover(function(){
		$('[name=password]').attr('type', 'text');
	}, function(){
		$('[name=password]').attr('type', 'password');
	});

	// ask for deletion
	$('.confirm').click(function(){
		return confirm('Are you sure to delete this member?');
	});

	// View category by clicking it
	$('.categories .cat h3').click(function(){
		$(this).next('.full-view').fadeToggle(500);
	});

	$('.option span').click(function(){
		$(this).addClass('active').siblings('span').removeClass('active');
		if ($(this).data('view') === 'full') {
			$('.categories .cat .full-view').fadeIn(200);
		} else {
			$('.categories .cat .full-view').fadeOut(200);
		}
	});

	// trigger selectboxit
	$("select").selectBoxIt({
		autoWidth: false,
	});

});