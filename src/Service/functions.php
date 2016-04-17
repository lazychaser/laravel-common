<?php

if ( ! function_exists('cyrillic_to_latin'))
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

if ( ! function_exists('normalize_string'))
{
    function normalize_string($value)
    {
        return mb_strtolower(trim($value));
    }
}

if ( ! function_exists('value_if'))
{
    function value_if($bool, $value)
    {
        return $bool ? $value : '';
    }
}

if ( ! function_exists('value_unless'))
{
    function value_unless($bool, $value)
    {
        return $bool ? '' : $value;
    }
}

if ( ! defined('PHONE_REGEX'))
{
    define('PHONE_REGEX', '/^\+[1-9][0-9]{0,2}[0-9]{10}$/');
}

if ( ! function_exists('sanitize_phone'))
{
    /**
     * Sanitize a phone number.`
     *
     * @param string $phone
     *
     * @return string
     */
    function sanitize_phone($phone)
    {
        if (empty($phone)) return null;

        $hasPlus = substr(trim($phone), 0, 1) === '+';

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if ($hasPlus) $phone = '+'.$phone;

        return $phone;
    }
}

if ( ! function_exists('parse_phone'))
{
    /**
     * @param $phone
     *
     * @return array
     */
    function parse_phone($phone)
    {
        $phone = sanitize_phone($phone);

        if ( ! $phone || strlen($phone) < 11) return null;

        $networkCode = substr($phone, -10, 3);
        $phoneNumber = substr($phone, -7);
        $countryCode = substr($phone, 0, -10);

        if ($countryCode = 8) $countryCode = '+7';

        return compact('countryCode', 'networkCode', 'phoneNumber');
    }
}

if ( ! function_exists('format_phone'))
{
    /**
     * @param $phone
     *
     * @return null|string
     */
    function format_phone($phone)
    {
        if ( ! $phone = parse_phone($phone)) return null;

        /**
         * @var string $phoneNumber
         * @var string $countryCode
         * @var string $networkCode
         */
        extract($phone);

        $phoneNumber = substr($phoneNumber, 0, 3).'-'.substr($phoneNumber, 3);

        return $countryCode.' ('.$networkCode.') '.$phoneNumber;
    }
}

if ( ! function_exists('partial_phone'))
{
    /**
     * Get phone partial representation that includes a contry code and last two
     * digits.
     *
     * @param string $phone
     *
     * @return string
     */
    function partial_phone($phone)
    {
        if ($phone = parse_phone($phone))
        {
            return $phone['countryCode'].'********'.substr($phone['phoneNumber'], -2);
        }

        return $phone;
    }
}

if ( ! function_exists('partial_email'))
{
    /**
     * Get a partial representation of the email.
     *
     * @param string $email
     *
     * @return string
     */
    function partial_email($email)
    {
        list($username, $domain) = explode('@', $email);

        $username = substr($username, 0, 2).'***';

        return $username.'@'.$domain;
    }
}

if ( ! function_exists('random_digits'))
{
    /**
     * Generate a number of random digits.
     *
     * @param int $digits
     *
     * @return string
     */
    function random_digits($digits = 5)
    {
        return str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
    }
}

if ( ! function_exists('key_to_name'))
{
    function key_to_name($key)
    {
        return app('html')->keyToName($key);
    }
}

if ( ! defined('YOUTUBE_REGEX'))
{
    define('YOUTUBE_REGEX', '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/');
}

if ( ! defined('YOUTUBE_VIDEO_ID_LENGTH'))
{
    define('YOUTUBE_VIDEO_ID_LENGTH', 11);
}

if ( ! function_exists('get_youtube_video_id'))
{
    /**
     * Get a youtube video id from the url.
     *
     * @param $url
     *
     * @return string|false
     */
    function get_youtube_video_id($url)
    {
        if (preg_match(YOUTUBE_REGEX, $url, $matches) and strlen($matches[2]) == YOUTUBE_VIDEO_ID_LENGTH)
        {
            return $matches[2];
        }

        return false;
    }
}

if ( ! function_exists('url_to'))
{
    /**
     * @param object|null $entity
     * @param array $params
     *
     * @return string|\Kalnoy\LaravelCommon\Http\UrlGenerator
     */
    function url_to($entity = null, array $params = [])
    {
        if (is_null($entity)) return app('url_to');

        return app('url_to')->entity($entity, $params);
    }
}