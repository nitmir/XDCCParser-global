/**
 * XDCC Parser
 * |- Javascript Module
 *
 * This software is free software and you are permitted to
 * modify and redistribute it under the terms of the GNU General
 * Public License version 3 as published by the Free Sofware
 * Foundation.
 *
 * @link http://xdccparser.is-fabulo.us/
 * @version 2.0
 * @author Alex 'xshadowfire' Yu <ayu@xshadowfire.net>
 * @author DrX
 * @copyright 2008-2009 Alex Yu and DrX
 */

var p = {

	b: [], // static bot list
	k: [], // fetched pack list
	t: 0, // type
	v: "", // value
	u: "", // url

	init: function(url) {
		this.u = url;
		w.init();
	},

	search: function() {
		if(arguments[0]) {
			$('search').value = arguments[0];
		}
		if($('search').value.replace(/\s/g,"") !== "") {
			var search = $('search').value.replace(/\+/ig,"%2B");
			w.request("search.php?t="+search);
			this.t = 1;
			this.v = search;
		}
	},

	search_bot: function(id) {
		w.request("search.php?n="+id);
		this.t = 2;
		this.v = id;
	},

	search_flush: function() {

		var buffer = w.tablehead;

		if(this.k.length<1) {
			buffer += "<tr class='anime0' id='none' ><td class='none' colspan='5'>No packs found.</td></tr>";
		} else {
			var length;
			var condensed = false;
			if( this.k.length <= 300 || arguments[0] ) {
				length = this.k.length-1; //-1 because of timestamp at the end
			} else {
				length = 200;
				condensed = true;
			}
			for(i=0;i<length;i++) {
				var size = (this.k[i][2]===0) ? "<1" : this.k[i][2];
				size += "M";
				buffer += "<tr class='anime"+(i%2)+"' onclick=\"p.genCommand("+this.k[i][0]+","+this.k[i][1]+");\"><td class='number'>"+this.b[this.k[i][0]]+"</td><td class='number'>"+this.k[i][1]+"</td><td class='number'>"+size+"</td><td class='number'>"+this.k[i][4]+"</td><td class='name'>"+this.k[i][3]+"</td></tr>";
			}
			// if condensed display show all row
			if( condensed ) {
				buffer += "<tr class='anime"+(i%2)+"' id='none' ><td class='none' colspan='5' onclick='p.search_flush(true);'>Show all "+this.k.length+" packs</td></tr>";
			}
		}

		buffer += "</table>";
		w.render(buffer);

	},

	fetch_page: function(pid) {
		w.request('custompage.php?p='+pid);
		this.t = 3;
		this.v = pid;
	},

	process_page: function(source) {

		var scripts = [];

		while(source.indexOf("<script") > -1 || source.indexOf("</script") > -1) {
			var s = source.indexOf("<script");
			var s_e = source.indexOf(">", s);
			var e = source.indexOf("</script", s);
			var e_e = source.indexOf(">", e);
			scripts.push(source.substring(s_e+1, e));
			source = source.substring(0, s) + source.substring(e_e+1);
		}

		this.title = scripts[0];
		w.render(source);

		for(var i=1; i<scripts.length; i++) {
			try {	
				eval(scripts[i]);
			} catch (ex) {
			}
		}

	},

	getTitle: function() {
		switch(this.t) { 
			case 1: 
				return "Search: " + this.v;
			case 2:
				return "Bot: " + this.b[this.v];
			case 3:
				return this.title || "&nbsp;";
			default:
				return "&nbsp;";
		}
	},

	getLastURI: function() {

		var param = "";

		switch(this.t) { 
			case 1: 
				param = "?search=";
				break;
			case 2:
				param = "?bot=";
				break;
			case 3:
				param = "?page=";
				break;
			default:
				param = "?";
		}

		param += this.v;
		prompt('Permalink:',this.url+param);

	},

	genCommand: function(bid,pack) {
		prompt('Paste this in your irc client:','/msg '+this.b[bid]+' xdcc send #'+pack);
	},


	/**
	 * Comparison functions for sorting
	 */
	botAsc: function(a,b) {
		var a = p.b[a[0]].toLowerCase();
		var b = p.b[b[0]].toLowerCase();
		return ((a < b) ? -1 : ((a > b) ? 1 : 0));
	},
	botDesc: function(a,b) {
		var a = p.b[a[0]].toLowerCase();
		var b = p.b[b[0]].toLowerCase();
		return ((a < b) ? 1 : ((a > b) ? -1 : 0));
	},
	numAsc: function(a,b) {
		return ((a[1] < b[1]) ? -1 : ((a[1] > b[1]) ? 1 : 0));
	},
	numDesc: function(a,b) {
		return ((a[1] < b[1]) ? 1 : ((a[1] > b[1]) ? -1 : 0));
	},
	sizeAsc: function(a,b) {
		return ((a[2] < b[2]) ? -1 : ((a[2] > b[2]) ? 1 : 0));
	},
	sizeDesc: function(a,b) {
		return ((a[2] < b[2]) ? 1 : ((a[2] > b[2]) ? -1 : 0));
	},
	nameAsc: function(a,b) {
		var a = a[3].toLowerCase();
		var b = b[3].toLowerCase();
		return ((a < b) ? -1 : ((a > b) ? 1 : 0));
	},
	nameDesc: function(a,b) {
		var a = a[3].toLowerCase();
		var b = b[3].toLowerCase();
		return ((a < b) ? 1 : ((a > b) ? -1 : 0));
	},
	timeAsc: function(a,b) {
		return ((a[5] < b[5]) ? 1 : ((a[5] > b[5]) ? -1 : 0));
	},
	timeDesc: function(a,b) {
		return ((a[5] < b[5]) ? -1 : ((a[5] > b[5]) ? 1 : 0));
	}

};

var w = {

	init: function() {
		this.maincontent=$('maincontent');
		this.status=$('status');
		this.searchdiv=$('searchdiv');
		this.title=$('maintitle');
		this.tablehead="<table cellspacing='0' class='listtable'><tr class='animeColumn'><th class='number'>Bot <a href='#' onclick='p.k.sort(p.botDesc);p.search_flush();return false;'>&#8593;</a>&nbsp;&nbsp;<a href='#' onclick='p.k.sort(p.botAsc);p.search_flush();return false;'>&#8595;</a></th><th class='number'>Pack <a href='#' onclick='p.k.sort(p.numDesc);p.search_flush();return false;'>&#8593;</a>&nbsp;&nbsp;<a href='#' onclick='p.k.sort(p.numAsc);p.search_flush();return false;'>&#8595;</a></th><th class='number'>Size <a href='#' onclick='p.k.sort(p.sizeDesc);p.search_flush();return false;'>&#8593;</a>&nbsp;&nbsp;<a href='#' onclick='p.k.sort(p.sizeAsc);p.search_flush();return false;'>&#8595;</a></th><th class='number'>Added <a href='#' onclick='p.k.sort(p.timeDesc);p.search_flush();return false'>&#8593;</a>&nbsp;&nbsp;<a href='#' onclick='p.k.sort(p.timeAsc);p.search_flush();return false'>&#8595;</a></th><th class='name'>Filename <a href='#' onclick='p.k.sort(p.nameDesc);p.search_flush();return false;'>&#8593;</a>&nbsp;&nbsp;<a href='#' onclick='p.k.sort(p.nameAsc);p.search_flush();return false;'>&#8595;</a></th></tr>";
		this.maincontent.innerHTML = this.tablehead + "<tr class='anime0' id='start'><td class='none' colspan='5'>Please select a bot or enter search terms to start.</td></tr></table>"; 
	},

	/**
	 * Ajax functions
	 * initialization, request, and callback
	 */
	ajax_init: function() {
		try {
			this.ajax_request = new XMLHttpRequest();
		} catch (trymicrosoft) {
			try {
				this.ajax_request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (othermicrosoft) {
				try {
					this.ajax_request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (failed) {
					this.ajax_request = null;
				}
			}
		}
		if (!this.ajax_request) {
			alert("Sorry, your browser is to old. To use this page, please make yourself happier and download a newer browser.");
		}
	},

	request: function(request) {
		this.center_status();
		this.ajax_init();
		this.ajax_request.onreadystatechange = this.ajax_callback;
		this.ajax_request.open("GET",request,true);
		this.ajax_request.send(null);
	},

	ajax_callback: function() {
		if(w.ajax_request.readyState == 4 && w.ajax_request.status == 200) {
			switch(p.t) {
				case 1:
				case 2:
					delete p.k;
					try {
						p.k = JSON.parse(w.ajax_request.responseText);
					} catch(nojson) {
						p.k = eval(w.ajax_request.responseText);
					}
					p.time = p.k[p.k.length-1];
					delete p.k[p.k.length-1];
					p.search_flush();
					break;
				case 3:
					p.process_page(w.ajax_request.responseText);
					break;

			}
		}
	},

	render: function(content) {
		this.title.innerHTML = p.getTitle();
		this.maincontent.innerHTML = content;
		this.status.style.display = 'none';
		onscroll();		
	},

	goTop: function() {

		var x = this.getScrollX();
		var y = this.getScrollY();

		window.scrollTo(Math.floor(x / 1.5), Math.floor(y / 1.5));

		if(x > 0 || y > 0) {
			window.setTimeout("w.goTop()", 15);
		}

	},

	center_status: function() {

		var my_width  = 0;
		var my_height = 0;

		if ( typeof( window.innerWidth ) == 'number' ) {
			my_width  = window.innerWidth;
			my_height = window.innerHeight;
		} else if ( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
			my_width  = document.documentElement.clientWidth;
			my_height = document.documentElement.clientHeight;
		} else if ( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
			my_width  = document.body.clientWidth;
			my_height = document.body.clientHeight;
		}

		this.status.style.position = 'absolute';
		this.status.style.display  = 'block';
		this.status.style.zIndex   = -1;

		var divheight = parseInt( this.status.style.height, 10 ) ? parseInt( this.status.style.height, 10 ) : parseInt( this.status.offsetHeight, 10 );
		var divwidth  = parseInt( this.status.style.width, 10 )  ? parseInt( this.status.style.width, 10 )  : parseInt( this.status.offsetWidth, 10 );
	
		divheight = divheight ? divheight : 200;
		divwidth  = divwidth  ? divwidth  : 400;
	
		var scrollY = this.getScrollY();

		var setX = ( my_width  - divwidth  ) / 2;
		var setY = ( my_height - divheight ) / 2 + scrollY;
		setX = ( setX < 0 ) ? 0 : setX;
		setY = ( setY < 0 ) ? 0 : setY;
	
		this.status.style.left = setX + "px";
		this.status.style.top  = setY + "px";
		this.status.style.zIndex = 99;

	},

	getScrollX: function() {

		var scrollX = 0;

		if ( document.documentElement && document.documentElement.scrollLeft ) {
			scrollX = document.documentElement.scrollLeft;
		} else if ( document.body && document.body.scrollLeft ) {
			scrollX = document.body.scrollLeft;
		} else if ( window.pageXOffset ) {
			scrollX = window.pageXOffset;
		} else if ( window.scrollX ) {
			scrollX = window.scrollX;
		}

		return scrollX;

	},

	getScrollY: function() {

		var scrollY = 0;

		if ( document.documentElement && document.documentElement.scrollTop ) {
			scrollY = document.documentElement.scrollTop;
		} else if ( document.body && document.body.scrollTop ) {
			scrollY = document.body.scrollTop;
		} else if ( window.pageYOffset ) {
			scrollY = window.pageYOffset;
		} else if ( window.scrollY ) {
			scrollY = window.scrollY;
		}

		return scrollY;

	}

};

function $(element) {
	if (arguments.length > 1) {
		for(var i = 0, elements = [], length = arguments.length; i < length; i++)
			elements.push($(arguments[i]));
		return elements;
	}
	if(typeof element == 'string')
		return document.getElementById(element);
	return null;
};

function onscroll() {
	var scrollY = w.getScrollY();
	if(scrollY > 76) {
		w.searchdiv.style.top = (scrollY-79)+"px";
	} else {
		w.searchdiv.style.top = "0px";
	}
};

if( window.addEventListener ) {
	window.addEventListener( "scroll", onscroll, false );
} else {
	window.attachEvent( "onscroll", onscroll );
}
