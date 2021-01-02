#!/bin/zsh

for line_number; do
    new_kanji=(${(s..)$(sed -n "$(( $line_number + 1 )),/\\$/p" ${XDG_DATA_HOME:-~/.local/share}/radkfile | head -n -1 | tr -d '\n')})
    [[ $kanji ]] || kanji=($new_kanji)
    kanji=(${kanji:*new_kanji})
done

print -l $kanji
