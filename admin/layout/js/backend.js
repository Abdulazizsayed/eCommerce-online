$(function(){
	'use strict';

	// dashboard
	$('.toggle-info').click(function(){
		$(this).toggleClass('selected').parent().next('.panel-body').fadeToggle(100);
		if($(this).hasClass('selected')) {
			$(this).html('<i class="fa fa-minus fa-lg"></i>');
		} else {
			$(this).html('<i class="fa fa-plus fa-lg"></i>');
		}
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

	// show delete button on child cats
	$(".child-cat").hover(function() {
		$(this).children(".show-delete").fadeIn();
	}, function() {
		$(this).children(".show-delete").fadeOut();
	});
});