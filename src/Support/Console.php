<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 22:51
 */

namespace Hanson\Vbot\Support;


use Carbon\Carbon;
use PHPQRCode\QRcode;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 控制台处理类
 *
 * Class Console
 * @package Hanson\Vbot\Support
 */
class Console
{

    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const MESSAGE = 'MESSAGE';

    /**
     * 输出字符串
     *
     * @param $str
     * @param string $level
     */
    public static function log($str, $level = 'INFO')
    {
        echo '[' . Carbon::now()->toDateTimeString() . ']' . "[{$level}] " . $str . PHP_EOL;
    }

    /**
     * debug 模式下调试输出
     *
     * @param $str
     */
    public static function debug($str)
    {
        if (server()->config['debug']) {
            static::log($str, 'DEBUG');
        }
    }

    /**
     * 初始化二维码style
     *
     * @param OutputInterface $output
     */
    private static function initQrcodeStyle(OutputInterface $output) {
        $style = new OutputFormatterStyle('black', 'black', array('bold'));
        $output->getFormatter()->setStyle('blackc', $style);
        $style = new OutputFormatterStyle('white', 'white', array('bold'));
        $output->getFormatter()->setStyle('whitec', $style);
    }


    /**
     * 控制台显示二维码
     *
     * @param $text
     */
    public static function showQrCode($text)
    {
        $output = new ConsoleOutput();
        static::initQrcodeStyle($output);

        if(System::isWin()){
            $pxMap = ['<whitec>mm</whitec>', '<blackc>  </blackc>'];
            system('cls');
        }else{
            $pxMap = ['<whitec>  </whitec>', '<blackc>  </blackc>'];
            system('clear');
        }

        $text   = QRcode::text($text);

        $length = strlen($text[0]);

        foreach ($text as $line) {
            $output->write($pxMap[0]);
            for ($i = 0; $i < $length; $i++) {
                $type = substr($line, $i, 1);
                $output->write($pxMap[$type]);
            }
            $output->writeln($pxMap[0]);
        }
    }

    /**
     * 获取命令行参数
     *
     * @return array
     */
    public static function getParams()
    {
        return getopt("", ["session:"]);
    }
}