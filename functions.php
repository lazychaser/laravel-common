<?php

if ( ! function_exists('cyrrilic_to_latin'))
{
    function cyrillic_to_latin($string)
    {
        $converter =
        [
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'ts',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'Ts',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        ];

        return strtr($string, $converter);
    }
}

if ( ! function_exists('columnize'))
{
    function columnize($items, $columns)
    {
        $result = [];

        foreach ($items as $i => $item)
        {
            $result[$i % $columns][] = $item;
        }

        return $result;
    }
}

if ( ! function_exists('vary_parameter'))
{
    function vary_parameter($parameters, $parameter, $value, $default = null)
    {
        if ($value == $default)
        {
            unset($parameters[$parameter]);
        }
        else
        {
            $parameters[$parameter] = $value;
        }

        return $parameters;
    }
}

if ( ! function_exists('cache_key'))
{
    function cache_key(array $options)
    {
        ksort($options);

        return md5(http_build_query($options));
    }
}