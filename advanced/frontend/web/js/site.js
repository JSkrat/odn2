/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready( function() {
	$('.truncate-vertical').each( function (self) {
//		alert(parseInt($(this).css('max-height')) + '\n' + $(this).height());
		if ($(this).height() < parseInt($(this).css('max-height'))) {
			$(this).parent().children('.fadeout').hide();
		}
	});
});
