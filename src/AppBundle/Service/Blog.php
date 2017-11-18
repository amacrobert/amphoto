<?php

namespace AppBundle\Service;

class Blog {

    public function __construct($wordpress_client, $guzzle) {
        $this->wordpress_client = $wordpress_client;
        $this->guzzle = $guzzle;
    }

    public function getPosts() {
        return $this->wordpress_client->getPosts();
    }

    public function getPost($post_id) {
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

        return $post;
    }

}
