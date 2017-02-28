/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * from http://stackoverflow.com/questions/11404047/transliterating-cyrillic-to-latin-with-javascript-function
 */
var a = {"Ё":"YO","Й":"I","Ц":"TS","У":"U","К":"K","Е":"E","Н":"N","Г":"G","Ш":"SH","Щ":"SCH","З":"Z","Х":"H","Ъ":"'",
	"ё":"yo","й":"i","ц":"ts","у":"u","к":"k","е":"e","н":"n","г":"g","ш":"sh","щ":"sch","з":"z","х":"h","ъ":"'","Ф":"F",
	"Ы":"I","В":"V","А":"a","П":"P","Р":"R","О":"O","Л":"L","Д":"D","Ж":"ZH","Э":"E","ф":"f","ы":"i","в":"v","а":"a",
	"п":"p","р":"r","о":"o","л":"l","д":"d","ж":"zh","э":"e","Я":"Ya","Ч":"CH","С":"S","М":"M","И":"I","Т":"T","Ь":"'",
	"Б":"B","Ю":"YU","я":"ya","ч":"ch","с":"s","м":"m","и":"i","т":"t","ь":"'","б":"b","ю":"yu",
	"І":"I","Ї":"I","Є":"E","і":"i","ї":"i","є":"e",};
function transliterate(word){
  return word.split('').map(function (char) { 
    return a[char] || char; 
  }).join("");
}

$(document).ready( function() {
	$('.truncate-vertical').each( function (self) {
//		alert(parseInt($(this).css('max-height')) + '\n' + $(this).height());
		if ($(this).height() < parseInt($(this).css('max-height'))) {
			$(this).parent().children('.fadeout').hide();
		}
	});
	// scripts for adminpanel
	$('[data-translit="source"]').change( function () {
		$('[data-translit=destination]').val(transliterate($(this).val()));
	});
});
