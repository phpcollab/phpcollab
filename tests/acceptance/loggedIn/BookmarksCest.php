<?php
namespace loggedIn;
use \AcceptanceTester;
use Exception;

class BookmarksCest
{
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
     * @skip
     * @param AcceptanceTester $I
     */
    public function createBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Create a new bookmark');
    }

    /**
     * @skip
     * @param AcceptanceTester $I
     */
    public function editBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Edit a bookmark');
//        $I->amOnPage('/bookmarks/viewbookmark.php?id=1');
//        $I->click('//*[@class=\'icons\']/descendant::td[2]/descendant::a');
////        /html/body/form/table[1]/tbody/tr/td[2]/a
//        $I->seeInCurrentUrl('/bookmarks/editbookmark.php?id=1');
    }

    /**
     * @skip
     * @param AcceptanceTester $I
     */
    public function DeleteBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Delete a bookmark');
    }
}
