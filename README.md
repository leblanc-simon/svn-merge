SvnMerge
========

Tools to simplify the merge on a Subversion projects

Installation
------------

* git clone [repository] svn-merge
* cd svn-merge
* composer install
* php -f build-phar.php
* cp svn-merge.phar /usr/bin/svn-merge


Prerequisite
------------

* add configuration file in /etc/svn-merge directory
* see test/config/*.ini to know how create project's configuration file
* you can add ~/.svn-merge/user.ini (for manage username and password) : see test/user/user.ini


Usage
-----

* List all availables commands

```bash
svn-merge
```

* Help for a command

```bash
svn-merge help [command]
```

* Show all availables projects

```bash
svn-merge project:list
```

* Show details for a project

```bash
svn-merge project:show [project name]
```

* Show the svn merge command

```bash
svn-merge command:show [project name] [direction] [commits number]
```

Autocomplete Command Line
-------------------------

Add a file ```svn-merge``` into the directory ```/etc/bash_completion.d/``` with this content :

```bash
_svn_merge() {
    local choices current

    case "$COMP_CWORD" in
        1)
            choices='command:show project:list project:show'
            ;;
        2)
            current_command=${COMP_WORDS[1]}
            case $current_command in
                'command:show')
                    choices=`svn-merge project:list | grep -E -e "^  " | awk '{print $1}'`
                    ;;
                'project:list')
                    choices=()
                    ;;
                'project:show')
                    choices=`svn-merge project:list | grep -E -e "^  " | awk '{print $1}'`
                    ;;
                *)
                    choices=''
                    ;;
            esac
            ;;
        3)
            current_command=${COMP_WORDS[1]}
            current_project=${COMP_WORDS[2]}
            if [ $current_command != 'command:show' ]; then
                choices=''
            else
                choices=`svn-merge project:show  ${current_project} | grep -E -e "^  " | awk '{print $1}'`
            fi
            ;;
        *)
            choices=()
            ;;
    esac

    current=${COMP_WORDS[COMP_CWORD]}
    COMPREPLY=( $(compgen -W '$choices' -- $current) )
    COMP_WORDBREAKS=${COMP_WORDBREAKS//:}
}

complete -F _svn_merge svn-merge
```

Thanks
------

[Symfony Components](http://symfony.com/doc/current/components/index.html) :
* Console
* Finder

License
-------

[MIT](http://opensource.org/licenses/MIT)

Author
------

Simon Leblanc <contact@leblanc-simon.eu>
