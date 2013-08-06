# XDCC Parser Project Page 
(dump from [web-archive](http://web.archive.org/web/20120826215828/http://xdccparser.is-fabulo.us/))
### About XDCC Parser

XDCC Parser is a PHP/AJAX script that parses packlists created by iroffer and presents the packlist in a fully searchable web2.0 environment.

XDCC Parser aims to make XDCC packlists more presentable while staying efficient and lightweight.
Although originally developed by DrX, current development is now lead by xshadowfire.

### Current Releases
XDCC Parser v1.1 Single

### Testing
XDCC Parser v1.2 Global - Status: Beta. Please help test it by downloading the beta release.

### Global vs. Single

 * Global searches all bots serverside, and Single only searches one bot at a time clientside.
 * Single is no longer being developed; support is restricted to bugfixes.
 * Global caches packlists, Single fetches in realtime. The reason for this is that it's just not practical to constantly parse a large number of packlists everytime the page is loaded.
 * Single has a box that shows bot stats (slots, speeds, gets, etc).
 * Global has a more advanced search algorithm
 * For more differences between Global and Single, read Global's CHANGELOG from 1.2.0 and on.


### Supported browsers

Internet Explorer 7+, Firefox 3+, Opera 9.6+, Chrome, Safari 4, Midori, most modern browsers

### Feedback/Bug Reports/Development

 * Visit our IRC channel at [#XDCCParser@irc.rizon.net](irc://irc.rizon.net/XDCCParser)
 * Webchat: [open](https://qchat.rizon.net/?nick=xdccparser...&channels=%23XDCCParser&prompt=0)

### FAQ

**Q: This script is retarded, how the hell do you install it?**

*A: We have tried our best to make sure XDCC Parser is error free, but as it is still a relatively new script,
there are bound to be bugs.
Please drop by our IRC channel and we will help you get it installed and fix any bugs for future releases.*

**Q: What happened to Global v1.1?**

*A: XDCC Parser Global v1.1 support was discontinued on 8 July 2009 due to lack of a streamlined install process
and and an increasing number of bugs that were discovered while developing Global v1.2.
Although still in a feature incomplete development stage, v1.2 has proven to be as or even more stable than v1.1.
If you are still interested in Global v1.1 for whatever reason, an archive of (some) releases can be found [here](http://web.archive.org/web/20121018112817/http://xdccparser.is-fabulo.us/old/).*

**Q: I set the `ADMIN_USER` and `ADMIN_PASS` but I can't log in to admin.php**

*A: When PHP is running in CGI (with some hosts such as Dreamhost), `PHP_AUTH_USER` and `PHP_AUTH_PW` are not sent to PHP, causing admin authentication to fail.
To bypass this, remove the authentication codeblock from admin.php and use your webserver's basic authentication for admin.php.*

For more information, visit our IRC channel.

