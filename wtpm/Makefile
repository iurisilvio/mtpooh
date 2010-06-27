DIRS=turing4

.PHONY: all deploy

all:
	-for d in $(DIRS); do (cd $$d; $(MAKE) $(MFLAGS) all); done

deploy:
	test -n "$(DESTDIR)"
	cp -rf *.php *.js *.css *.html $(DESTDIR)
	cp -rf ext ext-plugins img $(DESTDIR)
	-for d in $(DIRS); do (cd $$d; $(MAKE) $(MFLAGS) deploy); done
