#!/bin/bash

replace_image_url()
{
   cat wp_0.xml.tmp | sed '
1h;
1!H;
/\<src="http:\/\/b/s/\//_/g;
s/?_/__/g;
s/*/_/g;
s/_>/\/>/g;
s/<_/<\//g;
#s/AA\"/AA.jpg\"/g;
s/http:__/http:\/\/img.hackun.me\/tibet\//g
'

}


#--clear output and tmp--
if [ -f wp_0.xml.out ]; then 
	rm wp_0.xml.out
	rm wp_0.xml.tmp
else
	echo 
fi

#--backup--#
cp wp_0.xml wp_0.xml.orig

#--seprate <img to new line--#
cp wp_0.xml wp_0.xml.tmp
sed -in 's/<img/\n<img/g' wp_0.xml.tmp

#--replace thed url--#
replace_image_url >> wp_0.xml.out

#--clear tmp file--#
rm wp_0.xml.tmp
