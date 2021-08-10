<?php

namespace loggedIn;

use AcceptanceTester;
use Codeception\Util\Locator;
use Exception;

class BookmarksCest
{
    protected $bookmarkName;
    protected $bookmarkId;

    public function __construct()
    {
        $this->bookmarkName = "Codeception Bookmark Tests";
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'testAdmin');
        $I->fillField(['name' => 'passwordForm'], 'testing');
        $I->click('input[type="submit"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    /**
     * @param AcceptanceTester $I
     */
    public function listAllBookmarks(AcceptanceTester $I)
    {
        $I->wantTo('See a list of all bookmarks');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=all');
        $I->seeInTitle('View All Bookmarks');
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function listMyBookmarks(AcceptanceTester $I)
    {
        $I->wantTo('See a list of my bookmarks');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->seeInTitle('View My Bookmarks');
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function listPrivateBookmarks(AcceptanceTester $I)
    {
        $I->wantTo('See a list of private bookmarks');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=private');
        $I->seeInTitle('View Private Bookmarks');
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends listAllBookmarks
     */
    public function createBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Create a new bookmark');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add bookmark');
        $I->seeElement('form', ['name' => 'bookmarkForm']);
        $I->fillField('name', $this->bookmarkName);
        $I->fillField('url', 'https://www.codeception.com');
        $I->click('Save');
        $I->see('Success : Bookmark created', ['css' => '.message']);
        $I->see($this->bookmarkName, ['css' => '.listing']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends createBookmark
     */
    public function viewBookmark(AcceptanceTester $I)
    {
        $I->wantTo('View a Bookmark');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=all');
        $I->seeInTitle('View All Bookmarks');
        $I->seeElement('.listing');
        $I->click('//a[text()="' . $this->bookmarkName . '"]');
        $I->seeElement('.content');
        $I->see('Info', ['css' => '.content']);
        $I->see($this->bookmarkName, ['css' => '.content']);
        $I->see('Description :', ['css' => '.content']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends listAllBookmarks
     */
    public function createBookmarkWithoutNameAndUrl(AcceptanceTester $I)
    {
        $I->wantTo('See an error when creating a new bookmark with a blank name and URL');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add bookmark');
        $I->seeElement('form', ['name' => 'bookmarkForm']);
        $I->fillField('name', '');
        $I->fillField('url', '');
        $I->click('Save');
        $I->see('Errors found!', ".headingError");
        $I->see('Please enter a name and URL for the bookmark', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends listAllBookmarks
     */
    public function createBookmarkWithoutName(AcceptanceTester $I)
    {
        $I->wantTo('See an error when creating a new bookmark with a blank name');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add bookmark');
        $I->seeElement('form', ['name' => 'bookmarkForm']);
        $I->fillField('name', '');
        $I->fillField('url', 'www.codeception.com');
        $I->click('Save');
        $I->see('Errors found!', ['css' => '.headingError']);
        $I->see('Please enter a name for the bookmark', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends listAllBookmarks
     */
    public function createBookmarkWithoutUrl(AcceptanceTester $I)
    {
        $I->wantTo('See an error when creating a new bookmark without a URL');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add bookmark');
        $I->seeElement('form', ['name' => 'bookmarkForm']);
        $I->fillField('name', $this->bookmarkName);
        $I->fillField('url', '');
        $I->click('Save');
        $I->see('Errors found!', ".headingError");
        $I->see('Please enter a URL for the bookmark', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends listAllBookmarks
     */
    public function createBookmarkWithInvalidUrl(AcceptanceTester $I)
    {
        $I->wantTo('See an error when creating a new bookmark with an invalid URL');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add bookmark');
        $I->seeElement('form', ['name' => 'bookmarkForm']);
        $I->fillField('name', $this->bookmarkName);
        $I->fillField('url', 'www.codeception.com');
        $I->click('Save');
        $I->see('Errors found!', ".headingError");
        $I->see('Please enter a valid URL for the bookmark', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends listAllBookmarks
     */
    public function createBookmarkWithDescription(AcceptanceTester $I)
    {
        $I->wantTo('Create a new bookmark with a description');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add bookmark');
        $I->seeElement('form', ['name' => 'bookmarkForm']);
        $I->fillField('name', $this->bookmarkName . " - description");
        $I->fillField('url', 'https://www.codeception.com');
        $I->fillField('form textarea[name=description]', 'This is a bookmark description');
        $I->click('Save');
        $I->see('Success : Bookmark created', ['css' => '.message']);
        $I->seeLink($this->bookmarkName . ' - description');
    }

    /**
     * @param AcceptanceTester $I
     * @depends createBookmark
     */
    public function editBookmarkNoName(AcceptanceTester $I)
    {
        $I->wantTo('See error when editing a Bookmark with empty name field');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->see($this->bookmarkName, ['css' => '.listing']);
        $I->seeElement('.listing');
        $I->click(Locator::contains('a', $this->bookmarkName));
        $I->seeElement('.content');
        $I->see('Info', ['css' => '.content']);
        $I->see('Name :', ['css' => '.content']);
        $this->bookmarkId = $I->grabFromCurrentUrl('~id=(\d+)~');
        $I->amOnPage('/bookmarks/editbookmark.php?id=' . $this->bookmarkId);
        $I->see("Edit bookmark : " . $this->bookmarkName, ['css' => '.heading']);
        $I->fillField('name', "");
        $I->click('Save');
        $I->see('Errors found!', '.headingError');
        $I->see('Please enter a name for the bookmark', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends createBookmark
     */
    public function editBookmarkNoUrl(AcceptanceTester $I)
    {
        $I->wantTo('See error when editing a Bookmark with empty url field');
        $I->amOnPage('/bookmarks/editbookmark.php?id=' . $this->bookmarkId);
        $I->see("Edit bookmark : " . $this->bookmarkName, ['css' => '.heading']);
        $I->fillField('url', '');
        $I->click('Save');
        $I->see('Errors found!', '.headingError');
        $I->see('Please enter a URL for the bookmark', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends createBookmark
     */
    public function editBookmarkWithNoNameAndUrl(AcceptanceTester $I)
    {
        $I->wantTo('See error when editing a Bookmark with empty name and epmty url fields');
        $I->amOnPage('/bookmarks/editbookmark.php?id=' . $this->bookmarkId);
        $I->see("Edit bookmark : " . $this->bookmarkName, ['css' => '.heading']);
        $I->fillField('name', '');
        $I->fillField('url', '');
        $I->click('Save');
        $I->see('Errors found!', ['css' => '.headingError']);
        $I->see('Please enter a name and URL for the bookmark', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends createBookmark
     */
    public function editBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Edit a bookmark');
        $I->amOnPage('/bookmarks/editbookmark.php?id=' . $this->bookmarkId);
        $I->see("Edit bookmark : " . $this->bookmarkName, ['css' => '.heading']);
        $I->fillField('name', $this->bookmarkName . " - edited");
        $I->click('Save');
        $I->see('Success : Bookmark updated', ['css' => '.message']);
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->seeLink($this->bookmarkName . ' - edited');
    }

    /**
     * @param AcceptanceTester $I
     * @depends createBookmark
     */
    public function deleteBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Delete a bookmark');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->see($this->bookmarkName, ['css' => '.listing']);
        $I->seeElement('.listing');
        $I->click(Locator::contains('a', $this->bookmarkName));
        $I->seeElement('.content');
        $I->see('Info', ['css' => '.content']);
        $I->see('Name :', ['css' => '.content']);
        $bookmark_id = $I->grabFromCurrentUrl('~id=(\d+)~');
        $I->amOnPage('/bookmarks/deletebookmarks.php?id=' . $bookmark_id);
        $I->see("Delete bookmarks", ['css' => '.heading']);
        $I->seeElement('.content');
        $I->see('#' . $bookmark_id, ['css' => '.content']);
        $I->see($this->bookmarkName, ".//tr/td[contains(text(),'#" . $bookmark_id . "')]/following-sibling::td");
        $I->click('Delete');
        $I->see('Success : Deletion succeeded', ['css' => '.message']);
    }
}
