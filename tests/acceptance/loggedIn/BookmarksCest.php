<?php
namespace loggedIn;
use \AcceptanceTester;

class BookmarksCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'testUser');
        $I->fillField(['name' => 'passwordForm'], 'testing');
        $I->click('input[type="submit"]');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function listAllBookmarks(AcceptanceTester $I)
    {
        $I->wantTo('See a list of all bookmarks');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=all');
        $I->seeInTitle('View All Bookmarks');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    public function listMyBookmarks(AcceptanceTester $I)
    {
        $I->wantTo('See a list of my bookmarks');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=my');
        $I->seeInTitle('View My Bookmarks');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    public function listPrivateBookmarks(AcceptanceTester $I)
    {
        $I->wantTo('See a list of private bookmarks');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=private');
        $I->seeInTitle('View Private Bookmarks');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    public function viewBookmark(AcceptanceTester $I)
    {
        $I->wantTo('View a newsdesk post');
        $I->amOnPage('/bookmarks/listbookmarks.php?view=all');
        $I->seeInTitle('View All Bookmarks');
        $I->seeElement('.listing');
        $I->click('//*[@class=\'listing\']/descendant::tr[2]/descendant::td[2]/descendant::a');
        $I->seeElement('.content');
        $I->see('Info');
        $I->see('Name :');
        $I->see('URL :');
        $I->see('Description :');
    }

    public function createBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Create a new bookmark');
    }
/*
    public function editBookmark(AcceptanceTester $I)
    {
        $I->wantTo('Edit a bookmark');
        $I->amOnPage('/bookmarks/viewbookmark.php?id=1');
        $I->click('//*[@class=\'icons\']/descendant::td[2]/descendant::a');
//        /html/body/form/table[1]/tbody/tr/td[2]/a
        $I->seeInCurrentUrl('/bookmarks/editbookmark.php?id=1');
    }
*/
}
