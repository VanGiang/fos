<?php // 
/*
 * An template used for displaying an activity at stream
 * @author Tran Duc Thang
 * @var array $activity
 */
?>
<div class="alert stream-item">
    <?php
        $poll = $activity->poll->createViewLink();
        $user = $activity->user->profile->createViewLink();
        switch ($activity->type) {
            case Activity::CREATE_POLL;
                echo "{$user} created poll {$poll}.";
                break;
            case Activity::ADD_CHOICE;
                $choice = $activity->choice->content;
                echo "{$user} added choice {$choice} for poll {$poll}.";
                break;
            case Activity::CHANGE_POLL_TIME;
                echo "{$user} changed time settings of poll {$poll}.";
                break;
            case Activity::CHANGE_POLL_SETTING;
                echo "{$user} changed settings of poll {$poll}.";
                break;
            case Activity::VOTE;
                $choice = $activity->choice->content;
                echo "{$user} voted in poll {$poll} with {$choice}.";
                break;
            case Activity::RE_VOTE;
                $choice = $activity->choice->content;
                echo "{$user} re-voted in poll {$poll} with {$choice}.";
                break;
            case Activity::INVITE;
                $target_user = $activity->target_user->profile->createViewLink();
                echo "{$user} invited {$target_user} to vote in poll {$poll}";
                break;
            case Activity::COMMENT;
                echo "{$user} commented in poll {$poll}";
                break;
            case Activity::REPLY_COMMENT;
                echo "{$user} replied a comment in poll {$poll}";
                break;
            default:
                break;
        }
    ?>
</div>
