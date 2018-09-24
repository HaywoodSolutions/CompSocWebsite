<?php
/**
 * The main transfer of data to and from database without accessing database directly using single files.
 *
 * @author Thomas Haywood
 *
 c
function newmail($to, $subect, $html) {
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <webmaster@example.com>' . "\r\n";
    $headers .= 'Cc: myboss@example.com' . "\r\n";

    mail($to,$subject,$html,$headers);
}

class sendemail {
    function welcomeemail($email, $username) {
        newmail($to, "Computing Society: Thanks for registering", $html)
    }
}*/
 
class DB_Functions {
 
    private $conn;
 
    /**
     * Constructor that astablishes the connection to the database
     */ 
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    } 
 
    public function isUserExisted($username) {
        $stmt = $this->conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }
    
    
    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($uname, $email, $accountType, $firstname, $lastname) {
        $stmt = $this->conn->prepare("INSERT INTO users (username, firstname, lastname, email, accountType) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $uname, $firstname, $lastname, $email, $accountType);
        $stmt->execute();
        $amount = $stmt->insert_id;
        $stmt->close();
        return $amount > 0;
    }
    
    public function getUser($uname) {
        $stmt = $this->conn->prepare("SELECT username, firstname, lastname, email, accountType FROM users  WHERE username = ?)");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }
    
    /**
     * Get user by email and password
     */    
    public function getCourses() {
        $stmt = $this->conn->prepare("SELECT `id`,`name`,`main_school`,`mode_of_study`,`stage`,`term`,`exam-percentage`,`coursework-percentage` FROM courses");
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getBuildings() {
        $stmt = $this->conn->prepare("SELECT id, name FROM `map-building`");
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $row["wall_group"] = $this->getWallGroup($row["id"]);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getWallGroup($building_id) {
        $stmt = $this->conn->prepare("SELECT `id`, `name`, `type` FROM `map-wall-group` WHERE `map-building-id` = ?");
        $stmt->bind_param("i", $building_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = array();
            while ($wallgroup = $result->fetch_assoc()) {
                $wallgroup["walls"] = $this->getWalls($wallgroup["id"]);
                array_push($rows, $wallgroup);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return array();
        }
    }
    
    public function getWalls($wall_group_id) {
        $stmt = $this->conn->prepare("SELECT `id`, `lat`, `lng` FROM `map-walls` WHERE `wall-group-id` = ? ORDER BY `created`");
        $stmt->bind_param("i", $wall_group_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = array();
            while ($wall = $result->fetch_assoc()) {
                array_push($rows, $wall);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return array();
        }
    }
    
    public function addToWallsGroup($wall_group_id, $latlngs) {
        $error = 0;
        /*for ($latlngs  $latlng) {
            if ($this->addToWallGroup($wall_group_id, $latlng) == false)
                $error = 1;
        }*/
        return $error;
    }
    
    public function addToWallGroup($wall_group_id, $latlng) {
        $stmt = $this->conn->prepare("INSERT INTO `map-walls` (`lat`, `lng`, `wall-group-id`) VALUES (?, ?, ?);");
        $stmt->bind_param("ddi", $latlng["lat"], $latlng["lng"], $wall_group_id);
        $stmt->execute();
        $amount = $stmt->insert_id;
        $stmt->close();
        return $amount > 0;
    }
    
    public function getPaper($course_id, $year){
        $stmt = $this->conn->prepare("SELECT p.`id`, p.`course_id`, p.`year` FROM `past_papers` p WHERE p.`course_id` = ? AND p.`year` = ?;");
        $stmt->bind_param("ii", $course_id, $year);
        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_assoc();
            $result["questions"] = $this->getPaperQuestions($result["id"]);
            $stmt->close();
            return $result;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getPaperQuestions($paper_id){
        $stmt = $this->conn->prepare("SELECT q.`id`, q.`content`, q.`mark` FROM `past_paper_questions` q WHERE q.`paper_id` = ? ORDER BY q.`question`;");
        $stmt->bind_param("i", $paper_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return array();
        }
    }
    
    public function getCoursePastPapers($course_id) {
        $stmt = $this->conn->prepare("SELECT p.`id`, p.`course_id`, p.`year`, p.`file_id`, (NOW() >= f.`available_from` && NOW() <= f.`available_to`) AS visibile FROM `course-papers` p LEFT JOIN `files` f on f.`id` = p.`file_id` WHERE p.`course_id` = ?;");
        $stmt->bind_param("s", $course_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getCourseAssessments($course_id) {
        $stmt = $this->conn->prepare("SELECT a.`id`, a.`course_id`, a.`number`, a.`name`, a.`percentage`, (NOW() >= a.`date_given` && NOW() <= a.`date_hide`) AS visibile FROM `course-assessments` a WHERE a.`course_id` = ?");
        $stmt->bind_param("s", $course_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getCourseLectures($course_id) {
        $stmt = $this->conn->prepare("SELECT l.`id`, l.`course_id`, l.`lecture_no`, a.`video_file_id`, a.`transcript_file_id`, (NOW() >= f.`available_from` && NOW() <= f.`available_to`) AS visibile FROM `course-assessments` a LEFT JOIN `files` f ON f.`id` = l.`transcript_file_id` WHERE l.`course_id` = ?");
        $stmt->bind_param("s", $course_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getCourseMaterials($course_id) {
        $stmt = $this->conn->prepare("SELECT m.`id`, m.`course_id`, m.`name`, m.`file_id`, (NOW() >= f.`available_from` && NOW() <= f.`available_to`) AS visibile FROM `course-materials` m LEFT JOIN `files` f ON f.`id` = m.`file_id` WHERE m.`course_id` = ?");
        $stmt->bind_param("s", $course_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getCourseQuestions($course_id) {
        $stmt = $this->conn->prepare("SELECT q.`id`, q.`course_id`, q.`title`, q.`content`, q.`visibility`, q.`created` FROM `course-questions` q WHERE q.`course_id` = ?");
        $stmt->bind_param("s", $course_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getOwnQuestions($username) {
        $stmt = $this->conn->prepare("SELECT q.`id`, q.`course_id`, q.`title`, q.`content`, q.`visibility`, q.`created` FROM `course-questions` q LEFT JOIN `users` u ON u.`id` = q.`from_id` WHERE u.`username` = ?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getCourseAnswersForQuestion($course_id, $question_id) {
        $stmt = $this->conn->prepare("SELECT a.`id`, a.`question_id`, a.`content`, a.`user_id`, a.`created` FROM `course-answers` a WHERE a.`course_id` = ? AND a.`question_id` = ?");
        $stmt->bind_param("si", $course_id, $question_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }

    
    
    
    
    
        /**
     * Get user by email and password
     */    
    public function getUserByID($user_id) {
        $this->setActiveUser($user_id);
        $stmt = $this->conn->prepare('SELECT id, name, username, email, profile_url, date_joined, notes, location, course, title, rank, discord_id, date_joined, last_active FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param("s", $user_id);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $user["friends_count"] = $this->getUserFriendsCountByID($user["id"], $user["id"]);
            $user["skills"] = $this->getUserSkillsByID($user["id"]);
            $user["messages"] = $this->getUserMessagesGroupsByID($user["id"]);
            $stmt->close();
            return $user;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function createDB() {
        $stmt = $this->conn->prepare("CREATE USER 'kcs/username'@'%' IDENTIFIED WITH mysql_native_password AS '***';GRANT USAGE ON *.* TO 'kcs/username'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;CREATE DATABASE IF NOT EXISTS `kcs/username`;GRANT ALL PRIVILEGES ON `kcs/username`.* TO 'kcs/username'@'%';");
        if ($stmt->execute()) {
            $affected_rows = $stmt->affected_rows;
            echo $affected_rows;
            $stmt->close();
            return $affected_rows >= 1;
        } else {
            $stmt->close();
            return false;
        }
        
    }
    
    public function setActiveUser($username) {
        $stmt = $this->conn->prepare("UPDATE users SET last_active = NOW() WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();
    }
    
    
    public function addEmail($session_id, $to, $subject, $file){
        $stmt = $this->conn->prepare("INSERT INTO emails (user_id, receiver, subject, contents) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $session_id, $to, $subject, $file);
        $stmt->execute();
        $amount = $stmt->insert_id;
        $stmt->close();
        return $amount > 0;
    }
    
        /**
     * Storing new user
     * returns user details
     */
    public function updateUser($user_id, $name, $course, $location, $notes, $skills, $imageurl, $title, $discord_id) {
        $this->setSkillForUserID($user_id, $skills);
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, course = ?, location = ?, notes = ?, profile_url = ?, title = ?, discord_id = ? WHERE id = ?");
        $stmt->bind_param("ssssssss", $name, $course, $location, $notes, $imageurl, $title, $discord_id, $user_id);
        $stmt->execute();
        $amount = $stmt->affected_rows;
        $stmt->close();
        return $amount > 0;
    }
    
    /**
     * Get user by email and password
     */    
    public function getUserByEmailAndPassword($username, $password) {
        $stmt = $this->conn->prepare('SELECT id, name, username, email, profile_url, date_joined, notes, location, course, title, encrypted_password, salt, rank FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $salt = $user['salt'];   
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            if ($encrypted_password == $hash) {
                unset($user['encrypted_password']);
                return $user;
            }
        } else {
            $stmt->close();
        }
        return NULL;
    }
    
        /**
     * Get user by email and password
     */    
    public function getForums($session_id) {
        $stmt = $this->conn->prepare('SELECT id, name, created,description, getForumMessages(id) AS messages, getForumDiscussions(id) AS discussions FROM forums WHERE parent_forum_id IS NULL');
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
      /**
     * Get user by email and password
     */    
    public function getWikiPage($page_id) {
        $stmt = $this->conn->prepare('SELECT id, name, html, created, modified, views FROM wiki_tables WHERE id = ?');
        $stmt->bind_param("s", $page_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    /**
     * Get user by email and password
     */    
    public function getForumsSubForums($session_id, $forum_id) {
        $stmt = $this->conn->prepare('SELECT id, name, created, description, getForumMessages(id) AS messages, getForumDiscussions(id) AS discussions FROM forums WHERE parent_forum_id = ?');
        $stmt->bind_param("s", $forum_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
        /**
     * Get user by email and password
     */    
    public function getForumsThreads($session_id, $forum_id) {
        $stmt = $this->conn->prepare('SELECT id, name, created FROM forum_threads WHERE forum_id =?');
        $stmt->bind_param("s", $forum_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                //$row["replies"] = $this->getThreadReplies($session_id, $row["id"]);
                //$row["views"] = $this->getThreadViews($row["id"]);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    /**
     * Get user by email and password
     */    
    public function newThread($session_id, $forum_id, $title, $description) {
        $stmt = $this->conn->prepare('INSERT INTO forum_threads (forum_id, name, description, user_id) VALUES (?,?,?,?)');
        $stmt->bind_param("ssss", $forum_id, $title, $description, $session_id);
        $stmt->execute();
        $amount = $stmt->insert_id;
        $stmt->close();
        return $amount > 0;
    }
    
    /**
     * Get user by email and password
     */    
    public function getWikiPagesRecursion($page_id) {
        $stmt = $this->conn->prepare('SELECT id, name, level FROM wiki_tables WHERE parent_id = ?');
        $stmt->bind_param("s", $page_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    /**
     * Get user by email and password
     */    
    public function getWikiPagesBase() {
        $stmt = $this->conn->prepare('SELECT id, name, level FROM wiki_tables WHERE parent_id IS NULL');
        $stmt->bind_param("s", $page_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    /**
     * Get user by email and password
     */    
    public function getWikiPages() {
        $table = $this->getWikiPagesBase();
        $ind = 0;
        while ($ind < sizeof($table)) {
            $value = $table[$ind];
            $newlevel = $this->getWikiPagesRecursion($value["id"]);
            if (sizeof($newlevel)> 0) {
                $table = array_merge(array_slice($table, 0, $ind +1, true),
                    $newlevel,
                    array_slice($table, $ind +1, count($table) - 1, true)) ;
            }
            $ind++;
        }
        return $table;
    }
    
    
    /**
     * Get user by email and password
     */    
    public function newThreadMessage($session_id, $thread_id, $message) {
        $stmt = $this->conn->prepare('INSERT INTO thread_messages (user_id, thread_id, message) VALUES (?,?,?)');
        $stmt->bind_param("sss", $session_id, $thread_id, $message);
        $stmt->execute();
        $amount = $stmt->insert_id;
        $stmt->close();
        return $amount > 0;
    }
    
    /**
     * Get user by email and password
     */    
    public function likeThreadMessage($session_id, $message_id) {
        $stmt = $this->conn->prepare('INSERT INTO thread_message_likes (user_id, thread_message_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE active = !active');
        $stmt->bind_param("ss", $session_id, $message_id);
        $stmt->execute();
        $amount = $stmt->insert_id;
        $stmt->close();
        return $amount > 0;
    }
    
    /**
     * Get user by email and password
     */    
    public function getForum($session_id, $forum_id) {
        $result = [];
        $result['sub_forums'] = $this->getForumsSubForums($session_id, $forum_id);
        $result['threads'] = $this->getForumsThreads($session_id, $forum_id);
        return $result;
    }
    
            /**
     * Get user by email and password
     */    
    public function getThread($session_id, $thread_id) {
        $stmt = $this->conn->prepare('SELECT m.id, getThreadMessageLikes(m.id) AS likes, m.message, m.user_id, m.created, u.name, u.profile_url, u.notes, u.last_active, u.course, u.title, u.rank, getUserLikesReceived(u.id) as user_likes_received, getUsersMessagesCount(u.id) as user_messages_received, u.discord_id FROM thread_messages m LEFT JOIN users u ON u.id = m.user_id WHERE m.thread_id = ? ORDER BY m.created');
        $stmt->bind_param("s", $thread_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $row["user"]=$this->getUserDetailsBreifByID($session_id, $row["user_id"]);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    /**
     * Get user by email and password
     */    
    public function getUserDetailsByID($session_id, $user_id) {
        $stmt = $this->conn->prepare('SELECT id, name, username, email, profile_url, date_joined, notes, location, course,title, encrypted_password, id = ? AS self, rank, discord_id  FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param("ss", $session_id, $user_id);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $user["friends_count"] = $this->getUserFriendsCountByID($session_id, $user_id);
            $user["skills"] = $this->getUserSkillsByID($user_id);
            $stmt->close();
            return $user;
        } else {
            $stmt->close();
            return NULL;
        }
    }

    
    public function getUserMessagesGroupsByID($session_id) {
        $stmt = $this->conn->prepare('SELECT m.id, m.name, m.created FROM users_message_groups u LEFT JOIN message_groups m ON u.message_group_id = m.id WHERE u.user_id = ?');
        $stmt->bind_param("s", $session_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $messagegroup["unread_message_count"] = $this->getUnreadMessageCountGroupByID($row["id"], $row["id"]);
                $messagegroup["message"] = $this->getUnreadMessageCountGroupByID($row["id"], $row["id"]);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    public function getUnreadMessageCountGroupByID($session_id, $group_id) {
        $stmt = $this->conn->prepare('SELECT m.id FROM users_message_groups u LEFT JOIN group_messages m ON u.message_group_id = m.message_group_id WHERE u.user_id = ? AND u.message_group_id = ? AND m.read_timestamp < NOW()');
        $stmt->bind_param("ss", $session_id, $group_id);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    public function getLastMessageByGroupID($group_id) {
        $stmt = $this->conn->prepare('SELECT m.id, m.message, m.created, m.read_timestamp, m.read_timestamp >= NOW() AS read FROM group_messages m WHERE m.id = ? ORDER BY m.read_timestamp ASC');
        $stmt->bind_param("ss", $session_id, $group_id);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    public function getUserMessagesByID($session_id, $group_id) {
        $stmt = $this->conn->prepare('SELECT m.id, m.message, m.created, m.read_timestamp, m.read_timestamp >= NOW() AS read FROM users_message_groups u LEFT JOIN group_messages m ON u.message_group_id = m.message_group_id WHERE u.user_id = ? AND u.message_group_id = ? LIMIT 50');
        $stmt->bind_param("ss", $session_id, $group_id);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    public function getUserDetailsBreifByID($session_id, $user_id) {
        $stmt = $this->conn->prepare('SELECT id, name, username, profile_url, date_joined, id = ? AS self, rank, title,discord_id  FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param("ss", $session_id, $user_id);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $user["friends_count"] = $this->getUserFriendsCountByID($session_id, $user_id);
            $user["skills"] = $this->getUserSkillsByID($user_id);
            $stmt->close();
            return $user;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
    public function getUsersDetails($session_id, $filter) {
        $stmt = "";
        if ($filter > 0) {
            $stmt = $this->conn->prepare('SELECT id, name, username, email, profile_url, date_joined, notes, location, course,title, id = ? AS self, rank, discord_id  FROM users WHERE rank = ?');
            $stmt->bind_param("ss", $session_id, $filter);
        }  else {
            $stmt = $this->conn->prepare('SELECT id, name, username, email, profile_url, date_joined, notes, location, course,title, id = ? AS self, rank, discord_id  FROM users');
            $stmt->bind_param("s", $session_id);
        }
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $row["friends_count"] = $this->getUserFriendsCountByID($session_id, $row["id"]);
                $row["skills"] = $this->getUserSkillsByID($row["id"]);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return NULL;
        }
    }
    
        /**
     * Get user by email and password
     */    
    public function getUserFriendsByID($session_id, $user_id) {
        $stmt = $this->conn->prepare('SELECT id, user_id1, user_id2 FROM friends WHERE (user_id1 = ? &&  user_id2 = ?) || (user_id1 = ? && user_id2 = ?)');
        $stmt->bind_param("ssss", $user_id , $session_id, $session_id, $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                if ($row["user_id1"] == $session_id) {
                    array_push($rows, $this->getUserDetailsByID($session_id, $row["user_id2"]));
                } else {
                    array_push($rows, $this->getUserDetailsByID($session_id, $row["user_id1"]));
                }
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    private function deleteSkills($session_id) {
        $stmt = $this->conn->prepare('DELETE FROM user_skills WHERE user_id = ?');
        $stmt->bind_param("s", $session_id);
        if ($stmt->execute()) {
            $stmt->close();
            return TRUE;
        } else {
            $stmt->close();
            return FALSE;
        }
    }
    
    public function addSkill($session_id, $skill_name) {
        $stmt = $this->conn->prepare('INSERT INTO user_skills (user_id, name) VALUES (?,?)');
        $stmt->bind_param("ss", $session_id, $skill_name);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0){
                return TRUE;
                $stmt->close();
            }
        } else {
            $stmt->close();
        }
        return FALSE;
    }
    
            /**
     * Get user by email and password
     */    
    private function setSkillForUserID($session_id, $skills) {
        $this->deleteSkills($session_id);
        foreach ($skills as $skill_name) {
            $this->addSkill($session_id, $skill_name);
        }
        return TRUE;
    }
    
            /**
     * Get user by email and password
     */    
    private function getUserSkillsByID($user_id) {
        $stmt = $this->conn->prepare('SELECT name FROM user_skills WHERE user_id = ?');
        $stmt->bind_param("s", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return [];
        }
    }
    
    
            /**
     * Get user by email and password
     */    
    private function getUserFriendsCountByID($session_id, $user_id) {
        $stmt = $this->conn->prepare('SELECT COUNT(id) AS amount FROM friends WHERE  (user_id1 = ? &&  user_id2 = ?) || (user_id1 = ? && user_id2 = ?)');
        $stmt->bind_param("ssss", $user_id , $session_id, $session_id, $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result["amount"];
        } else {
            $stmt->close();
            return 0;
        }
    }
    
    /**
     * Get user by email and password
     */    
    public function getEvents($session_id, $user_id) {
        $stmt = $this->conn->prepare('SELECT e.id, e.name, e.description, e.start_date, e.end_date, t.name AS event_type, e.food_provided, e.tickets_price, e.ticket_url, e.user_id, e.image, e.tickets, e.food, e.created, "event" AS post_type FROM gathering e LEFT JOIN event_types t ON t.id = e.type_id WHERE user_id = ? ORDER BY e.start_date');
        $stmt->bind_param("s", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $row["user"]=$this->getUserDetailsBreifByID($session_id, $row["user_id"]);
                $row["has_ticket"] = $this->hasTicketForEvent($session_id, $row["id"]);
                $row["comments"]=$this->getComments($session_id, $row["id"], 3);
                /*if ($row["food_provided"] == 1)
                    $row["food_available"] = $this->getEventFood($session_id, $row["id"]);*/
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
            return [];
        }
    }
    
        /**
     * Get user by email and password
     */    
    public function hasTicketForEvent($session_id, $event_id) {
        $stmt = $this->conn->prepare('SELECT t.id FROM going_event t WHERE t.user_id = ? AND t.event_id = ?');
        $stmt->bind_param("ss", $session_id, $event_id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0){
                $stmt->close();
                return TRUE;
            } else {
                $stmt->close();
            }
        } else {
            $stmt->close();
        }
        return FALSE;
    }
    
            /**
     * Get user by email and password
     */    
    public function getEventTicket($session_id, $event_id) {
        $stmt = $this->conn->prepare('INSERT INTO going_event (user_id, event_id) VALUES (?,?)');
        $stmt->bind_param("ss", $session_id, $event_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0){
                $stmt->close();
                return TRUE;
            } else {
                $stmt->close();
            }
        } else {
            $stmt->close();
        }
        return false;
    }
    
        /**
     * Get user by email and password
     */    
    public function getEvent($session_id, $event_id) {
        $stmt = $this->conn->prepare('SELECT e.id, e.name, e.description, e.start_date, e.end_date, t.name AS event_type, e.food_provided, e.ticket_price, e.ticket_url, e.user_id, e.image, e.tickets, e.food FROM event e LEFT JOIN event_types t ON t.id = e.item_id WHERE e.id = ? LIMIT 1');
        $stmt->bind_param("s", $event_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $result["user"]=$this->getUserDetailsBreifByID($session_id, $result["user_id"]);
            if ($result["food_provided"] == 1)
                $result["food_available"] = $this->getEventFood($session_id, $event_id);
            return $result;
        } else {
            $stmt->close();
            return [];
        }
    }
        
        /**
     * Get user by email and password
     */    
    public function getEventFood($session_id, $event_id) {
        $stmt = $this->conn->prepare('SELECT f.id, f.name, f.price, t.name AS food_type, COALESCE(o.amount, 0) AS ordered FROM orderable_food f LEFT JOIN orderable_food_types t ON t.id = f.type_id LEFT JOIN event_food_orders o ON f.id = o.item_id AND o.event_id = ? AND o.user_id = ? ORDER BY t.name');
        $stmt->bind_param("ss", $event_id, $session_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
        }
        return [];
    }

        
    /**
     * Get user by email and password
     */    
    public function updateFoodOrderedForEvent($session_id, $event_id, $foods) {
        $this->removeFoodOrderedForEvent($session_id, $event_id);
        foreach ($foods as &$food){
            $this->addFoodOrderedForEvent($session_id, $event_id, $food["id"], $food["amount"]);
        }
        return TRUE;
    }
    
    private function removeFoodOrderedForEvent($session_id, $event_id) {
        $stmt = $this->conn->prepare('DELETE FROM event_food_orders WHERE user_id = ? AND event_id = ?');
        $stmt->bind_param("ss", $session_id, $event_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0){
                $stmt->close();
                return TRUE;
            } else
                $stmt->close();
        } else {
            $stmt->close();
        }
        return false;
    }
    
    private function addFoodOrderedForEvent($session_id, $event_id, $foodid, $amount) {
        $stmt = $this->conn->prepare('INSERT INTO event_food_orders (user_id, item_id, event_id, amount) VALUES (?,?,?,?)');
        $stmt->bind_param("ssss", $session_id, $foodid, $event_id, $amount);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0){
                $stmt->close();
                return TRUE;
            } else
                $stmt->close();
        } else {
            $stmt->close();
        }
        return false;
    }
    
            
        /**
     * Get user by email and password
     */    
    public function addPostFromUser($session_id, $message, $image_url) {
        $stmt = $this->conn->prepare('INSERT INTO post (user_id, message, image_url) VALUES (?,?,?)');
        $stmt->bind_param("sss", $session_id, $message, $image_url);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0){
                $stmt->close();
                return TRUE;
            } else
                $stmt->close();
        } else {
            $stmt->close();
        }
        return false;
    }
    
    /**
     * Get user by email and password
     */    
    public function addProduct($session_id, $name, $description, $url, $price) {
        $stmt = $this->conn->prepare('INSERT INTO post (requestor_id, name, description, url, price) VALUES (?,?,?,?,?)');
        $stmt->bind_param("sssss", $session_id, $name, $description, $url, $price);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0){
                $stmt->close();
                return TRUE;
            } else
                $stmt->close();
        } else {
            $stmt->close();
        }
        return false;
    }
    
            /**
     * Get user by email and password
     */    
    private function getPosts($session_id, $user_id) {
        $stmt = $this->conn->prepare('SELECT *, "post" AS post_type FROM post WHERE user_id = ? AND publish_date >= NOW()');
        $stmt->bind_param("s", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $row["user"]=$this->getUserDetailsBreifByID($session_id, $row["user_id"]);
                $row["comments"]=$this->getComments($session_id, $row["id"], 1);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
        }
        return [];
    }
    
             /**
     * Get user by email and password
     */    
    private function getComments($session_id, $post_id, $post_type) {
        $stmt = $this->conn->prepare('SELECT id, user_id, message, created FROM comments WHERE post_id = ? AND post_type = ? ORDER BY created DESC');
        $stmt->bind_param("ss", $post_id, $post_type);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $row["user"]=$this->getUserDetailsBreifByID($session_id, $row["user_id"]);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
        }
        return [];
    }
    
                /**
     * Get user by email and password
     */    
    private function getProducts($session_id, $user_id) {
        $stmt = $this->conn->prepare('SELECT *, "product" AS post_type FROM products WHERE user_id = ?');
        $stmt->bind_param("s", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $row["user"]=$this->getUserDetailsBreifByID($session_id, $row["user_id"]);
                $row["comments"]=$this->getComments($session_id, $row["id"], 2);
                array_push($rows, $row);
            }
            $stmt->close();
            return $rows;
        } else {
            $stmt->close();
        }
        return [];
    }
    
    
        /**
     * Get user by email and password
     */    
    public function getTimeline($session_id) {
        $posts = $this->getPosts($session_id, '%');
        $products = $this->getProducts($session_id, '%');
        $events = $this->getEvents($session_id, '%');
        $timelineArray = array_merge($posts,$products,$events);
        function sortDate($val1, $val2) {
            if ($val1['created'] == $val2['created']) {
                return 0;
            }
            return (strtotime($val1['created']) > strtotime($val2['created'])) ? -1 : 1;
        }
        usort($timelineArray, 'sortDate');
        return $timelineArray;
    }
    
      /**
     * Get user by email and password
     */    
    public function getActivity($session_id, $user_id) {
        $posts = $this->getPosts($session_id, $user_id);
        $products = $this->getProducts($session_id, $user_id);
        $events = $this->getEvents($session_id, $user_id);
        $timelineArray = array_merge($posts,$products,$events);
        function sortDate($val1, $val2) {
            if ($val1['created'] == $val2['created']) {
                return 0;
            }
            return (strtotime($val1['created']) > strtotime($val2['created'])) ? -1 : 1;
        }
        usort($timelineArray, 'sortDate');
        return $timelineArray;
    }

 
    public function hashSSHA($password) {
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    public function checkhashSSHA($salt, $password) {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }
}
?>