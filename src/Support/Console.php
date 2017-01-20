<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 22:51
 */

namespace Hanson\Vbot\Support;


use PHPQRCode\QRcode;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class Console
{

    public static function log($str)
    {
        echo $str . PHP_EOL;
    }

    private static function initStyle(OutputInterface $output) {
        $style = new OutputFormatterStyle('black', 'black');
        $output->getFormatter()->setStyle('blackc', $style);
        $style = new OutputFormatterStyle('white', 'white');
        $output->getFormatter()->setStyle('whitec', $style);
    }

    private static function getTTYSize() {
        if(!posix_isatty(STDOUT)){
            return false;
        }
        $ttyName = posix_ttyname(STDOUT);
        $builder = new ProcessBuilder();
        $process = $builder->setPrefix('stty')->setArguments(array('-f', $ttyName, 'size'))->getProcess();
        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            return false;
        }
        $output = $process->getOutput();
        if(!preg_match('~^(\d+)\s+(\d+)$~', $output, $match)) {
            return false;
        }
        return array($match[1], $match[2]);
    }


    public static function showQrCode($text)
    {
        $output = new ConsoleOutput();
        static::initStyle($output);
        $map = array(
            0 => '<whitec>  </whitec>',
            1 => '<blackc>  </blackc>',
        );
        $lrPadding = 1;
        $tbPadding = 0;
        $text   = QRcode::text($text);

        $length = strlen($text[0]);
        $screenSize = static::getTTYSize();
        if(!$screenSize) {
            $output->getErrorOutput()->writeln('<comment>Get Screen Size Failed</comment>');
        } else {
            list($maxLines, $maxCols) = $screenSize;
            $qrCols = 2 * ($length + $lrPadding * 2);
            $qrLines = count($text) + $tbPadding * 2;
            if($qrCols > $maxCols || $qrLines > $maxLines){
                $output->getErrorOutput()->writeln('<error>Max Lines/Columns Reached:请缩小控制台字体大小</error>');
                return;
            }
        }
        $paddingLine = str_repeat($map[0], $length + $lrPadding * 2) . "\n";
        $after = $before = str_repeat($paddingLine, $tbPadding);
        $output->write($before);
        foreach ($text as $line) {
            $output->write(str_repeat($map[0], $lrPadding));
            for ($i = 0; $i < $length; $i++) {
                $type = substr($line, $i, 1);
                $output->write($map[$type]);
            }
            $output->writeln(str_repeat($map[0], $lrPadding));
        }
        $output->write($after);
    }
}