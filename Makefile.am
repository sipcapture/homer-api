include config/utility.am
include config/project.am

@PACKAGE@_EXTRA_DIST = \
	api \
	apidoc \
	examples/web \
	examples/mysql \
	sql \
	pkg \
	README.md \
	scripts/sipcapture_configure

@PACKAGE@datadir = /var/www/sipcapture/htdocs

install-data-local:
	mkdir -p $(DESTDIR)/var/www/sipcapture/htdocs
	cp -r api apidoc $(DESTDIR)/var/www/sipcapture/htdocs/
