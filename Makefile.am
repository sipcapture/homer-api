include config/utility.am
include config/project.am

@PACKAGE@_EXTRA_DIST = \
	api \
	apidoc \
	examples/web \
	examples/mysql \
	examples/sipcapture \
	sql \
	pkg \
	README.md \
	scripts

@PACKAGE@datadir = /var/www/sipcapture/htdocs

install-data-local:
	mkdir -p $(DESTDIR)/var/www/sipcapture/htdocs
	cp -r api apidoc $(DESTDIR)/var/www/sipcapture/htdocs/
	mkdir -p $(DESTDIR)/etc/kamailio
	cp examples/sipcapture/kamailio.cfg $(DESTDIR)/etc/kamailio/kamailio-sipcapture.cfg
	mkdir -p $(DESTDIR)/etc/logrotate.d
	cp examples/sipcapture/sipcapture.logrotated $(DESTDIR)/etc/logrotate.d/sipcapture
	mkdir -p $(DESTDIR)/etc/rsyslog.d
	cp examples/sipcapture/sipcapture.rsyslogd $(DESTDIR)/etc/rsyslog.d/sipcapture.conf
