<?php

/**
 * This is the model class for table "polls".
 *
 * The followings are the available columns in table 'polls':
 * @property integer $id
 * @property string $question
 * @property string $description
 * @property integer $user_id
 * @property integer $is_multichoice
 * @property integer $poll_type
 * @property integer $display_type
 * @property integer $result_display_type
 * @property integer $result_detail_type
 * @property integer $result_show_time_type
 * @property string $created_at
 * @property string $updated_at
 * @property string $start_at
 * @property string $end_at
 */
class Poll extends ActiveRecord
{

    const IS_MULTICHOICES_YES = 1;
    const IS_MULTICHOICES_NO = 0;
    const POLL_TYPE_SETTINGS_ANONYMOUS = 1;
    const POLL_TYPE_SETTINGS_NON_ANONYMOUS = 2;
    const POLL_DISPLAY_SETTINGS_PUBLIC = 1;
    const POLL_DISPLAY_SETTINGS_RESTRICTED = 2;
    const POLL_DISPLAY_SETTINGS_INVITED_ONLY = 3;
    const RESULT_DISPLAY_SETTINGS_PUBLIC = 1;
    const RESULT_DISPLAY_SETTINGS_VOTED_ONLY = 2;
    const RESULT_DISPLAY_SETTINGS_OWNER_ONLY = 3;
    const RESULT_DETAIL_SETTINGS_ALL = 1;
    const RESULT_DETAIL_SETTINGS_ONLY_PERCENTAGE = 2;
    const RESULT_TIME_SETTINGS_AFTER = 1;
    const RESULT_TIME_SETTINGS_DURING = 2;

    public static $IS_MULTICHOICES_SETTINGS = array(
        'No' => self::IS_MULTICHOICES_NO,
        'Yes' => self::IS_MULTICHOICES_YES,        
    );
    public static $POLL_TYPE_SETTINGS = array(
        'Non-anonymous' => self::POLL_TYPE_SETTINGS_NON_ANONYMOUS,
        'Anonymous' => self::POLL_TYPE_SETTINGS_ANONYMOUS,        
    );
    public static $POLL_DISPLAY_SETTINGS = array(
        'Public' => self::POLL_DISPLAY_SETTINGS_PUBLIC,
        'Restricted' => self::POLL_DISPLAY_SETTINGS_RESTRICTED,
        'Invited only' => self::POLL_DISPLAY_SETTINGS_INVITED_ONLY,
    );
    public static $RESULT_DISPLAY_SETTINGS = array(
        'Public' => self::RESULT_DISPLAY_SETTINGS_PUBLIC,
        'Voted only' => self::RESULT_DISPLAY_SETTINGS_VOTED_ONLY,
        'Owner only' => self::RESULT_DISPLAY_SETTINGS_OWNER_ONLY,
    );
    public static $RESULT_DETAIL_SETTINGS = array(
        'All' => self::RESULT_DETAIL_SETTINGS_ALL,
        'Only percentage' => self::RESULT_DETAIL_SETTINGS_ONLY_PERCENTAGE,
    );
    public static $RESULT_TIME_SETTINGS = array(
        'After' => self::RESULT_TIME_SETTINGS_AFTER,
        'During' => self::RESULT_TIME_SETTINGS_DURING,
    );

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Poll the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'polls';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question, description, start_at, end_at', 'required'),
            array('user_id, is_multichoice, poll_type, display_type, result_display_type, result_detail_type, result_show_time_type', 'numerical', 'integerOnly' => true),
            array('question', 'length', 'max' => 255),
            array('description, created_at, updated_at, start_at, end_at', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, question, description, user_id, is_multichoice, poll_type, display_type, result_display_type, result_detail_type, result_show_time_type, created_at, updated_at, start_at, end_at', 'safe', 'on' => 'search'),
            array('start_at, end_at', 'date', 'format' => 'yyyy-M-d H:m:s'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'choices' => array(self::HAS_MANY, 'Choice', 'poll_id'),
            'comments' => array(self::HAS_MANY, 'Comment', 'poll_id'),
            'invitations' => array(self::HAS_MANY, 'Invitation', 'poll_id'),
            'notifications' => array(self::HAS_MANY, 'Notification', 'poll_id'),
            'activities' => array(self::HAS_MANY, 'Activity', 'poll_id'),
        );
    }

    /**
     *
     * @return array The behaviors attached to Profile
     * @author Tran Duc Thang
     */
    public function behaviors()
    {
        return array(
            'ViewLinkBehavior' => array(
                'class' => 'application.components.ViewLinkBehavior',
                'display_attribute' => 'question',
                'controller_name' => 'poll'
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'question' => 'Question',
            'description' => 'Description',
            'user_id' => 'User',
            'is_multichoice' => 'Is Multichoice',
            'poll_type' => 'Poll Type',
            'display_type' => 'Display Type',
            'result_display_type' => 'Result Display Type',
            'result_detail_type' => 'Result Detail Type',
            'result_show_time_type' => 'Result Show Time Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('question', $this->question, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('is_multichoice', $this->is_multichoice);
        $criteria->compare('poll_type', $this->poll_type);
        $criteria->compare('display_type', $this->display_type);
        $criteria->compare('result_display_type', $this->result_display_type);
        $criteria->compare('result_detail_type', $this->result_detail_type);
        $criteria->compare('result_show_time_type', $this->result_show_time_type);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('start_at', $this->start_at, true);
        $criteria->compare('end_at', $this->end_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @author Nguyen Anh Tien
     */
    public function defaultScope()
    {
        return array(
            'order'=> $this->getTableAlias(false, false).".`created_at` DESC",
        );
    }

    /**
     * @author Nguyen Van Cuong , Vu Dang Tung
     * after delete poll . Automatic delete comments and choices belong to this poll
     */
    public function afterDelete()
    {
        foreach ($this->comments as $comment) {
            $comment->delete();
        }
        foreach ($this->choices as $choice) {
            $choice->delete();
        }
        foreach ($this->activities as $activity) {
            $activity->delete();
        }
        foreach ($this->invitations as $invitation) {
            $invitation->delete();
        }
        foreach ($this->notifications as $notification) {
            $notification->delete();
        }
    }

    /**
     * @author Nguyen Anh Tien
     */
    public function hasChoice($choice_id)
    {
        return Choice::model()->exists(
                'id=:id AND poll_id=:poll_id', array(':id' => $choice_id, ':poll_id' => $this->id)
        );
    }

    /**
     *
     * @return boolean poll has started or not
     * @author Tran Duc Thang
     */
    public function hasStarted()
    {
        return $this->start_at <= date('Y-m-d H:i:s');
    }

    /**
     *
     * @return boolean poll has ended or not
     * @author Tran Duc Thang
     */
    public function hasEnded()
    {
        return $this->end_at <= date('Y-m-d H:i:s');
    }

    /**
     * @author Nguyen Anh Tien
     * @param integer $user_id id of user that can see those polls
     */
    public function canBeSeenBy($user_id)
    {
        // user can see public and restricted poll
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'user_id=:user_id or display_type in (:public, :restricted)',
                'params' => array(
                    ':user_id' => $user_id,
                    ':public' => Poll::POLL_DISPLAY_SETTINGS_PUBLIC,
                    ':restricted' => Poll::POLL_DISPLAY_SETTINGS_RESTRICTED,
                ),
            )
        );
        $this->getDbCriteria()->mergeWith(
            array(
                'with' => 'invitations',
                'condition' => 'invitations.receiver_id=:user_id and display_type=:invite_only',
                'params' => array(
                    ':user_id' => $user_id,
                    ':invite_only' => Poll::POLL_DISPLAY_SETTINGS_INVITED_ONLY,
                ),
                'together' => true,
            ), 'OR'
        );

        return $this;
    }

    /**
     * @author Nguyen Anh Tien
     * @param Poll $new edited poll model
     */
    public function logChangedAttributes($new)
    {
        $other_settings_attr = array(
            'result_detail_type',
            'result_display_type',
            'result_show_time_type',
        );
        $types = array();
        foreach ($new as $attr => $value) {
            if ($value !== $this->attributes[$attr]) {
                if (!in_array(Activity::CHANGE_POLL_TIME, $types)
                    && ($attr == 'start_at' || $attr == 'end_at')
                ) {
                    $types[] = Activity::CHANGE_POLL_TIME;
                } else if (!in_array(Activity::CHANGE_POLL_SETTING, $types)
                    && in_array($attr, $other_settings_attr)
                ) {
                    $types[] = Activity::CHANGE_POLL_SETTING;
                }
            }
        }
        Yii::app()->session['activity_type'] = $types;
    }

    /**
     * record vote type vote or re-vote
     * @author Nguyen Anh Tien
     */
    public function logVoteType($user){
        $votes = $user->getAllVotes($this->id);
        if (empty($votes)) {
            Yii::app()->session['vote_type'] = Activity::VOTE;
        } else {
            Yii::app()->session['vote_type'] = Activity::RE_VOTE;
        }
    }

    /**
     * @author Nguyen Anh Tien
     * @return function afterSave
     */
    public function afterSave()
    {
        if ($this->isNewRecord) {
            $params = array(
                'type' => Activity::CREATE_POLL,
                'user_id' => $this->user_id,
                'poll_id' => $this->id,
                'display_type' => $this->getActivityDisplayType(),
            );
            Activity::create($params);
        } else {
            if (!empty(Yii::app()->session['activity_type'])) {
                foreach (Yii::app()->session['activity_type'] as $type) {
                    $params = array(
                        'type' => $type,
                        'user_id' => $this->user_id,
                        'poll_id' => $this->id,
                        'display_type' => $this->getActivityDisplayType(),
                    );
                    Activity::create($params);
                }
            }
            unset(Yii::app()->session['activity_type']);
        }
        return parent::afterSave();
    }

    /**
     * @author Nguyen Anh Tien
     * @return integer activity display type of this poll
     */
    public function getActivityDisplayType(){
        if ($this->poll_type == Poll::POLL_TYPE_SETTINGS_ANONYMOUS) {
            return Activity::DISPLAY_PRIVATE;
        }
        if ($this->display_type == Poll::POLL_DISPLAY_SETTINGS_INVITED_ONLY) {
            return Activity::DISPLAY_RESTRICTED;
        } else {
            return Activity::DISPLAY_PUBLIC;
        }
    }
    
    /**
     * @author Vu Dang Tung
     * 
     */
    public function isVoting(){
        $current = time();
        $start = strtotime($this->start_at);
        $end = strtotime($this->end_at);
        if($start <= $current && $current <= $end)
        {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @author Pham Tri Thai
     * 
     */
    public function isVoted(){
        $current = time();
        $end = strtotime($this->end_at);
        if($current >= $end)
        {
            return true;
        } else {
            return false;
        }
    }
    
    public function loadComment($comment_id = null) {
        
        $criteria = new CDbCriteria;
        $criteria->condition ='poll_id=:poll_id';
        $params[':poll_id'] =  $this->id;
        if ($comment_id) {
            $criteria->addCondition('id<:comment_id');
            $params[':comment_id'] = $comment_id;
        }
        $criteria->params = $params;
        return Comment::model()->is_parent()->limit_comment()->order_created_at()->findAll($criteria);
    }
}

