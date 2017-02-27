<?php

namespace frontend\controllers;

use Yii;
//use frontend\models\Templates;
use frontend\models\ObjectValues;
use frontend\models\Classes;
use frontend\models\ObjectFields;
use frontend\models\ObjectQuery;
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
				'query' => ObjectQuery::find()->where(['class_id' => $class]),
			]);
			return $this->render('index', [
				'dataProvider' => $dataProvider,
				'fields' => array_merge(['id', 'name'], ObjectFields::find()->select('name')->where(['class_id' => $class])->column()),
				'classModel' => Classes::findOne($class),
			]);
		} else {
			$dataProvider = new ActiveDataProvider([
				'query' => Classes::find(),
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
        $model = new ObjectValues();

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
    public function actionUpdate($class = 0, $id = 0, $page = null, $block = false) {
		if ($id) {
			$model = $this->findModel($id);
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
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
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
     * Deletes an existing TemplateValues model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $object = $this->findModel($id);
		$classID = $object->classID;
		$d = $object->delete();
		if (! $d) {
			echo "не удалось удалить, чёт не так (AdminController:actionDelete)<br/>\n";
			print_r($d); echo " - deleted rows<br/>\nAR object:\n";
			print_r($object); echo "<br/>\n";
			echo $object->beforeDelete();
//			echo $object->create
			die('fuck!');
		}
        return $this->redirect(['index', 'class' => $classID]);
    }

    /**
     * Finds the TemplateValues model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ObjectValues the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ObjectQuery::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionImport() {
		ObjectQuery::importFromJoomla();
	}
	
	public function actionGalleries() {
		ObjectQuery::createGalleries();
	}
}
