#!/bin/sh
#
# Shellscript pour gettext

SRCDIR=$1
FWDIR=$2
OUTFILE=$3

FROM_ENC="ISO-8859-15"

PATH=$PATH:/usr/local/bin:/usr/local/sbin

# gettext
if [ -z "$(which gettext)" ] ; then
  echo "gettext introuvable"
  exit 1
fi

# perl
if [ -z "$(which perl )" ] ; then
  echo "perl introuvable"
  exit 1
fi

# ne pas enlever cette parenthese
(

# parser les fichiers php
xgettext -L PHP --from-code=$FROM_ENC -o - \
  $(find $SRCDIR $FWDIR -type f -iname '*.php')

# parser les fichiers html 
find $SRCDIR/lib/templates/ -type f -iname '*.html' \
| xargs perl -n00 -pi -e '
while( m#{t}([^{]+?){/t}#mgis ) { 
  $a = $1;
  # backslasher les guillemets (si pas deja fait)
  $a =~ s/([^\\])\"/$1\\"/g;
  # encadrer les chaines multilignes
  $a =~ s/\n/\\n\"\n\"/g; 
  # ne pas avoir d antislashes avant les apostrophes
  $a =~ s/\\\x27/\x27/g;
  print STDOUT "#: void:1\nmsgid \"$a\"\nmsgstr \"\"\n\n";  
}' 

# on enleve les msgids pas uniques, on converti en iso8859 et on stocke
) > $OUTFILE

perl -pi -e "s/SOME DESCRIPTIVE TITLE/Onlogistics gettext catalog/" $OUTFILE
perl -pi -e "s/YEAR THE PACKAGE'S COPYRIGHT HOLDER/2007 ATEOR/" $OUTFILE
perl -pi -e "s/FIRST AUTHOR <EMAIL\@ADDRESS>, YEAR/Ateor dev team <dev\@ateor.com> 2007/" $OUTFILE
perl -pi -e "s/FULL NAME <EMAIL\@ADDRESS>/Ateor dev team <dev\@ateor.com>/" $OUTFILE
perl -pi -e "s/LANGUAGE <LL\@li\.org>/Ateor dev team <dev\@ateor.com>/" $OUTFILE
perl -pi -e "s/PACKAGE VERSION/Onlogistics/" $OUTFILE
