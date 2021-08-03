<?php
namespace loggedIn;
use \AcceptanceTester;

/**
 * Class NewsdeskCest
 * @package loggedIn
 *
 * Tests performed as a general user
 */
class NewsdeskCest
{
    private $postId;
    private $commentId;

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

    /**
     * @param AcceptanceTester $I
     */
    public function listPosts(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Newsdesk posts');
        $I->amOnPage('/newsdesk/listnews.php');
        $I->see('News list', ['css' => '.breadcrumbs']);
        $I->see('Newsdesk', ['css' => '.heading']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends listPosts
     */
    public function addPost(AcceptanceTester $I)
    {
        $I->wantTo('Add a newsdesk post');
        $I->amOnPage('/newsdesk/addnews.php');
        $I->see('Add News Article', ['css' => '.heading']);

        $I->submitForm('form', [
            'title' => 'Codeception Article Title',
            'content'  => 'Content created by Codeception',
            'links' => 'www.example.com;https://codeception.com'
        ]);
        // Success : Addition succeeded
        $I->see('Success : Addition succeeded', ['css' => '.message']);
        $I->see('Codeception Article Title', ".//tr/td/*[contains(text(), 'Title')]/ancestor::td/following-sibling::td");
        $this->postId = $I->grabFromCurrentUrl('~id=(\d+)~');
    }

    /**
     * @param AcceptanceTester $I
     * @depends addPost
     */
    public function viewPost(AcceptanceTester $I)
    {
        $I->wantTo('View a newsdesk post');
        $I->amOnPage('/newsdesk/listnews.php');
        $I->see('Newsdesk', ['css' => '.heading']);

        // Find the entry from above
        $I->see('Codeception Article Title', ".//tr/td/a[contains(text(), 'Codeception Article Title')]");
        $I->click(".//tr/td/a[contains(text(), 'Codeception Article Title')]");
        $I->see('Newsdesk', ['css' => '.heading']);
        $I->see('Codeception Article Title', ".//tr/td/*[contains(text(), 'Title')]/ancestor::td/following-sibling::td");
        $I->see('Content created by Codeception', ".//tr/td/*[contains(text(), 'News Body')]/ancestor::td/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewPost
     */
    public function addComment(AcceptanceTester $I)
    {
        $I->wantTo('Add a comment to a news post');
        $I->amOnPage('/newsdesk/viewnews.php?id=' . $this->postId);
        $I->see('Newsdesk', ['css' => '.heading']);
        $I->see('Comments', ['css' => '.heading']);
        $I->amOnPage('/newsdesk/addcomment.php?postid=' . $this->postId);
        $I->see('Add a comment to the News Article', ['css' => '.heading']);
        $I->submitForm('form', [
            'comment' => 'Codeception comment',
            'postId'  => $this->postId,
            'action'  => 'add'
        ]);
        $I->see('Success : Comment added', ['css' => '.message']);
        $I->see('Codeception comment', ['css' => '#clPrc']);
        $this->commentId = preg_replace("/[^0-9s]/", "", $I->grabAttributeFrom('#clPrc table.listing tr:last-child img', 'name'));
    }

    /**
     * @param AcceptanceTester $I
     * @depends addComment
     */
    public function editComment(AcceptanceTester $I)
    {
        $I->wantTo('Edit my news post comment');
        $I->amOnPage('/newsdesk/viewnews.php?id=' . $this->postId);
        $I->see('Codeception comment', ['css' => '#clPrc']);
        $I->amGoingTo('Navigate to the edit view');
        $I->amOnPage('/newsdesk/editcomment.php?postid=' . $this->postId . '&id=' . $this->commentId);
        $I->see('Edit the comment of the News Article :', ['css' => '.heading']);
        $I->submitForm('form', [
            'comment' => 'Codeception comment - edited',
            'postId'  => $this->postId,
            'commentId' => $this->commentId,
            'action'  => 'update'
        ]);
        $I->see('Success : Comment updated', ['css' => '.message']);
        $I->see('Codeception comment - edited', ['css' => '#clPrc']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends editComment
     */
    public function deleteComment(AcceptanceTester $I)
    {
        $I->wantTo('Delete my news post comment');
        $I->amOnPage('/newsdesk/viewnews.php?id=' . $this->postId);
        $I->amOnPage('/newsdesk/viewnews.php?id=' . $this->postId);
        $I->see('Codeception comment', ['css' => '#clPrc']);
        $I->amGoingTo('Navigate to the delete view');
        $I->amOnPage('/newsdesk/deletecomment.php?postid=' . $this->postId . '&id=' . $this->commentId);
        $I->see('Delete the selected comments', ['css' => '.heading']);
        $I->seeElement('.content');
        $I->see('#' . $this->commentId, ['css' => '.leftvalue']);
        $I->see('Codeception comment - edited', ".//tr/td[contains(text(), '#" . $this->commentId . "')]/ancestor::*[position()=1]/following-sibling::tr[1]/td[2]");

        $I->click('Delete');
        $I->see('Success : The Comment of the News Article has been successfully deleted', ['css' => '.message']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends deleteComment
     */
    public function deleteNewsArticle(AcceptanceTester $I)
    {
        $I->wantTo('Delete my News Article');
        $I->amOnPage('/newsdesk/listnews.php');
        $I->see('Newsdesk', ['css' => '.heading']);
        $I->see('Codeception Article Title', ".//tr/td/a[contains(text(), 'Codeception Article Title')]");
        $I->amOnPage('/newsdesk/deletenews.php?id=' . $this->postId);
        $I->see('Delete News Article', ['css' => '.heading']);
        $I->see('#'. $this->postId, ['css' => '.leftvalue']);
        $I->see('Codeception Article Title', ".//tr/td[contains(text(), '#". $this->postId ."')]/following-sibling::td");
        $I->click('Delete');
        $I->see('Success : The News Article has been successfully deleted with all its comments', ['css' => '.message']);
    }
}
