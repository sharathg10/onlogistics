# Makefile pour l'i18n dans onlogistics.
# Génère le fichier catalogue merge les fichiers .po et formate les .mo
#
# $Id$

SHELL=/bin/sh
MSGFMT=msgfmt
MSGMERGE=msgmerge
MSGUNIQ=msguniq
DOMAIN=messages
GENSCRIPT=xgettext.sh
FWDIR?=../../../../pwak/trunk/
LANGS=en_GB fr_FR nl_NL de_DE tr_TR pl_PL es_ES
POTFILES=messages.pot

# force l'execution des cibles listées
.PHONY: $(LANGS)

# Regénère le fichier catalogue .pot, merge les fichiers .po, et re-formatte
# les fichiers compilés .mo
all: clean check $(POTFILES) $(LANGS)

# Regénère le fichier catalogue .pot uniquement s'il n'existe pas, merge les
# fichiers .po, et re-formatte les fichiers compilés .mo
update: $(POTFILES) $(LANGS)

# checks necessaires
check:
	@if ! test -d $(FWDIR); then \
		echo "$(FWDIR) does not exist, aborting."; \
		exit 1; \
	fi

# Supprime le fichier catalogue .pot et les fichiers compilés .mo et les
# fichiers backup générés par msgmerge
clean:
	@echo ">> cleaning pot and mo files..."
	@rm -f ./$(DOMAIN).pot
	@for lang in $(LANGS); do \
		rm -f $$lang/LC_MESSAGES/*.mo; \
		rm -f $$lang/LC_MESSAGES/*.po~; \
	done

$(POTFILES):
	@echo ">> generating $@..."
	@$(SHELL) $(GENSCRIPT) .. $(FWDIR) $@
	@$(MSGUNIQ) $(DOMAIN).pot > tmp.pot
	@mv tmp.pot $(DOMAIN).pot

$(LANGS):
	@echo ">> processing $@ language..."
	@po=$@/LC_MESSAGES
	@if ! test -d $@; then \
	    echo "    - lang $@ not installed, installing..."; \
		mkdir -p $@/LC_MESSAGES; \
		if ! test -f $@/LC_MESSAGES/$@.po; then \
			cp $(DOMAIN).pot $@/LC_MESSAGES/$@.po; \
			echo "*.po~" > $(DOMAIN).pot $@/LC_MESSAGES/.cvsignore; \
		fi \
	fi
	@echo "    - merging $@/LC_MESSAGES/$@.po with $(DOMAIN).pot"
	@$(MSGMERGE) -U -N $@/LC_MESSAGES/$@.po $(DOMAIN).pot > /dev/null 2>&1
	@echo "    - building $@/LC_MESSAGES/$@.mo with $@/LC_MESSAGES/$@.po"
	@$(MSGFMT) -f -o $@/LC_MESSAGES/$(DOMAIN).mo $@/LC_MESSAGES/$@.po \
		> /dev/null 2>&1
