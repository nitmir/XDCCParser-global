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
 * @version 1.2.0
 * @author Alex 'xshadowfire' Yu <ayu@xshadowfire.net>
 * @author DrX
 * @copyright 2008-2009 Alex Yu and DrX
 */

function p() {
	this.k = new Array();
	this.lastType = 0;
	this.lastValue = "";
	this.url = "";
	this.init=function(url) {
		this.url=url;
		this.table=document.getElementById('listtable');
		this.status=document.getElementById('status');
		this.searchdiv=document.getElementById('searchdiv');
		this.listname=document.getElementById('listname');
		this.tablehead="<table cellspacing='0' id='listtable'><tr class='animeColumn'><th class='number'>Bot <a href='javascript:p.k.sort(p.botDesc);p.flush();'>&#8593;</a>&nbsp;&nbsp;<a href='javascript:p.k.sort(p.botAsc);p.flush();'>&#8595;</a></th><th class='number'>Pack <a href='javascript:p.k.sort(p.numberDesc);p.flush();'>&#8593;</a>&nbsp;&nbsp;<a href='javascript:p.k.sort(p.numberAsc);p.flush();'>&#8595;</a></th><th class='number'>Size <a href='javascript:p.k.sort(p.sizeDesc);p.flush();'>&#8593;</a>&nbsp;&nbsp;<a href='javascript:p.k.sort(p.sizeAsc);p.flush();'>&#8595;</a></th><th class='name'>Filename <a href='javascript:p.k.sort(p.nameDesc);p.flush();'>&#8593;</a>&nbsp;&nbsp;<a href='javascript:p.k.sort(p.nameAsc);p.flush();'>&#8595;</a></th></tr>";
		this.search();
	};
	this.ajax_init=function() {
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
		if (!this.ajax_request)
			alert("Sorry, your browser is to old. To use this page, please make yourself happier and download a newer browser.");
	};
	this.flush=function() {
		var buffer = this.tablehead;
		if(this.k.length<1) {
			buffer += "<tr class='anime0' id='none' ><td class='none' colspan='4'>No packs found.</td></tr>";
		} else {
			for(i=0;i<this.k.length;i++) {
				var size = (this.k[i]['s']==0) ? "<1" : this.k[i]['s'];
				size += "M";
				buffer += "<tr class='anime"+(i%2)+"' onclick=\"p.genCommand('"+this.k[i]['b']+"',"+this.k[i]['n']+");\"><td class='number'>"+this.k[i]['b']+"</td><td class='number'>"+this.k[i]['n']+"</td><td class='number'>"+size+"</td><td class='name'>"+this.k[i]['f']+"</td></tr>";
			}
		}
		buffer += "</table>";
		this.listname.innerHTML = this.getLastName();
		this.table.innerHTML = buffer;
		this.status.style.display = 'none';
	};
	this.getLastName=function() {
		var name = (this.lastType==1) ? "Search: " : "Bot: ";
		name += this.lastValue;
		return name;
	};
	this.getLastURI=function() {
		var param = (this.lastType==1) ? "?search=" : "?nick=";
		param += this.lastValue;
		prompt('Permalink:',this.url+param);
	};
	this.genCommand=function(nick,pack) {
		prompt('Paste this in your irc client:','/msg '+nick+' xdcc send #'+pack);
	};
	this.search=function() {
		if(document.getElementById('search').value != "" && document.getElementById('search').value != " ") {
			var search = document.getElementById('search').value.replace(/\+/ig,"%2B");
			this.request("t="+search);
			this.lastType = 1;
			this.lastValue = search;
		} else {
			this.table.innerHTML = this.tablehead + "<tr class='anime0' id='start'><td class='none' colspan='4'>Please select a bot or enter search terms to start.</td></tr></table>";
		}
	};
	this.nickPacks=function(nick) {
		this.request("nick="+nick);
		this.lastType = 2;
		this.lastValue = nick;
	};
	this.numberAsc=function(a,b) {
		var x = a.n;
		var y = b.n;
		return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	};
	this.numberDesc=function(a,b) {
		var x = a.n;
		var y = b.n;
		return ((x < y) ? 1 : ((x > y) ? -1 : 0));
	};
	this.sizeAsc=function(a,b) {
		var x = a.s;
		var y = b.s;
		return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	};
	this.sizeDesc=function(a,b) {
		var x = a.s;
		var y = b.s;
		return ((x < y) ? 1 : ((x > y) ? -1 : 0));
	};
	this.nameAsc=function(a,b) {
		var x = a.f.toLowerCase();
		var y = b.f.toLowerCase();
		return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	};
	this.nameDesc=function(a,b) {
		var x = a.f.toLowerCase();
		var y = b.f.toLowerCase();
		return ((x < y) ? 1 : ((x > y) ? -1 : 0));
	};
	this.botAsc=function(a,b) {
		var x = a.b.toLowerCase();
		var y = b.b.toLowerCase();
		return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	};
	this.botDesc=function(a,b) {
		var x = a.b.toLowerCase();
		var y = b.b.toLowerCase();
		return ((x < y) ? 1 : ((x > y) ? -1 : 0));
	};
	this.request=function(request) {
		request = "search.php?"+request;
		this.center_status();
		this.ajax_init();
		this.ajax_request.onreadystatechange = this.ajax_callback;
		this.ajax_request.open("GET",request,true);
		this.ajax_request.send(null);
		this.inited = true;
	};
	this.ajax_callback=function() {
		if(p.ajax_request.readyState == 4 && p.ajax_request.status == 200) {
			p.k = new Array();
			eval(p.ajax_request.responseText);
			p.flush();
		}
	};
	this.center_status=function() {

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
	};
	this.getScrollY=function() {
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
	};
	this.goTop=function() {
		var dx = 0;
		var dy = 0;
		var bx = 0;
		var by = 0;
		if (document.documentElement) {
			dx = document.documentElement.scrollLeft || 0;
			dy = document.documentElement.scrollTop || 0;
		}
		if (document.body) {
			bx = document.body.scrollLeft || 0;
			by = document.body.scrollTop || 0;
		}
		var wx = window.scrollX || 0;
		var wy = window.scrollY || 0;
		var x = Math.max(wx, Math.max(bx, dx));
		var y = Math.max(wy, Math.max(by, dy));
		window.scrollTo(Math.floor(x / 1.5), Math.floor(y / 1.5));
		if(x > 0 || y > 0) {
			window.setTimeout("p.goTop()", 15);
		}
	};
	this.setSkin=function(id) {
		document.getElementById("skin").href = "style"+id+".css";
		var exdate=new Date();
		exdate.setDate(exdate.getDate()+7);
		document.cookie="skin"+"="+escape(id)+";expires="+exdate.toGMTString();
	};
}

var p = new p();

window.onscroll=function() {
	var scrollY = p.getScrollY();
	if(scrollY > 76) {
		p.searchdiv.style.top = (scrollY-79)+"px";
	} else {
		p.searchdiv.style.top = "0px";
	}
};

