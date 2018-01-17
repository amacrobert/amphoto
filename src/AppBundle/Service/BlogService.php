<?php

namespace AppBundle\Service;

class BlogService {

    private $wordpress_client;
    private $guzzle;
    private $cache;

    public function __construct($wordpress_client, $guzzle, $cache) {
        $this->wordpress_client = $wordpress_client;
        $this->guzzle = $guzzle;
        $this->cache = $cache;
    }

    public function getPosts() {
        return $this->wordpress_client->getPosts();
    }

    public function getPost($post_id) {

        $cache_item = $this->cache->getItem('blog.' . $post_id);

        if ($cache_item->isHit()) {
            return $cache_item->get();
        }

        $post = (object)$this->wordpress_client->getPost($post_id);

        $featured_image = isset($post->post_thumbnail['link']) ? $post->post_thumbnail['link'] : null;
        $featured_image = str_replace('https://', 'http://', $featured_image);
        $post->featured_image = $featured_image;

        // Whitespace
        $post->post_content = str_replace('&nbsp;', ' ', $post->post_content);

        // Convert WordPress captions to HTML
        $post->post_content = preg_replace("/\s*\[caption(.*?)\](<img(.*?)>)(.*?)\[\/caption\]\s*/", PHP_EOL.PHP_EOL."<img $3><div class='caption'>$4</div>", $post->post_content);

        // Convert instagram objects to HTML
        $post->post_content = preg_replace_callback(
            "/\s*\[instagram url=(.*?)\]\s*/",
            function($match) {
                try {
                    $response = $this->guzzle->get('https://api.instagram.com/oembed?url=' . $match[1]);
                    if ($response->getStatusCode() !== 200) {
                        throw new \Exception;
                    }
                    $body = json_decode($response->getBody());
                    return '<p>'.$body->html.'</p>';
                }
                catch (\Exception $e) {
                    return '<p>[Error loading instagram content]</p>';
                }
            },
            $post->post_content
        );

        // Convert vimeo objects to HTML
        $post->post_content = preg_replace_callback(
            "/\s*\[vimeo url=(.*?)\]\s*/",
            function($match) {
                try {
                    $response = $this->guzzle->get('https://vimeo.com/api/oembed.json?url=' . $match[1]);
                    if ($response->getStatusCode() !== 200) {
                        throw new \Exception;
                    }
                    $body = json_decode($response->getBody());
                    return '<p>'.$body->html.'</p>';
                }
                catch (\Exception $e) {
                    return '<p>[Error loading vimeo content]</p>';
                }
            },
            $post->post_content
        );

        $cache_item->set($post)->expiresAfter(3600);
        $this->cache->save($cache_item);

        return $post;
    }

}
