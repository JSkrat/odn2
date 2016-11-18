<?php

namespace frontend\controllers;

use Yii;
//use frontend\models\Templates;
use frontend\models\TemplateValues;
use frontend\models\TemplateClasses;
use frontend\models\TemplateFields;
use app\models\TemplateQuery;
use yii\data\ActiveDataProvider;
//use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\Pages;
use frontend\models\PageFields;

/**
 * AdminController implements the CRUD actions for TemplateValues model.
 */
class AdminController extends Controller
{
	public function __construct($id, $module, $config = array()) {
		parent::__construct($id, $module, $config);
		$this->layout = 'admin';
	}
	
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TemplateValues models.
     * @return mixed
     */
    public function actionIndex($class = 0)
    {
		if ($class) {
			$dataProvider = new ActiveDataProvider([
				'query' => TemplateQuery::find()->where(['template_class_id' => $class]),
			]);
			return $this->render('index', [
				'dataProvider' => $dataProvider,
				'fields' => array_merge(['id', 'name'], TemplateFields::find()->select('name')->where(['template_class_id' => $class])->column()),
				'classModel' => TemplateClasses::findOne($class),
			]);
		} else {
			$dataProvider = new ActiveDataProvider([
				'query' => TemplateClasses::find(),
			]);
			return $this->render('index', [
				'dataProvider' => $dataProvider,
				'fields' => ['id', 'name'],
				'classModel' => null,
			]);
		}
    }

    /**
     * Displays a single TemplateValues model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TemplateValues model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TemplateValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TemplateValues model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $class
	 * @param integer $id
     * @return mixed
     */
    public function actionUpdate($class = 0, $id = 0) {
		if ($id) {
			$model = $this->findModel($id);
			$class = $model->classID;
		} else {
			$model = new TemplateQuery();
			$model->setClass($class);
		}
		$classModel = TemplateClasses::findOne($class);
		if (null === $classModel) { throw new NotFoundHttpException('The requested page does not exist.'); }
		$allPagesList = array();
		$allPagesCheckboxen = array();
		$checkedPages = array_flip(PageFields::find()->where(['template_id' => $model->id])->select('page_id')->column());
		foreach (Pages::find()->all() as $page) {
			$caption = "{$page->title} ({$page->url})";
			$allPagesCheckboxen[$page->id] = array(
				'caption' => $caption,
				'checked' => (isset($checkedPages[$page->id])),
			);
			$allPagesList[$page->id] = $caption;
		}
		$fields = TemplateFields::find()->where(['template_class_id' => $class])->all();
		$allowedClasses = array();
		foreach ($fields as $f) {
			if ('template' == $f->namedType->type_name) {
				$ca = array();
				foreach ($f->allowedObjects as $classAllowed) {
					if (! array_key_exists($classAllowed->templateClass->name, $ca)) { $ca[$classAllowed->templateClass->name] = array(); }
					$ca[$classAllowed->templateClass->name][$classAllowed->id] = $this->renderPartial(
							'classtpl/' . $classAllowed->templateClass->name,
							['data' => $classAllowed]
					);
				}
				$allowedClasses[$f->name] = $ca;
			} //elseif ('')
		}
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			// we need to save page relations here
			$pages = Yii::$app->request->post()['pages'];
			$transaction = PageFields::getDb()->transaction(function ($db) use ($model, $pages) {
				PageFields::deleteAll(['template_id' => $model->id]);
				$rows = array();
				foreach ($pages as $page => $value) {
					$rows[] = array(
						'page_id' => $page,
						'template_id' => $model->id,
					);
				}
//				print_r($rows); die();
				PageFields::getDb()->createCommand()->batchInsert(PageFields::tableName(), (new PageFields())->attributes(), $rows)->execute();
			});
//			print_r(Yii::$app->request->post()['pages']); die();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'classModel' => $classModel,
				'fields' => $fields,
				'allowedClasses' => $allowedClasses,
				'allPagesList' => $allPagesList,
				'allPagesCheckboxen' => $allPagesCheckboxen,
            ]);
        }
    }

    /**
     * Deletes an existing TemplateValues model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $template = $this->findModel($id);
		$classID = $template->classID;
		$d = $template->delete();
		if (! $d) {
			print_r($d); echo '<br/>';
			print_r($template); echo '<br/>';
			print_r($template->errors);
			die('fuck!');
		}
        return $this->redirect(['index', 'class' => $classID]);
    }

    /**
     * Finds the TemplateValues model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TemplateValues the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TemplateQuery::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
