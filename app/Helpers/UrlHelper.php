<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Exception;

class UrlHelper
{
    public static function getUrlMetadata($url)
    {
        $metadata = [
            'title' => $url,
            'description' => '',
            'image' => '',
        ];

        try {
            $response = Http::get($url);
            $html = $response->body();

            $dom = new \DOMDocument();
            @$dom->loadHTML($html);

            $title = $dom->getElementsByTagName('title')->item(0)->textContent ?? '';
            $metaTags = $dom->getElementsByTagName('meta');

            foreach ($metaTags as $meta) {
                if ($meta->getAttribute('name') === 'description') {
                    $metadata['description'] = $meta->getAttribute('content');
                }
                if ($meta->getAttribute('property') === 'og:image') {
                    $metadata['image'] = $meta->getAttribute('content');
                }
            }

            $metadata['title'] = $title ?: $metadata['title']; // Use URL as fallback

        } catch (Exception $e) {
        }

        return $metadata;
    }
}
