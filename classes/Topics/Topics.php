<?php


namespace phpCollab\Topics;

use Exception;
use InvalidArgumentException;
use phpCollab\Database;
use phpCollab\Notification;
use phpCollab\Notifications\Notifications;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Topics
 * @package phpCollab\Topics
 */
class Topics
{
    protected $topics_gateway;
    protected $db;
    protected $projects;
    protected $teams;
    protected $notifications;
    protected $strings;
    protected $root;

    /**
     * Topics constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->topics_gateway = new TopicsGateway($this->db);
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];
    }

    /**
     * @param $projectIds
     * @param $dateFilter
     * @param null $sorting
     * @return mixed
     */
    public function getHomeTopics($projectIds, $dateFilter, $sorting = null)
    {
        $data = $this->topics_gateway->getTopicsByProjectAndFilteredByDate($projectIds, $dateFilter, $sorting);
        return $data;
    }

    /**
     * @param $projectId
     * @param null $offset
     * @param null $limit
     * @param $sorting
     * @return mixed
     */
    public function getTopicsByProjectId($projectId, $offset = null, $limit = null, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        return $this->topics_gateway->getTopicsByProject($projectId, $offset, $limit, $sorting);
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @param null $offset
     * @param null $limit
     * @return mixed
     */
    public function getProjectSiteTopics($projectId, $sorting = null, $offset = null, $limit = null)
    {
        return $this->topics_gateway->getProjectSiteTopics($projectId, $offset, $limit, $sorting);
    }

    /**
     * @param $projectId
     * @return int
     */
    public function getTopicCountForProject($projectId)
    {
        $topics = $this->topics_gateway->getTopicsByProject($projectId);
        return count($topics);
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getTopicsByTopicOwner($ownerId, $sorting)
    {
        $ownerId = filter_var($ownerId, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getTopicsByOwner($ownerId, $sorting);
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getTopicByTopicId($topicId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getTopicById($topicId);
    }

    /**
     * @param $topicIds
     * @return mixed
     */
    public function getTopicsIn($topicIds)
    {
        return $this->topics_gateway->getTopicsIn($topicIds);
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getPostsByTopicId($topicId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getPostsByTopicId($topicId);
    }

    /**
     * @param $topicId
     * @param $ownerId
     * @return mixed
     */
    public function getPostsByTopicIdAndNotOwner($topicId, $ownerId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        $ownerId = filter_var($ownerId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getPostsByTopicIdAndNotOwner($topicId, $ownerId);
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function closeTopic($topicId)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->closeTopic($topicId);

        return $data;
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function publishTopic($topicId)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->publishTopic($topicId);

        return $data;
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function unPublishTopic($topicId)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->unPublishTopic($topicId);

        return $data;
    }

    /**
     * @param $projectId
     * @param $memberId
     * @param $subject
     * @param int $status
     * @param null $last_post
     * @param int $posts
     * @param int $published
     * @return mixed
     */
    public function addTopic($projectId, $memberId, $subject, $status = 1, $posts = 1, $published = 0, $last_post = null)
    {
        if (is_null($last_post)) {
            $last_post = date('Y-m-d h:i');
        }

        $newTopicId = $this->topics_gateway->createTopic($projectId, $memberId, $subject, $status, $last_post, $posts, $published);
        return $this->getTopicByTopicId($newTopicId);
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function incrementTopicPostsCount($topicId)
    {
        return $this->topics_gateway->incrementTopicPostsCount($topicId, date('Y-m-d h:i'));
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function decrementTopicPostsCount($topicId)
    {
        if (empty($topicId)) {
            throw new InvalidArgumentException('Topic ID is missing or empty.');
        }

        return $this->topics_gateway->decrementTopicPostsCount($topicId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getPostById($postId)
    {
        return $this->topics_gateway->getPostById($postId);
    }

    /**
     * @param $topicId
     * @param $memberId
     * @param $message
     * @param null $created
     * @return string
     */
    public function addPost($topicId, $memberId, $message, $created = null)
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
        if (isset($topicIds) && is_array($topicIds)) {
            return $this->topics_gateway->deleteTopics($topicIds);
        }
        return false;
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function deletePost($postId)
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
        if (isset($topicIds) && is_array($topicIds)) {
            return $this->topics_gateway->deletePostsByTopicIds($topicIds);
        }
        return false;
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteTopicWhereProjectIdIn($projectIds)
    {
        return $this->topics_gateway->deleteTopicsByProjectId($projectIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deletePostsByProjectId($projectIds)
    {
        return $this->topics_gateway->deletePostsByProjectId($projectIds);
    }

    /**
     * @param $tmpQuery
     * @param null $sorting
     * @param null $limit
     * @param null $rowLimit
     * @return mixed
     */
    public function getSearchTopics($tmpQuery, $sorting = null, $limit = null, $rowLimit = null)
    {
        return $this->topics_gateway->searchResultTopics($tmpQuery, $sorting, $limit, $rowLimit);
    }


    /*
     * Notifications related to Topics and Posts
     */

    /**
     * @param $topicDetails
     * @param Session $session
     * @throws Exception
     */
    public function sendNewTopicNotification($topicDetails, Session $session)
    {
        $this->projects = new Projects();
        $this->teams = new Teams();
        $this->notifications = new Notifications();

        /*
         *  Get the project details, specifically we need:
         *  pro_org_id, pro_org_name, pro_published, pro_name, pro_id
         */
        $projectDetails = $this->projects->getProjectById($topicDetails["top_project"]);

        /*
         * Get a list of team members, excluding the current member
         */
        $teamMembers = $this->teams->getOtherProjectTeamMembers($topicDetails["top_project"], $topicDetails["top_owner"]);

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
            $mail = new Notification(true);

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
                if ($listNotifications) {
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
{$mail->partMessage}

{$this->strings["discussion"]} : {$topicDetails["top_subject"]}
{$this->strings["posted_by"]} : {$session->get('nameSession')} ({$session->get('loginSession')})

{$this->strings["project"]} : {$projectDetails["pro_name"]} ({$projectDetails["pro_id"]})
{$this->strings["organization"]} : {$projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]}

MESSAGE_BODY;

                                if ($listNotification["organization"] == "1") {
                                    $body .= "{$this->root}/general/login.php?url=topics/viewtopic.php%3Fid=" . $topicDetails["top_id"];
                                } elseif ($listNotification["organization"] != "1") {
                                    $body .= "{$this->root}/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetails["pro_id"];
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
                }
            } catch (Exception $e) {
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending email.');
        }
    }

    /**
     * @param $postDetails
     * @param $topicDetails
     * @param Session $session
     * @throws Exception
     */
    public function sendNewPostNotification($postDetails, $topicDetails, Session $session)
    {
        $this->projects = new Projects();
        $this->teams = new Teams();
        $this->notifications = new Notifications();

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
         * We loop through the list of $teamMembers so we can pass it through to get their notification preferences
         */
        $posters = [];
        foreach ($listPosts as $post) {
            array_push($posters, $post["pos_mem_id"]);
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
            $mail = new Notification(true);

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
                if ($listNotifications) {
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
{$mail->partMessage}

{$this->strings["discussion"]} : {$topicDetails["top_subject"]}
{$this->strings["posted_by"]} : {$session->get('nameSession')} ({$session->get('loginSession')})

{$this->strings["project"]} : {$projectDetails["pro_name"]} ({$projectDetails["pro_id"]})
{$this->strings["organization"]} : {$projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]}

MESSAGE_BODY;

                                if ($listNotification["organization"] == "1") {
                                    $body .= "{$this->root}/general/login.php?url=topics/viewtopic.php%3Fid=" . $topicDetails["top_id"];
                                } elseif ($listNotification["organization"] != "1") {
                                    $body .= "{$this->root}/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetails["pro_id"];
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
                }
            } catch (Exception $e) {
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending email.');
        }
    }
}
