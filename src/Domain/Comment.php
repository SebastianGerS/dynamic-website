<?php

namespace Blog\Domain;

class Comment {

    private $id;
    private $post_id;
    private $user_id;
    private $post_creation_time;
    private $content;

    public function getId(): int {
        return $this->id;
    }

    public function getPostId(): int {
        return $this->post_id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getPostCreationTime(): string {
        return $this->post_creation_time;
    }

    public function getContent(): string {
        return $this->content;
    }
}