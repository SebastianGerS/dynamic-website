<?php

namespace Blog\Domain;

class Blogpost {

    private $id;
    private $user_id;
    private $username;
    private $post_name;
    private $tags;
    private $post_creation_time;
    private $post_edit_time;
    private $content;

    public function getId(): int {
        return $this->id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }
    public function getUsername(): string {
        return $this->username;
    }

    public function getPostName(): string {
        return $this->post_name;
    }

    public function getTags(): string {
        return $this->tags;
    }

    public function getPostCreationTime(): string {
        return $this->post_creation_time;
    }

    public function getPostEditTime(): string {
        return $this->post_edit_time;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function issetPostEditTime() {

       if (isset($this->post_edit_time)) {
           return true;
       }

        return false;

    }
}