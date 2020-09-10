phpCollab now uses Composer! Do not clone this repo right into a web directory - it requires a build step. [Learn more](https://phpcollab.github.io/phpcollab/developer/Build-Process.html) or just [download the latest build instead](https://github.com/phpcollab/phpcollab/releases/latest).


phpCollab
===
[![Gitter](https://badges.gitter.im/phpcollab/phpcollab.svg)](https://gitter.im/phpcollab/phpcollab?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![Slack Status](https://slack.phpcollab.com/badge.svg)](https://slack.phpcollab.com)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/93ecaeb6-c941-4bdb-87c9-52209d76ed20/mini.png)](https://insight.sensiolabs.com/projects/93ecaeb6-c941-4bdb-87c9-52209d76ed20)

## Introduction
phpCollab is a project management and collaboration system. Features include: team/client sites, task assignment, document repository/workflow, gantt charts, discussions, calendar, notifications, support requests, weblog newsdesk, invoicing, and many other tools.


### Features
* phpCollab is a project management system. It is comprised of two interfaces:
    * User, which includes admin, employees, co-workers, etc.
    * Client.
* A central login handles everything.
    * If a client logs in, then the client is directed to the client interface.
    * If a team user logs in, then they are directed to the user interface.

#### User Features
* Within each project you have the following features:
    * Project Overview
    * Phases (optional)
    * Tasks & Sub tasks
    * Discussions
    * Team Members
    * Linked Content (uploaded files)
    * Notes
* In addition, you also have a few global features such as:
    * Reports
    * Calendar
    * Search
    * Bookmarks
* One very useful feature is the **Home** page. This page pulls up all the relevant items for that individual user in one place. This is also the first page that a user sees when logging into the system. So, a user can easily determine what tasks are due, or what projects they are involved in.

#### Client Features
* Having a unique interface for your clients to use allows your clients to interact with the project team members.
    * Please note that you must **publish** any of the items mentioned in order for a client to be able to see it.
    * At all times you maintain full control of the project.
* When a client first logs into phpCollab they are presented with a **Home** page that shows all projects that have been created for them. Once a client selects a project they will have the following options:
    * Project Team - a list of users assigned to the project.
    * Team Tasks - all the tasks assigned to the project team
    * Client Tasks - all the tasks assigned to the client
    * Document List - a list of all the documents uploaded in **Linked Content**
    * Bulletin Board - interfaces with the **Discussion** section found in the user interface
    * Support - can submit a task as a support request to the project team

#### Publishing
* By default, a client will not be able to view anything.
    * Throughout a project you have the option of **publishing** items, such as tasks, linked content, team members, etc. Once published, your clients will be able to view the item.
    * You also have the option of **un-publishing** items as well.

### What it does **NOT** do
* phpCollab is a fantastic program that can give you the tools necessary to manage all of your tasks and projects. However, there is one misconception.
    * phpCollab does not publish your web site. phpCollab manages your projects, not your web site.


## Development
To build the project locally, please follow [this guide](https://phpcollab.github.io/phpcollab/developer/Build-Process.html).

## Documentation
https://phpcollab.github.io/phpcollab/

## Installation
Download the [latest release](https://github.com/phpcollab/phpcollab/releases/latest) and refer to the docs/install.md file

Note: Do NOT clone or download the phpCollab codebase directly. That is ONLY required if you wish to contribute to the development of the project.

----

### Big Thanks

Cross-browser Testing Platform and Open Source <3 Provided by [Sauce Labs][sauceLabs] and [<img src="https://phpcollab.github.io/phpcollab/images/browserstack-logo.svg" width="180px" height="32px">][browserStack]

[sauceLabs]: https://saucelabs.com
[browserStack]: http://browserstack.com



