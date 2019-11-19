search=$1
replace=$2
grep -lr $search |  xargs -n1 -P4 sed -i -e "s/$search/$replace/g"
