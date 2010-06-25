EXAMPLE_DIR=turing4/example/

.PHONY: deploy

all:
	true

deploy:
	test -n "$(DESTDIR)"
	mkdir -p $(DESTDIR)$(EXAMPLE_DIR)
	cp *.tm $(DESTDIR)$(EXAMPLE_DIR)
