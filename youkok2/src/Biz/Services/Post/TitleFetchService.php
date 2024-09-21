<?php
namespace Youkok\Biz\Services\Post;

class TitleFetchService
{
    const int CURL_CONNECTION_TIMEOUT_IN_SECONDS = 5;
    const int CURL_TIMEOUT_IN_SECONDS = 8;
    const int CURL_MAX_REDIRECTS = 5;

    public function run(string $url): ?string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, static::CURL_MAX_REDIRECTS);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, static::CURL_CONNECTION_TIMEOUT_IN_SECONDS);
        curl_setopt($ch, CURLOPT_TIMEOUT, static::CURL_TIMEOUT_IN_SECONDS);

        $output = curl_exec($ch);
        curl_close($ch);

        preg_match("#<title>(.*)</title>#im", $output, $matches);

        if (count($matches) > 0 and mb_strlen($matches[1]) > 0) {
            return $matches[1];
        }

        return null;
    }
}
