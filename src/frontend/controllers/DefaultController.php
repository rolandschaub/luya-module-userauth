<?php

namespace luya\userauth\frontend\controllers;

use Yii;
use luya\web\Controller;
use luya\userauth\models\UserLoginForm;
use luya\admin\models\Config;
use luya\userauth\frontend\Module;
use luya\helpers\Url;
use luya\cms\menu\QueryOperatorFieldInterface;

/**
 * User Login.
 *
 * @author Basil Suter <basil@nadar.io
 * @since 1.0.0
 */
class DefaultController extends Controller
{
    /**
     * Render the login form model.
     *
     * @return \yii\web\Response|string
     */
    public function actionIndex($redir = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($this->getRedirectUrl($redir));
        }
        
        $model = new UserLoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate() && Yii::$app->user->login($model->user)) {
            return $this->redirect($this->getRedirectUrl($redir));
        }
        
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Get the redirect url from config, redir parmater or default base (home) url.
     *
     * @param string $redir Optional urlencoded redirect from url
     * @return string
     */
    protected function getRedirectUrl($redir)
    {
        if (!empty($redir)) {
            return urldecode($redir);
        }
        
        $navId = Config::get(Module::USERAUTH_CONFIG_AFTER_LOGIN_NAV_ID, false);
        
        if ($navId) {
            $navItem = Yii::$app->menu->find()->where([QueryOperatorFieldInterface::FIELD_NAVID => $navId])->with(['hidden'])->one();
            
            if ($navItem) {
                return $navItem->absoluteLink;
            }
        }
        
        return Url::base(true);
    }
}
