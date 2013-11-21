#!/bin/bash

DISTFILES="Memo.pdf use-cases.pdf cli diagrams app htdocs sql tests"
DISTNAME=Kevin-Brandstatter-PR

mkdir $DISTNAME
for i in $DISTFILES; do
   cp -r $i $DISTNAME
done

tar -cjf ${DISTNAME}.tar.bz2 $DISTNAME

rm -r $DISTNAME
