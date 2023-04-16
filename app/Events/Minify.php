<?php

namespace App\Events;

use CodeIgniter\Events\Events;

class Minify
{
    public static function minifyOutput()
    {
        $output = ob_get_contents();

        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $output = gzencode($output, 9);
            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($output));
        }

        echo $output;
    }

    public static function minifyHTML()
    {
        $output = ob_get_contents();

        $output = preg_replace('/\s{2,}/', ' ', $output);
        $output = preg_replace('/<!--([^\[|(<!)].*)/', '', $output);
        $output = preg_replace('/(?<!\S)\/\/\s*[^\r\n]*/', '', $output);
        $output = preg_replace('/\s*\n/', '', $output);

        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $output = gzencode($output, 9);
            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($output));
        }

        echo $output;
    }

    public static function minifyJS()
    {
        $output = ob_get_contents();

        $output = preg_replace('/^\s+|\s+$/m', '', $output);
        $output = preg_replace('/\r|\n/', '', $output);
        $output = preg_replace('/\s{2,}/', ' ', $output);
        $output = preg_replace('/\/\*.*?\*\//', '', $output);
        $output = preg_replace('/\bfunction\b/', 'function ', $output);

        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $output = gzencode($output, 9);
            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($output));
        }

        echo $output;
    }

    public static function init()
    {
        if (config('App')->minifyOutput) {
            ob_start();
            Events::on('post_controller', function () {
                if (stripos($_SERVER['HTTP_USER_AGENT'], 'bot') === false && !empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
                    switch (true) {
                        case strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false:
                            ob_end_flush();
                            ob_start('Minify::minifyOutput', 1);
                            break;
                        case strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false:
                            ob_end_clean();
                            header('Content-Encoding: deflate');
                            header('Vary: Accept-Encoding');
                            ob_start('gzdeflate');
                            break;
                        default:
                            ob_end_flush();
                            break;
                    }
                }
            });
        }

        if (config('App')->minifyHTML) {
            Events::on('post_controller_constructor', function () {
                ob_start('\App\Events\Minify::minifyHTML', 1);
            });
        }

        if (config('App')->minifyJS) {
            Events::on('post_controller_constructor', function () {
				// ob_start('\App\Events\Minify::minifyJS', 1);
			});
		}
	}
}