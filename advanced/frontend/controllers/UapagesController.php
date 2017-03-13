<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Pages;
use frontend\models\ObjectQuery;
use frontend\models\ObjectFields;
use frontend\models\Classes;
use frontend\models\PageFields;
use frontend\models\Templates;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
//use frontend\models\Templates;
use pendalf89\filemanager\models\MediafileSearch;
//use 

/**
 * UAPagesController implements the CRUD actions for Pages model.
 */
class UapagesController extends Controller
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
     * Lists all Pages models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Pages::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pages model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if (1 == $id || 0 == $id) { // HARDCODED id for global fields page
			$page = (object) [
				'title' => 'Global fields',
				'id' => 1,
			];
			$objects = ObjectQuery::getPageByURI('home', true);
		} else {
			$page = $this->findModel($id);
			$objects = [];
			if ($page->template) {
				// заполняем список объектов пустыми объектами для этого шаблона
				foreach ($page->template->templateClasses as $tc) {
					$objects[$tc->name] = $tc->class_id; /*new ObjectQuery();
					$objects[$tc->name]->setClass($tc->class_id);
					$objects[$tc->name]->name = $tc->name;*/
				}
				// грузим из базы что есть
				foreach (ObjectQuery::getPageByURI($page->url, false) as $field) {
					$objects[$field->name] = $field;
				}
			}
		}
		$ret = ObjectQuery::constructMenusArrangeObjects($objects);
        return $this->render('view', [
            'pageModel' => $page,
			'objects' => $ret['objectsByName'],
			'menus' => $ret['menus'],
			'fileModel' => new MediafileSearch(),
        ]);
    }

    /**
     * Updates an existing Pages model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = 0)
    {
		if (0 == $id) {
			$model = new Pages();
		} else {
			$model = $this->findModel($id);
		}
		$templates = [];
		foreach (Templates::find()->all() as $item) {
			$templates[$item->id] = $item->name;
		}
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'templates' => $templates,
            ]);
        }
    }

    /**
     * Updates an existing ObjectValues model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $class for create case
	 * @param integer $id for update case
	 * @param integer $page owner id
	 * @param boolean $block hides all pages checkboxes
	 * @param integer $gobackid ugly implementation of callstack. After save will redirect to view specified id or will redirect to view saved id if not specified
     * @return mixed
     */
    public function actionUpdatefield($class = 0, $id = 0, $page = null, $block = true, $gobackid = null) {
//		die(123);
		if ($id) {
			$model = $this->findObjectModel($id);
			$class = $model->classID;
		} else {
			$model = new ObjectQuery();
			$model->setClass($class);
			$model->name = Yii::$app->request->get('name', '');
		}
		$classModel = Classes::findOne($class);
		if (null === $classModel) { throw new NotFoundHttpException('The requested page does not exist.'); }
		$allPagesList = array();
		$allPagesCheckboxen = array();
		// actually we're using only one page-owner for any field
		$checkedPages = array_flip(PageFields::find()->where(['object_id' => $model->id])->select('page_id')->column());
		foreach (Pages::find()->orderBy('title')->all() as $p) {
			$caption = "{$p->title} ({$p->url})";
			if (isset($page)) { $checked = $page == $p->id; }
			else { $checked = isset($checkedPages[$p->id]); }
			$allPagesCheckboxen[$p->id] = array(
				'caption' => $caption,
				'checked' => $checked,
			);
			$allPagesList[$p->id] = $caption;
		}
		$fields = ObjectFields::find()->where(['class_id' => $class])->all();
		$allowedClasses = array();
		foreach ($fields as $f) {
			if ('object' == $f->namedType->type_name) {
				$ca = array();
				foreach ($f->allowedObjects as $classAllowed) {
					if (! array_key_exists($classAllowed->class->name, $ca)) { $ca[$classAllowed->class->name] = array(); }
					$ca[$classAllowed->class->name][$classAllowed->id] = $classAllowed->name;/*$this->renderPartial(
							'classtpl/' . $classAllowed->class->name,
							['data' => $classAllowed->class]
					);*/
				}
				$allowedClasses[$f->name] = $ca;
			} //elseif ('')
		}
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			// we need to save page relations here
			// fuck! Why ?? operator only in php7? =(
			$pages = Yii::$app->request->post();
			if (isset($pages['pages'])) { $pages = $pages['pages']; }
			else { $pages = []; }
			$transaction = PageFields::getDb()->transaction(function ($db) use ($model, $pages) {
				PageFields::deleteAll(['object_id' => $model->id]);
				$rows = array();
				foreach ($pages as $page => $value) {
					$rows[] = array(
						'page_id' => $page,
						'object_id' => $model->id,
					);
				}
//				print_r($rows); die();
				PageFields::getDb()->createCommand()->batchInsert(PageFields::tableName(), (new PageFields())->attributes(), $rows)->execute();
			});
//			print_r(Yii::$app->request->post()['pages']); die();
			$id = $model->id; // ugly stack implementation
			if (isset($gobackid)) $id = $gobackid;
            return $this->redirect(['view', 'id' => $id]);
        } else {
            return $this->render('updatefield', [
                'model' => $model,
				'classModel' => $classModel,
				'fields' => $fields,
				'allowedClasses' => $allowedClasses,
				'allPagesList' => $allPagesList,
				'allPagesCheckboxen' => $allPagesCheckboxen,
				'ownerPages' => $checkedPages,
				'block' => (boolean) $block,
            ]);
        }
    }
	
    /**
     * Deletes an existing Pages model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	
	public function actionError() {
		$error = Yii::app()->errorHandler->error;
		print_r($error);
	}

    /**
     * Finds the Pages model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pages the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pages::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the ObjectValues model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ObjectValues the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findObjectModel($id)
    {
        if (($model = ObjectQuery::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
}