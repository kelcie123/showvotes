    <?php
    /**
    *
    * @package phpBB3
    * @version $Id$
    * @copyright (c) 2005 phpBB Group
    * @license http://opensource.org/licenses/gpl-license.php GNU Public License
    *
    */

    /**
    * Script to show vote details
    */
    define('IN_PHPBB', true);
    $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
    $phpEx = substr(strrchr(__FILE__, '.'), 1);
    include($phpbb_root_path . 'common.' . $phpEx);
    include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
    include($phpbb_root_path . 'includes/bbcode.' . $phpEx);

    // Initial var setup
    $topic_id       = request_var('id', 0);

    // Check if can see votes
    $sql = 'SELECT  poll_start,poll_length,poll_title
            FROM `phpbb_topics`
            WHERE topic_id ='.$db->sql_escape($topic_id);

    $result = $db->sql_query($sql);

    $row = $db->sql_fetchrow($result);
    if (empty($row) || empty($row['poll_title'])) {
            die("invalid poll");
    }
    $poll_end = $row['poll_length'] + $row['poll_start'];
    if ($poll_end > time()) {
            die("The poll is still active! Be patient ;-)");
    } else {
            $pollTitle=$row['poll_title'];
    }
    $db->sql_freeresult($result);



    /**
     * Get Results
     */
    $sql = 'SELECT username, poll_option_text, group_id, user_lastvisit
            FROM `phpbb_users`
            LEFT JOIN `phpbb_poll_votes` ON phpbb_poll_votes.vote_user_id = phpbb_users.user_id
            LEFT JOIN phpbb_poll_options ON phpbb_poll_options.poll_option_id = phpbb_poll_votes.poll_option_id
            WHERE phpbb_poll_votes.topic_id ='.$db->sql_escape($topic_id).'
            AND phpbb_poll_options.topic_id ='.$db->sql_escape($topic_id);

    $result = $db->sql_query($sql);


    echo '<html><head><title>Αποτελέσματα ψηφοφορίας'.$pollTitle.'</title></head>
                    <body><h1>'.$pollTitle.'</h1>';
    echo '<table cellpadding="5px" style="color:#333">
                            <tr>
                                    <td><strong>Username</strong></td>
                                    <td><strong>Ψήφος</strong></td>
                                    <td><strong>Ομάδα</strong></td>
                                    <td><strong>Τελευταία Επίσκεψη</strong></td>
                            </tr>';
    while ($row = $db->sql_fetchrow($result))
    {
            echo '<tr><td>'.$row['username'].'</td><td>'.$row['poll_option_text'].'</td><td>'.$row['group_id'].'</td><td>'.$row['user_lastvisit'].'</td>';
            echo "</tr>";
    }
    $db->sql_freeresult($result);
    echo '</table></body></html>';
