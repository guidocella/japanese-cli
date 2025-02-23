## sdcv

You can use sdcv as a terminal dictionary. Install it from your package manager and download JMDict-ja-en and Kanjidic2 from https://web.archive.org/web/20230808110043/http://download.huzheng.org/ja/ to `~/.local/share/stardict/dic`.

Once you reach a high enough level, you will want the monolingual daijirin dictionary since it has the most information, but this requires some work since it's copyrighted and in an exoteric format:

- Download the KOKUGO Epwing directory from rutracker (the kenkyuusha and kotowaza dictionaries are not worth it in my experience)
- Download Yomichan Import from https://foosoft.net/projects/yomichan-import/, and use it to convert daijirin to JSON
- Extract the resulting zip
- From the directory with the extracted JSON files, execute this repository's `convert-daijirin.php`
- Install Stardict's convertion tools. The AUR has `stardict-tools-git`, but it no longers works due to huzheng.org being dead. On Debian you can `apt install stardict-tools`.
- Execute `stardict-tabfile daijirin.tab` (Arch) / `tabfile daijirin.tab` (Debian)
- Execute `mv daijirin.{dict,idx,ifo} ~/.local/share/stardict/dic`

## Kanji lookup by selecting radicals

This consists of a zsh script that parses the [RADKFILE](http://www.edrdg.org/krad/kradinf.html), and a shell function that lets you select a kanji with fzf, and looks it up in sdcv.

- Download the RADKFILE: `curl http://ftp.edrdg.org/pub/Nihongo/radkfile.gz | gunzip | iconv -f EUC-JP -t UTF-8 -o ${XDG_DATA_HOME:-~/.local/share}/radkfile`
- Copy `radicals.zsh` to `~/.local/lib`
- Define this function in `zshrc`/`bashrc`: `radicals() { ~/.local/lib/radicals.zsh $(awk '/^\$/ {print NR,$2,$3 }' ${XDG_DATA_HOME-~/.local/share}/radkfile | fzf -m --with-nth=2,3 --accept-nth 1 --bind=ctrl-l:jump --preview='~/.local/lib/radicals.zsh {+1}') | fzf --bind=ctrl-l:jump-accept | sdcv --color; }`

You use this by selecting the radicals of the kanji you want to look up and pressing Tab. You can filter the selections by typing the stroke count, and can move down with Ctrl+j or by showing labels you can jump to with Ctrl+l (mnemonic: label). As you select radicals, the preview window will the show the kanji that contain them. Once you find your kanji, press enter, select it in the new fzf instance, and sdcv will show its definition.

## Minimalistic IME

A lightweight alternative to IBus that interacts with Anthy on standard input and output. This converts one word + one particle at a time, so it's only viable if you mostly just read Japanese rather than writing it.

- Download Anthy's .tar.gz archive from https://packages.debian.org/sid/anthy (as the CLI binary in the old Sourceforge version is broken) and cd to the extracted directory.
- Apply `anthy.patch` with `patch -p1 < /path/to/japanese-cli/anthy.patch`. This removes most printf calls and makes it read from standard input
- Execute `./configure && make`
- Install Rust and execute `CARGO_HOME=~/.local/share/cargo cargo install to-kana` for a program that converts romaji to hiragana
- Add a keybinding that uses [fzfmenu](https://github.com/junegunn/fzf/wiki/Examples#fzf-as-dmenu-replacement) or dmenu with the dynamic options patch. An example for Wayland is: `wtype $(fzfmenu --disabled --bind='change:reload(cd /path/to/anthy/test; ~/.local/share/cargo/bin/to-kana hira {q} | ./anthy),ctrl-l:jump-accept' < /dev/null)`
- `change:reload(~/.local/share/cargo/bin/to-kana hira {q})` and `change:reload(~/.local/share/cargo/bin/to-kana kata {q})` can be used to convert romaji to Hiragana and Katana instead

This modified Anthy can then also be used to convert romaji to Japanese programmatically in scripts. For example, this lets you select a Kanji conversion with fzf and shows its translation with sdcv in fzf's preview window: `fzf --disabled --bind='change:reload(cd /path/to/anthy/test; ~/.local/share/cargo/bin/to-kana hira {q} | ./anthy),ctrl-l:jump' --preview='sdcv -n --color {}' --preview-window='85%,border-left,wrap,<60(75%,down,border-top)' < /dev/null`
