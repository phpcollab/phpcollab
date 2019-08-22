<?php
namespace loggedIn;
use \AcceptanceTester;
use Codeception\Util\Locator;
use Exception;

class BookmarksCest
{
    protected $bookmarkName;

    public function __construct() {
        $this->bookmarkName = "Codeception Bookmark Tests";
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'testUser');
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
     */
    public function viewBookmark(AcceptanceTester $I)
    {
        $I->wantTo('View a Bookmark');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=all');
        $I->seeInTitle('View All Bookmarks');
        $I->seeElement('.listing');
        $I->click('.listing tr:nth-child(2) td:nth-child(2) a');
        $I->seeElement('.content');
        $I->see('Info');
        $I->see('Name :');
        $I->see('URL :');
        $I->see('Description :');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function createBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Create a new bookmark');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add Bookmark');
        $I->seeElement('form', ['name' => 'booForm']);
        $I->fillField('name', $this->bookmarkName);
        $I->fillField('url', 'www.codeception.com');
        $I->click('Save');
        $I->see('Success : Addition succeeded');
        $I->see($this->bookmarkName);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function createBookmarkWithDescription(AcceptanceTester $I)
    {
        $I->wantTo('Create a new bookmark with a description');
        $I->amOnPage('/bookmarks/editbookmark.php');
        $I->seeInTitle('Add Bookmark');
        $I->seeElement('form', ['name' => 'booForm']);
        $I->fillField('name', $this->bookmarkName . " - copy");
        $I->fillField('url', 'www.codeception.com');
        $I->fillField('form textarea[name=description]', 'This is a bookmark description');
        $I->click('Save');
        $I->see('Success : Addition succeeded');
        $I->see($this->bookmarkName);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function editBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Edit a bookmark');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->see($this->bookmarkName);
        $I->seeElement('.listing');
        $I->click(Locator::contains('a', $this->bookmarkName));
        $I->seeElement('.content');
        $I->see('Info');
        $I->see('Name :');
        $bookmark_id = $I->grabFromCurrentUrl('~id=(\d+)~');
        $I->amOnPage('/bookmarks/editbookmark.php?id=' . $bookmark_id);
        $I->see("Edit bookmark : " . $this->bookmarkName);
        $I->fillField('name', $this->bookmarkName . " - edited");
        $I->click('Save');
        $I->see('Success : Modification succeeded');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->see($this->bookmarkName . " - edited");
    }

    /**
     * @param AcceptanceTester $I
     */
    public function deleteBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Delete a bookmark');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->see($this->bookmarkName);
        $I->seeElement('.listing');
        $I->click(Locator::contains('a', $this->bookmarkName));
        $I->seeElement('.content');
        $I->see('Info');
        $I->see('Name :');
        $bookmark_id = $I->grabFromCurrentUrl('~id=(\d+)~');
        $I->amOnPage('/bookmarks/deletebookmarks.php?id=' . $bookmark_id);
        $I->see("Delete bookmarks");
        $I->seeElement('.content');
        $I->see('#' . $bookmark_id);
        $I->see($this->bookmarkName);
        $I->click('Delete');
        $I->see('Success : Deletion succeeded');
    }
}
