<?php

declare(strict_types=1);

use League\CLImate\CLImate;

/* -------------------------------------------------------
                                              _..
                                          .qd$$$$bp.
                                        .q$$$$$$$$$$m.
                                       .$$$$$$$$$$$$$$
                                     .q$$$$$$$$$$$$$$$$
                                    .$$$$$$$$$$$$P\$$$$;
                                  .q$$$$$$$$$P^"_.`;$$$$
                                 q$$$$$$$P;\   ,  /$$$$P
                               .$$$P^::Y$/`  _  .:.$$$/
                              .P.:..    \ `._.-:.. \$P
                              $':.  __.. :   :..    :'
                             /:_..::.   `. .:.    .'|
                           _::..          T:..   /  :
                        .::..             J:..  :  :
                     .::..          7:..   F:.. :  ;
                 _.::..             |:..   J:.. `./
            _..:::..               /J:..    F:.  :
          .::::..                .T  \:..   J:.  /
         /:::...               .' `.  \:..   F_o'
        .:::...              .'     \  \:..  J ;
        ::::...           .-'`.    _.`._\:..  \'
        ':::...         .'  `._7.-'_.-  `\:.   \
         \:::...   _..-'__.._/_.--' ,:.   b:.   \._
          `::::..-"_.'-"_..--"      :..   /):.   `.\
            `-:/"-7.--""            _::.-'P::..    \}
 _....------""""""            _..--".-'   \::..     `.
(::..              _...----"""  _.-'       `---:..    `-.
 \::..      _.-""""   `""""---""                `::...___)
  `\:._.-"""                             GingTeam
------------------------------------------------------- */
require __DIR__.'/vendor/autoload.php';

$cli = new CLImate();

$input = $cli->input('Nhập chuỗi cần mã hóa:');
$text = $input->prompt();

$len = strlen($text);
if (0 === $len) {
    $cli->error('Chuỗi rỗng');
    exit(1);
}

$data = [];
$ctx = (array) count_chars($text, 1);
arsort($ctx);
$n = count($ctx);

foreach ($ctx as $ascii => $v) {
    $data[] = [chr($ascii), $v];
}

$h = 0;
$ntb = 0;
$table = [];
$f = 0;
$expr = fn (int $x): string => sprintf('%d / %d', $x, $len);
foreach ($data as $i => $d) {
    /** @var int[] $d */
    $p = $d[1] / $len;
    $n = (int) log(1 / $p, 2) + 1;
    $pu = $expr($d[1]);
    $fu = $expr($f);
    $h += $p * log(1 / $p, 2);
    $ntb += $n * $p;
    $table[] = [
        'Ký tự' => $d[0],
        'P(u)' => $pu,
        'F(u)' => $fu,
        'N' => $n,
        'Mã hóa' => encode(calc($fu), $n),
    ];
    $f += $d[1];
}

$cli->table($table);
$cli->info('H = '.round($h, 2));
$cli->info('Ntb = '.round($ntb, 2));

/**
 * @internal
 */
function encode(float $f, int $n): string
{
    $result = '';
    for ($i = 0; $i < $n; ++$i) {
        $f *= 2;
        if ($f >= 1) {
            $result .= '1';
            --$f;
        } else {
            $result .= '0';
        }
    }

    return $result;
}

/**
 * @internal
 */
function calc(string $expr): float
{
    return (float) eval('return '.$expr.';');
}
