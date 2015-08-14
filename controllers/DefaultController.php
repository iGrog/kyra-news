<?php

    namespace kyra\news\controllers;

    use kyra\common\PayloadEvent;
    use kyra\common\BaseController;
    use kyra\common\Json2HtmlRenderer;
    use kyra\gallery\models\Gallery;
    use kyra\image\models\Image;
    use kyra\news\models\News;
    use Yii;
    use yii\base\Event;
    use yii\bootstrap\ActiveForm;
    use yii\data\ActiveDataProvider;
    use yii\db\Query;
    use yii\filters\AccessControl;
    use yii\helpers\ArrayHelper;
    use yii\web\Response;

    class DefaultController extends BaseController
    {
        public function behaviors()
        {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['delete', 'create', 'preview', 'edit', 'admin-list'],
                            'roles' => $this->module->accessRoles,
                        ],
                    ],
                ],
            ];
        }

        public function actionDelete($nid)
        {
            $news = News::findOne($nid);
            if(empty($news))
            {
                Yii::$app->session->setFlash('admin.flash', 'Нет новости с таким ID');
            }
            else
            {
                if(!empty($news->HeaderIID))
                {
                    // TODO: Remove images
                }
                $news->DeleteNews();
                Yii::$app->session->setFlash('admin.flash', 'Новость была успешно удалена!');
            }

            return $this->redirect(['/kyra.news/default/admin-list']);
        }

        public function actionCreate()
        {
            $model = new News;
            $model->IsVisible = true;

            $model->load($_POST);
            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if (Yii::$app->request->isPost && $model->validate())
            {
                $html = Json2HtmlRenderer::Render($model->ContentJSON);
                $model->ContentHTML = empty($html) ? '' : $html;
                if ($model->AddNews())
                {
                    Yii::$app->trigger(News::EVENT_NEWS_CREATED, new PayloadEvent(['payload' => $model->attributes]));
                    Yii::$app->session->setFlash('admin.flash', 'Новость была успешно добавлена!');
                    return $this->redirect(['/kyra.news/default/edit', 'nid' => $model->NID]);
                }
            }

            if(empty($model->DateOf)) $model->DateOf = date('Y-m-d H:i');
            $this->layout = $this->module->adminLayout;
            $this->pageTitle = 'Добавить новость';
            $this->breadcrumbs[] = $this->pageTitle;
            $galleries = ArrayHelper::map(Gallery::find()->all(), 'GalleryID', 'GalleryName');

            return $this->render('create', ['model' => $model, 'galleries' => $galleries]);
        }

        public function actionPreview()
        {
            $model = new News;
            $model->load($_POST);
            if($model->validate())
            {
                return $this->render('view', ['news' => $model]);
            }
        }

        public function actionEdit($nid)
        {
            $model = News::find()->with('headerImage')->where(['NID' => $nid])->one();
            if (empty($model))
            {
                Yii::$app->session->setFlash('admin.flash', 'Нет новости с таким ID!');
                return $this->redirect(['/kyra.news/default/list']);
            }

            $model->load($_POST);
            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if (Yii::$app->request->isPost && $model->validate())
            {
                $model->ContentHTML = Json2HtmlRenderer::Render($model->ContentJSON);
                if ($model->UpdateNews())
                {
                    Yii::$app->trigger(News::EVENT_NEWS_UPDATED, new PayloadEvent(['payload' => $model->attributes]));
                    Yii::$app->session->setFlash('admin.flash', 'Новость была успешно отредактирована!');
                    return $this->redirect(['/kyra.news/default/edit', 'nid' => $model->NID]);
                }
            }

            if (!empty($model->headerImage))
            {
                $model->image = Image::GetImageUrl($model->headerImage->attributes, $this->module->uploadPathKey, 'preview');
            }

            $this->pageTitle = 'Редактировать новость';
            $this->breadcrumbs[] = $this->pageTitle;

            $this->layout = $this->module->adminLayout;

            $galleries = ArrayHelper::map(Gallery::find()->all(), 'GalleryID', 'GalleryName');

            return $this->render('edit', ['model' => $model, 'galleries' => $galleries]);
        }

        public function actionAdminList()
        {
            $dp = new ActiveDataProvider([
                'query' => News::find()->with(['headerImage']),
                'sort' => [
                    'defaultOrder' => [
                        'DateOf' => SORT_DESC,
                    ]
                ],
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);

            $this->pageTitle = 'Список новостей';
            $this->breadcrumbs[] = $this->pageTitle;
            $this->layout = $this->module->adminLayout;
            return $this->render('list', ['dp' => $dp]);
        }

    }