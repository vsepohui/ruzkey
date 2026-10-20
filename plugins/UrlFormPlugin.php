<?php

use Proxy\Config;
use Proxy\Event\ProxyEvent;
use Proxy\Plugin\AbstractPlugin;

function translit($value)
{
	$converter = array(
		'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
		'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
		'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
		'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
		'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
		'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
		'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
 
		'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',    'Д' => 'D',
		'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',   'З' => 'Z',    'И' => 'I',
		'Й' => 'Y',    'К' => 'K',    'Л' => 'L',    'М' => 'M',    'Н' => 'N',
		'О' => 'O',    'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
		'У' => 'U',    'Ф' => 'F',    'Х' => 'H',    'Ц' => 'C',    'Ч' => 'Ch',
		'Ш' => 'Sh',   'Щ' => 'Sch',  'Ь' => '',     'Ы' => 'Y',    'Ъ' => '',
		'Э' => 'E',    'Ю' => 'Yu',   'Я' => 'Ya',
	);
 
	$value = strtr($value, $converter);
	return $value;
}
 

class UrlFormPlugin extends AbstractPlugin
{
    public function onCompleted(ProxyEvent $event)
    {

        $request = $event['request'];
        $response = $event['response'];

        $url = $request->getUri();

        // we attach url_form only if this is a html response
        if (!is_html($response->headers->get('content-type'))) {
			if (preg_match("/^text\/", $response->headers->get('content-type'))) {
				$output = $response->getContent();
				if ($output) {
					$output = translit($output);
				}
				$response->setContent($output);
			}
            return;
        }

        // this path would be relative to index.php that included it?
        $url_form = render_template("./templates/url_form.php", array(
            'url' => $url
        ));
        
        $output = $response->getContent();

        // remove favicon if so
        if (Config::get('replace_icon')) {

            $output = preg_replace_callback('/<link[^>]+rel=".*?(?:shortcut|icon).*?"[^>]+>/', function ($matches) {
                return "";
            }, $output);
        }

        // does the html page contain <body> tag, if so insert our form right after <body> tag starts
        $output = preg_replace('@<body.*?>@is', '$0' . PHP_EOL . $url_form, $output, 1, $count);
        
		$output = translit($output);

        // <body> tag was not found, just put the form at the top of the page
        if ($count == 0) {
            $output = $url_form . $output;
        }

        $response->setContent($output);
    }
}
