<?php


namespace phpCollab\Bookmarks;


use InvalidArgumentException;

class Bookmark extends AbstractBookmark
{
    public function __construct(int $owner, string $name, string $url)
    {
        if (empty($owner)) {
            throw new InvalidArgumentException('Owner ID invalid');
        }

        if (empty($name)) {
            throw new InvalidArgumentException('Name is invalid');
        }

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('URL invalid');
        }

        parent::__construct($owner, $name, $url);
    }

    /**
     * @param $prop
     * @return mixed
     */
    public function get($prop)
    {
        return parent::get($prop);
    }

    public function setId(int $id)
    {
        if (!isset($id) || !filter_var($id, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Bookmark ID is invalid');
        }
        parent::set('id', $id);
    }

    /**
     * @param string|null $value
     */
    public function setDescription(string $value = null)
    {
        $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        parent::set('description', $value);
    }

    /**
     * @param string|int $value
     */
    public function setCategory(string $value)
    {
        if (!isset($value)) {
            throw new InvalidArgumentException('Category is invalid');
        }

        if ((string)$value) {
            // New Category
            $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        if ((int)$value) {
            // Existing Category
            $value = filter_var($value, FILTER_VALIDATE_INT);
        }

        parent::set('category', $value);
    }

    /**
     * @param bool $flag
     */
    public function setShared(bool $flag = false)
    {
        parent::set('shared', (int)$flag);
    }

    /**
     * @param bool $flag
     */
    public function setHome(bool $flag = false)
    {
        parent::set('home', (int)$flag);
    }

    /**
     * @param bool $flag
     */
    public function setComments(bool $flag = false)
    {
        parent::set('comments', (int)$flag);
    }

    /**
     * @param string|null $value
     */
    public function setSharedWith(string $value = null)
    {
        parent::set('sharedWith', $value);
    }

}
