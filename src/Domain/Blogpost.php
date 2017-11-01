<?php

namespace Blog\Domain;

class Blogpost {

    private $id;
    private $user_id;
    private $post_name;
    private $post_time;
    private $content;

    public function getId(): int {
        return $this->id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getPostName(): string {
        return $this->post_name;
    }

    public function getPostTime(): datetime {
        return $this->post_time;
    }

    public function getContent(): string {
        return $this->content;
    }
}