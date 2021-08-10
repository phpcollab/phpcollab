<?php


namespace phpCollab\Bookmarks;


abstract class AbstractBookmark
{
    protected $id;
    protected $owner;
    protected $category;
    protected $name;
    protected $url;
    protected $description;
    protected $shared;
    protected $home;
    protected $comments;
    protected $sharedWith;
    protected $created;
    protected $modified;

    public function __construct(int $owner, string $name, string $url)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->url = $url;
    }

    protected function get($prop)
    {
        return $this->$prop;
    }

    protected function set($key, $value)
    {
        $this->$key = $value;
    }
}
