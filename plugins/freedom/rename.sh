find . -type f -name "*" -print0 | while read -r -d '' file; do 
mv "$file" "${file//plugin-name/freedom}" 
done



