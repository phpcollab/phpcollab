<?php


namespace phpCollab\Topics;

use Exception;
use InvalidArgumentException;
use phpCollab\Container;
use phpCollab\Database;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Topics
 * @package phpCollab\Topics
 */
class Topics
{
    protected $topics_gateway;
    protected $db;
    protected $container;
    protected $projects;
    protected $teams;
    protected $notifications;
    protected $strings;
    protected $root;

    /**
     * Topics constructor.
     * @param Database $database
     * @param Container $container
     */
    public function __construct(Database $database, Container $container)
    {
        $this->db = $database;
        $this->container = $container;
        $this->topics_gateway = new TopicsGateway($this->db);
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];
    }

    /**
     * @param string $projectIds
     * @param string $dateFilter
     * @param string|null $sorting
     * @return mixed
     */
    public function getHomeTopics(string $projectIds, string $dateFilter, string $sorting = null)
    {
        return $this->topics_gateway->getTopicsByProjectAndFilteredByDate($projectIds, $dateFilter, $sorting);
    }

    /**
     * @param int $projectId
     * @param int|null $offset
     * @param int|null $limit
     * @param string|null $sorting
     * @return mixed
     */
    public function getTopicsByProjectId(int $projectId, int $offset = null, int $limit = null, string $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        return $this->topics_gateway->getTopicsByProject($projectId, $offset, $limit, $sorting);
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @param int|null $offset
     * @param int|null $limit
     * @return mixed
     */
    public function getProjectSiteTopics(int $projectId, string $sorting = null, int $offset = null, int $limit = null)
    {
        return $this->topics_gateway->getProjectSiteTopics($projectId, $offset, $limit, $sorting);
    }

    /**
     * @param int $projectId
     * @return int
     */
    public function getTopicCountForProject(int $projectId): int
    {
        $topics = $this->topics_gateway->getTopicsByProject($projectId);
        return count($topics);
    }

    /**
     * @param int $ownerId
     * @param string|null $sorting
     * @return mixed
     */
    public function getTopicsByTopicOwner(int $ownerId, string $sorting = null)
    {
        $ownerId = filter_var($ownerId, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getTopicsByOwner($ownerId, $sorting);
    }

    /**
     * @param int $topicId
     * @return mixed
     */
    public function getTopicByTopicId(int $topicId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getTopicById($topicId);
    }

    /**
     * @param string $topicIds
     * @return mixed
     */
    public function getTopicsIn(string $topicIds)
    {
        return $this->topics_gateway->getTopicsIn($topicIds);
    }

    /**
     * @param int $topicId
     * @return mixed
     */
    public function getPostsByTopicId(int $topicId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getPostsByTopicId($topicId);
    }

    /**
     * @param int $topicId
     * @param int $ownerId
     * @return mixed
     */
    public function getPostsByTopicIdAndNotOwner(int $topicId, int $ownerId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        $ownerId = filter_var($ownerId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getPostsByTopicIdAndNotOwner($topicId, $ownerId);
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function closeTopic(int $topicId): string
    {
        // Sanitize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        return $this->topics_gateway->closeTopic($topicId);
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function publishTopic(int $topicId): string
    {
        // Sanitize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        return $this->topics_gateway->publishTopic($topicId);
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function unPublishTopic(int $topicId): string
    {
        // Sanitize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        return $this->topics_gateway->unPublishTopic($topicId);
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @param string $subject
     * @param int $status
     * @param int $posts
     * @param int $published
     * @param string|null $last_post
     * @return mixed
     */
    public function addTopic(
        int $projectId,
        int $memberId,
        string $subject,
        int $status = 1,
        int $posts = 1,
        int $published = 0,
        string $last_post = null
    ) {
        if (is_null($last_post)) {
            $last_post = date('Y-m-d h:i');
        }

        $newTopicId = $this->topics_gateway->createTopic($projectId, $memberId, $subject, $status, $last_post, $posts,
            $published);
        return $this->getTopicByTopicId($newTopicId);
    }

    /**
     * @param int $topicId
     * @return mixed
     */
    public function incrementTopicPostsCount(int $topicId)
    {
        return $this->topics_gateway->incrementTopicPostsCount($topicId, date('Y-m-d h:i'));
    }

    /**
     * @param int $topicId
     * @return mixed
     */
    public function decrementTopicPostsCount(int $topicId)
    {
        if (empty($topicId)) {
            throw new InvalidArgumentException('Topic ID is missing or empty.');
        }

        return $this->topics_gateway->decrementTopicPostsCount($topicId);
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function getPostById(int $postId)
    {
        return $this->topics_gateway->getPostById($postId);
    }

    /**
     * @param int $topicId
     * @param int $memberId
     * @param string $message
     * @param string|null $created
     * @return string
     */
    public function addPost(int $topicId, int $memberId, string $message, string $created = null): string
    {
        if (is_null($created)) {
            $created = date('Y-m-d h:i');
        }
        $newPostId = $this->topics_gateway->createPost($topicId, $memberId, $message, $created);
        return $this->getPostById($newPostId);

    }

    /**
     * @param array $topicIds
     * @return mixed
     */
    public function deleteTopics(array $topicIds)
    {
        return $this->topics_gateway->deleteTopics($topicIds);
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function deletePost(int $postId)
    {
        if (empty($postId)) {
            throw new InvalidArgumentException('Post ID is missing or empty.');
        }

        return $this->topics_gateway->deletePost($postId);
    }

    /**
     * @param array $topicIds
     * @return mixed
     */
    public function deletePostsFromTopics(array $topicIds)
    {
        return $this->topics_gateway->deletePostsByTopicIds($topicIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteTopicWhereProjectIdIn(string $projectIds)
    {
        return $this->topics_gateway->deleteTopicsByProjectId($projectIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deletePostsByProjectId(string $projectIds)
    {
        return $this->topics_gateway->deletePostsByProjectId($projectIds);
    }

    /**
     * @param string $tmpQuery
     * @param string|null $sorting
     * @param int|null $limit
     * @param int|null $rowLimit
     * @return mixed
     */
    public function getSearchTopics(string $tmpQuery, string $sorting = null, int $limit = null, int $rowLimit = null)
    {
        return $this->topics_gateway->searchResultTopics($tmpQuery, $sorting, $limit, $rowLimit);
    }


    /*
     * Notifications related to Topics and Posts
     */

    /**
     * @param array $topicDetails
     * @param Session $session
     * @throws Exception
     */
    public function sendNewTopicNotification(array $topicDetails, Session $session)
    {
        $this->projects = $this->container->getProjectsLoader();
        $this->teams = $this->container->getTeams();
        $this->notifications = $this->container->getNotificationsManager();

        /*
         *  Get the project details, specifically we need:
         *  pro_org_id, pro_org_name, pro_published, pro_name, pro_id
         */
        $projectDetails = $this->projects->getProjectById($topicDetails["top_project"]);

        /*
         * Get a list of team members, excluding the current member
         */
        $teamMembers = $this->teams->getOtherProjectTeamMembers($topicDetails["top_project"],
            $topicDetails["top_owner"]);

        /*
         * We loop through the list of $teamMembers so we can pass it through to get their notification preferences
         */
        $posters = [];
        foreach ($teamMembers as $teamMember) {
            array_push($posters, $teamMember["tea_member"]);
        }

        /*
         * Retireve a list of notifications for the list of $teamMembers retrieved above
         */
        $listNotifications = $this->notifications->getNotificationsWhereMemberIn(implode(', ', $posters));

        /*
         * Sanity check to make sure we have all the required data before proceeding.
         */
        if ($topicDetails && $projectDetails && $listNotifications) {
            /*
             * Start creating the mail notification
             */
            $mail = $this->container->getNotification();

            try {
                $mail->setFrom($topicDetails["top_mem_email_work"], $topicDetails["top_mem_name"]);

                $mail->partSubject = $this->strings["noti_newtopic1"];
                $mail->partMessage = $this->strings["noti_newtopic2"];


                $subject = $mail->partSubject . " " . $topicDetails["top_subject"];


                if ($projectDetails["pro_org_id"] == "1") {
                    $projectDetails["pro_org_name"] = $this->strings["none"];
                }

                /*
                 * Loop through $listNotifications
                 */
                foreach ($listNotifications as $listNotification) {
                        if (
                            ($listNotification["organization"] != "1"
                                && $topicDetails["top_published"] == "0"
                                && $projectDetails["pro_published"] == "0")
                            || $listNotification["organization"] == "1"
                        ) {
                            /*
                             * Make sure the user has an email address, and is flagged
                             * to receive new topic notifications
                             */

                            if (
                                !empty($listNotification["email_work"])
                                && $listNotification["newTopic"] == "0"
                            ) {
                                /*
                                 * Build up the body of the message
                                 */
                                $body = <<<MESSAGE_BODY
$mail->partMessage

{$this->strings["discussion"]} : {$topicDetails["top_subject"]}
{$this->strings["posted_by"]} : {$session->get('name')} ({$session->get('login')})

{$this->strings["project"]} : {$projectDetails["pro_name"]} ({$projectDetails["pro_id"]})
{$this->strings["organization"]} : {$projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]}

MESSAGE_BODY;

                                if ($listNotification["organization"] == "1") {
                                    $body .= "$this->root/general/login.php?url=topics/viewtopic.php%3Fid=" . $topicDetails["top_id"];
                                }
                                if ($listNotification["organization"] != "1") {
                                    $body .= "$this->root/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetails["pro_id"];
                                }

                                $body .= "\n\n" . $mail->footer;

                                $mail->Subject = $subject;
                                $mail->Priority = "3";

                                // To: Address
                                $mail->addAddress($listNotification["email_work"], $listNotification["name"]);

                                $mail->Body = $body;
                                $mail->send();
                                $mail->clearAddresses();
                            }
                        }
                    }
            } catch (Exception $e) {
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending email.');
        }
    }

    /**
     * @param array $postDetails
     * @param array $topicDetails
     * @param Session $session
     * @throws Exception
     */
    public function sendNewPostNotification(array $postDetails, array $topicDetails, Session $session)
    {
        $this->projects = $this->container->getProjectsLoader();
        $this->teams = $this->container->getTeams();
        $this->notifications = $this->container->getNotificationsManager();

        /*
         *  Get the project details, specifically we need:
         *  pro_org_id, pro_org_name, pro_published, pro_name, pro_id
         */
        $projectDetails = $this->projects->getProjectById($topicDetails["top_project"]);

        /*
         * Get a list of other members that have created posts, excluding the current member
         */
        $listPosts = $this->getPostsByTopicIdAndNotOwner($topicDetails["top_id"], $postDetails["pos_mem_id"]);

        /*
         * We loop through the list of $teamMembers, so we can pass it through to get their notification preferences
         */
        $posters = [];
        foreach ($listPosts as $post) {
            array_push($posters, $post["pos_mem_id"]);
        }

        /*
         * Retrieve a list of notifications for the list of $teamMembers retrieved above
         */
        $listNotifications = $this->notifications->getNotificationsWhereMemberIn(implode(', ', $posters));


        /*
         * Sanity check to make sure we have all the required data before proceeding.
         */
        if ($topicDetails && $projectDetails && $listNotifications) {
            /*
             * Start creating the mail notification
             */

            $mail = $this->container->getNotification();
            try {
                $mail->setFrom($topicDetails["top_mem_email_work"], $topicDetails["top_mem_name"]);

                $mail->partSubject = $this->strings["noti_newpost1"];
                $mail->partMessage = $this->strings["noti_newpost2"];


                $subject = $mail->partSubject . " " . $topicDetails["top_subject"];


                if ($projectDetails["pro_org_id"] == "1") {
                    $projectDetails["pro_org_name"] = $this->strings["none"];
                }

                /*
                 * Loop through $listNotifications
                 */
                foreach ($listNotifications as $listNotification) {
                        if (
                            ($listNotification["organization"] != "1"
                                && $topicDetails["top_published"] == "0"
                                && $projectDetails["pro_published"] == "0")
                            || $listNotification["organization"] == "1"
                        ) {
                            /*
                             * Make sure the user has an email address, and is flagged
                             * to receive new topic notifications
                             */

                            if (
                                !empty($listNotification["email_work"])
                                && $listNotification["newPost"] == "0"
                            ) {
                                /*
                                 * Build up the body of the message
                                 */

                                $body = <<<MESSAGE_BODY
$mail->partMessage

{$this->strings["discussion"]} : {$topicDetails["top_subject"]}
{$this->strings["posted_by"]} : {$session->get('name')} ({$session->get('login')})

{$this->strings["project"]} : {$projectDetails["pro_name"]} ({$projectDetails["pro_id"]})
{$this->strings["organization"]} : {$projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]}

MESSAGE_BODY;

                                if ($listNotification["organization"] == "1") {
                                    $body .= "$this->root/general/login.php?url=topics/viewtopic.php%3Fid=" . $topicDetails["top_id"];
                                }
                                if ($listNotification["organization"] != "1") {
                                    $body .= "$this->root/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetails["pro_id"];
                                }

                                $body .= "\n\n" . $mail->footer;

                                $mail->Subject = $subject;
                                $mail->Priority = "3";

                                // To: Address
                                $mail->addAddress($listNotification["email_work"], $listNotification["name"]);

                                $mail->Body = $body;
                                $mail->send();
                                $mail->clearAddresses();
                            }
                        }
                    }
            } catch (Exception $e) {
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending email.');
        }
    }
}
