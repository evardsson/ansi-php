ansi-php
========

ANSI character color shortcuts for PHP

USAGE:
````php
<?php
use \evardsson\ansi\ANSI;

$a = new ANSI();
$foreground = ANSI::WHITE;
$background = ANSI::GREEN;
$style = array (
    ANSI::BOLD,
    ANSI::UNDERLINE,
    ANSI::BLINK
    );

$a2 = new ANSI($foreground, $background, $style);
$a2->pline("This should be green background, white text, bright, underlined, and blinking");

````

Constructor
$foreground color either an int value from the constants, or a color name (case is not important, but spelling is - it must be in the color list below)
   OPTION: if your terminal supports 256 colors, you can use any int value from 0 - 255 here. If you are unsure whether your terminal can display 256 colors or are looking for the right color to use, you can call the static function showForegroundColors()

$background color either an int value from the constants, or a color name (case is not important, but spelling is - it must be in the color list below)
   OPTION: if your terminal supports 256 colors, you can use any int value from 0 - 255 here. If you are unsure whether your terminal can display 256 colors or are looking for the right color to use, you can call the static function showBackgroundColors()

$style int value from the list below, or array of ints, or a value name or array of names (case is not important, but spelling is - it must be in the style list below)
   NOTE: Styles do not get applied for extended colors. They are only applied to the default system colors. The only exception is INVERSE which this script emulates in extended color sets by swapping foreground and background colors.

**Colors** for foreground/background must be one of:

| ints         | strings  |
| ANSI::BLACK  | 'black'  |
| ANSI::RED    | 'red'    |
| ANSI::GREEN  | 'green'  |
| ANSI::YELLOW | 'yellow' |
| ANSI::BLUE   | 'blue'   |
| ANSI::PURPLE | 'purple' |
| ANSI::CYAN   | 'cyan'   |
| ANSI::WHITE  | 'white'  |

**Styles** for style must be one of or array of:

| ints                                                                          | strings      |
| ----------------------------------------------------------------------------- | ------------ |
| ANSI::NORMAL - normal (default term) style                                    | 'normal'     |
| ANSI::BOLD - same as BRIGHT, uses brighter value of color                     | 'bold'       |
| ANSI::BRIGHT                                                                  | 'bright'     |
| ANSI::DULL - same as FAINT, uses fainter value of color, not widely supported | 'dull'       |
| ANSI::FAINT                                                                   | 'faint'      |
| ANSI::ITALIC - not widely supported                                           | 'italic'     |
| ANSI::UNDERLINE - underlines text                                             | 'underline'  |
| ANSI::BLINK - same as BLINK_SLOW (< 150 / min)                                | 'blink'      |
| ANSI::BLINK_SLOW                                                              | 'blink_slow' |
| ANSI::BLINK_FAST - same as BLINK_DOS, only supported in MS-DOS pre WinXP      |'blink_fast'  |
| ANSI::BLINK_DOS                                                               | 'blink_dos'  |
| ANSI::INVERSE - switches foreground/background colors                         | 'inverse'    |



