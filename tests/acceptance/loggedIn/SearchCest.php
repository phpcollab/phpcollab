<?php
namespace loggedIn;
use AcceptanceTester;
use Exception;

class SearchCest
{
    private $searchTerm = 'test';

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
    public function listSearch(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.xdebug-error');
        $I->dontSeeElement('.xdebug-var-dump');
        $I->dontSee('Fatal error');
        $I->dontSee('Warning');
    }

    /**
     * @param AcceptanceTester $I
     * @depends listSearch
     *
     */
    public function generalSearchNoResults(AcceptanceTester $I)
    {
        $I->wantTo('Perform a general search with no results');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');
        $I->fillField(['name' => 'searchfor'], 'codeception');
        $I->click('button[type="submit"]');
        $I->seeInCurrentUrl('/search/resultssearch.php');
        $I->see('Search results for keywords : codeception', ['css' => '.content']);
        $I->see('The search returned no results.', ['css' => '.content']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearchNoResults
     */
    public function generalSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a general search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');
        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->click('button[type="submit"]');
        $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
        $I->dontSee('The search returned no results.', ['css' => '.content']);
        $I->seeInCurrentUrl('/search/resultssearch.php');
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearch
     * @throws Exception
     */
    public function notesSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Notes search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Notes');
        $I->click('button[type="submit"]');
        try {
            $I->see('The search returned no results.', ['css' => '.content']);
        } catch (Exception $exception) {
            $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
            $I->see('Search Results : Notes', ['css' => '.heading']);
            $I->seeInCurrentUrl('/search/resultssearch.php');
            $I->seeNumberOfElements('.heading', 1);
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearch
     * @throws Exception
     */
    public function clientOrganizationsSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Client Organization search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Client Organizations');
        $I->click('button[type="submit"]');
        try {
            $I->see('The search returned no results.', ['css' => '.content']);
        } catch (Exception $exception) {
            $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
            $I->see('Search Results : Client Organizations', ['css' => '.heading']);
            $I->seeInCurrentUrl('/search/resultssearch.php');
            $I->seeNumberOfElements('.heading', 1);
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearch
     * @throws Exception
     */
    public function projectsSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Projects search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Projects');
        $I->click('button[type="submit"]');
        try {
            $I->see('The search returned no results.', ['css' => '.content']);
        } catch (Exception $exception) {
            $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
            $I->see('Search Results : Projects', ['css' => '.heading']);
            $I->seeInCurrentUrl('/search/resultssearch.php');
            $I->seeNumberOfElements('.heading', 1);
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearch
     * @throws Exception
     */
    public function tasksSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Tasks search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Tasks');
        $I->click('button[type="submit"]');
        try {
            $I->see('The search returned no results.', ['css' => '.content']);
        } catch (Exception $exception) {
            $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
            $I->see('Search Results : Tasks', ['css' => '.heading']);
            $I->seeInCurrentUrl('/search/resultssearch.php');
            $I->seeNumberOfElements('.heading', 1);
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearch
     * @throws Exception
     */
    public function subtasksSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Subtasks search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Subtasks');
        $I->click('button[type="submit"]');
        try {
            $I->see('The search returned no results.', ['css' => '.content']);
        } catch (Exception $exception) {
            $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
            $I->see('Search Results : Subtasks', ['css' => '.heading']);
            $I->seeInCurrentUrl('/search/resultssearch.php');
            $I->seeNumberOfElements('.heading', 1);
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearch
     * @throws Exception
     */
    public function discussionsSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Discussions search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Discussions');
        $I->click('button[type="submit"]');
        try {
            $I->see('The search returned no results.', ['css' => '.content']);
        } catch (Exception $exception) {
            $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
            $I->see('Search Results : Discussions', ['css' => '.heading']);
            $I->seeInCurrentUrl('/search/resultssearch.php');
            $I->seeNumberOfElements('.heading', 1);
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends generalSearch
     * @throws Exception
     */
    public function usersSearch(AcceptanceTester $I)
    {
        $I->wantTo('Perform a Users search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.headingError');

        $I->fillField(['name' => 'searchfor'], $this->searchTerm);
        $I->selectOption('form select[name=heading]', 'Users');
        $I->click('button[type="submit"]');
        try {
            $I->see('The search returned no results.', ['css' => '.content']);
        } catch (Exception $exception) {
            $I->see('Search results for keywords : ' . $this->searchTerm, ['css' => '.content']);
            $I->see('Search Results : Users', ['css' => '.heading']);
            $I->seeInCurrentUrl('/search/resultssearch.php');
            $I->seeNumberOfElements('.heading', 1);
        }
    }
}
