<?php

namespace Hanson\Vbot\Console;

use PHPQRCode\QRcode as QrCodeConsole;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class QrCode extends Console
{
    /**
     * show qrCode on console.
     *
     * @param $text
     *
     * @return bool
     */
    public function show($text)
    {
        if (!array_get($this->config, 'qrcode', true)) {
            return false;
        }
        $output = new ConsoleOutput();
        static::initQrcodeStyle($output);

        $pxMap[0] = Console::isWin() ? '<whitec>mm</whitec>' : '<whitec>  </whitec>';
        $pxMap[1] = '<blackc>  </blackc>';

        $text = QrCodeConsole::text($text);

        $length = strlen($text[0]);

        $output->write("\n");
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
     * init qrCode style.
     *
     * @param OutputInterface $output
     */
    private static function initQrcodeStyle(OutputInterface $output)
    {
        $style = new OutputFormatterStyle('black', 'black', ['bold']);
        $output->getFormatter()->setStyle('blackc', $style);
        $style = new OutputFormatterStyle('white', 'white', ['bold']);
        $output->getFormatter()->setStyle('whitec', $style);
    }
}
