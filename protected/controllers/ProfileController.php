<?php
class ProfileController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }
        
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
    return array(
        array(
            'allow',  // allow all users to perform 'index' and 'view' actions
            'actions' => array('index','view', 'getInfo'),
            'users' => array('@'),
        ),
        array(
            'allow', // allow admin user to perform actions
            'controllers' => array('profile'),
            'actions' => array('create', 'update', 'delete', 'sendSignUpEmail'),
            'expression' => '$user->is_admin'
        ),
        array(
            'deny',  // deny all users
            'users' => array('*'),
        ),
    );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id=null)
    {
        if (!$id) {
            $id = $this->current_user->profile_id;
        }
        $profile = $this->loadModel($id);
        if (isset($profile->user)) {
            $criteria = new CDbCriteria;
            $criteria->limit = 20;
            if ($profile->user->id === $this->current_user->id){
                $criteria->condition = "user_id = {$this->current_user->id}";
                $activities = Activity::model()->findAll($criteria);
            } else {
                $user = User::model()->findByPk($this->current_user->id);
                $activities = $user->getAllVisibleActivitesOfUser($profile->user->id)->findAll($criteria);
            }
        } else {
            $activities = array();
        }
        $this->render('view', array(
            'profile' => $profile,
            'activities' => $activities,
        ));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $criteria = new CDbCriteria();
        $count = Profile::model()->count($criteria);
        $pages = new CPagination($count);
        $pages->pageSize=10;
        $pages->applyLimit($criteria);
        $profiles = Profile::model()->findAll($criteria);
        $this->render('index',array(
            'profiles' => $profiles,
            'pages' => $pages,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $profile = new Profile;
        if (isset($_POST['Profile'])) {
            $profile->attributes = $_POST['Profile'];
            if (empty($profile->date_of_birth)) {
                $profile->date_of_birth = null;
            }
            if ($profile->save()) {
                Yii::app()->user->setFlash('success', 'You created successfully!');
                $this->redirect(array('view', 'id' => $profile->id));
            }
        }

        $this->render('create', array(
            'profile' => $profile,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $profile = $this->loadModel($id);
        if (isset($_POST['Profile'])) {
            $profile->attributes = $_POST['Profile'];
            if (empty($profile->date_of_birth)) {
                $profile->date_of_birth = null;
            }
            if ($profile->save()) {
                Yii::app()->user->setFlash('success', 'You updated successfully!');
                $this->redirect(array('view', 'id' => $profile->id));
            }
        }

        $this->render('update', array(
            'profile' => $profile,
        ));
    }

    /**
     * @author Nguyen Thi Huyen 
     * @param integer $id of the model to be delete
     */
    public function actionDelete($id)
    {
        $profile = $this->loadModel($id);
        if (empty($profile->user->is_admin)) {
            $profile->delete();
            Yii::app()->user->setFlash('success', 'You deleted successfully!');
        }
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Profile the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $profile = Profile::model()->findByPk($id);
        if ($profile === null)
        {
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $profile;
    }

    /**
     * @author Nguyen Thi Huyen
     * @param integer $id of the model to be send signup email
     */
    public function actionSendSignUpEmail($id) {
        $profile = Profile::model()->findByPk($id);
        if ($profile != null && $profile->sendSignUpEmail()) {
            Yii::app()->user->setFlash('sucess', "Sign up email has been sent to {$profile->email}");
        }
        $this->redirect(array('profile/index'));
    }
    
    public function actionGetInfo() {
        if (isset($_POST['profile_id'])) {
            $profile = $this->loadModel($_POST['profile_id']);
            
            if ($profile) {
                echo json_encode($profile->attributes);
            } else {
                throw new CHttpException(403, 'Forbidden.');
            }
        } else {
            throw new CHttpException(403, 'Forbidden.');
        }
    }
}