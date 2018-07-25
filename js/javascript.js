function changeSort(srch, sortid, sortdir, pg) {
	/* $.blockUI({ message: '<h1 style="white-space: nowrap; min-width: 40%;"><img src="images/busy.gif" />Just a moment...</h1>' }); */

    $.get("results.php?srch=" + srch + "&sort=" + sortid + "&sortdir=" + sortdir + "&pg=" + pg, function(data, status){
		$("section").html(data);
	});
}

function setSearch(srch) {
	if (self !== top) {
		$("[name=srch]", window.parent.document).val(srch);
	} else {
		$("[name=srch]").val(srch);
	}
	doSearch();
}

function doSearch() {
	$.blockUI({ message: '<h1 style="white-space: nowrap; min-width: 40%;"><img src="images/busy.gif" />Just a moment...</h1>' });

	var srch;
	if (self !== top) {
		srch = encodeURI($("[name=srch]", window.parent.document).val());

		$.get("results.php?srch=" + srch, function(data, status){
			$("section", window.parent.document).html(data);
			if (status == 'success') {
				$.unblockUI();
				if (data.search('login_form') >= 0) {
					top.location.href = '/login.php';
				} else {
					window.history.pushState({"html":'index.php?srch='+srch,"pageTitle":'Kirbyville C.I.S.D. - Users'}, "", '/index.php?srch='+srch);
				}
			}
		});
	} else {
		srch = $("[name=srch]").val();

		$.get("results.php?srch=" + srch, function(data, status){
			$("section").html(data);
			if (status == 'success') {
				$.unblockUI();
				if (data.search('login_form') >= 0) {
					top.location.href = '/login.php';
				} else {
					window.history.pushState({"html":'index.php?srch='+srch,"pageTitle":'Kirbyville C.I.S.D. - Users'}, "", '/index.php?srch='+srch);
				}
			}
		});
	}
}

function noFrames() {
	if (top.location != location) {
		top.location.href = document.location.href;
	}
}

/*

add this function to the document.ready in header.php and then change all the links to one section in css that applies to all the menu links.
rename all the images and get an image for every menu item.
change the menu on computer screen to a row of links and keep it a drop down menu on smaller screens like phones.

function changeMenuImages() {
	$('.dropdown-content').children('a').each(function(i) {
		n = this.attr('id');
		this.css("background-image", "url('images/'" + n + ".png)");
	});
}
*/

function showPopup(url, x, y) {
	if (self !== top) {
		$("#popupframe", window.parent.document).attr("src", "");

		var left = Math.max(0, (($(window.parent).width()/2)-(x / 2)) + $(window.parent).scrollLeft());
		var top = Math.max(0, (($(window.parent).height()/2)-(y / 2)) + $(window.parent).scrollTop());

		if ( x > $(window.parent).width() ) {
				left = 0;
				x = $(window.parent).width();
		}

		if ( y > $(window.parent).height() ) {
			top = 0;
			y = $(window.parent).height();
		}

		$("#popupframe", window.parent.document).attr("src", url);

		$("#popup", window.parent.document).css("top", top + 'px');
		$("#popup", window.parent.document).css("left", left + 'px');
		$("#popup", window.parent.document).css("width", x + 'px');
		$("#popup", window.parent.document).css("height", y + 'px');
		$("#popupframe", window.parent.document).css("height", (y-29) + 'px');
		$("#popup", window.parent.document).css("display", "block");
	} else {
		$("#popupframe").attr("src", "");

		var left = Math.max(0, (($(window).width()/2)-(x / 2)) + $(window).scrollLeft());
		var top = Math.max(0, (($(window).height()/2)-(y / 2)) + $(window).scrollTop());

		if ( x > $(window).width() ) {
				left = 0;
				x = $(window).width();
		}

		if ( y > $(window).height() ) {
			top = 0;
			y = $(window).height();
		}

		$("#popupframe").attr("src", url);

		$("#popup").css("top", top + 'px');
		$("#popup").css("left", left + 'px');
		$("#popup").css("width", x + 'px');
		$("#popup").css("height", y + 'px');
		$("#popupframe").css("height", (y-29) + 'px');
		$("#popup").css("display", "block");
	}
}

function hidePopup() {
	if (self !== top) {
		$("#popup", window.parent.document).css("display", "none");
	} else {
		$("#popup").css("display", "none");
	}
}

function handle_mousedown(e){
    window.my_dragging = {};
    my_dragging.pageX0 = e.pageX;
    my_dragging.pageY0 = e.pageY;
    my_dragging.elem = this;
    my_dragging.offset0 = $(this).offset();
    function handle_dragging(e){
        var left = my_dragging.offset0.left + (e.pageX - my_dragging.pageX0);
        var top = my_dragging.offset0.top + (e.pageY - my_dragging.pageY0);
        $(my_dragging.elem)
        .offset({top: top, left: left});
    }
    function handle_mouseup(e){
        $('body')
        .off('mousemove', handle_dragging)
        .off('mouseup', handle_mouseup);
    }
    $('body')
    .on('mouseup', handle_mouseup)
    .on('mousemove', handle_dragging);
}
$('#popup').mousedown(handle_mousedown);

function massPDF(){
	var url = '';
	url = $("input[type=checkbox]").map(function() {
		if (this.checked == true) return this.name;
	}).get().join(",");
	showPopup('pdf.php?id='+url, 800, 600);
}

function delusers() {
	var url = '';
	url = $("input[type=checkbox]").map(function() {
		if (this.checked == true) return this.name;
	}).get().join(",");
	showPopup('delete.php?id='+url, 300, 125);
}

function resetPassword() {
	var url = '';
	url = $("input[type=checkbox]").map(function() {
		if (this.checked == true) return this.name;
	}).get().join(",");
	showPopup('passreset.php?id='+url, 300, 125);
	console.log('passreset.php?id='+url);
}
