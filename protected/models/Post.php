<?php

/**
 * @version   Article.php  17:53 2013年09月11日
 * @author    poctsy <poctsy@foxmail.com>
 * @copyright Copyright (c) 2013 poctsy
 * @link      http://www.fircms.com
 */


/**
 * This is the model class for table "{{post}}".
 *
 * The followings are the available columns in table '{{post}}':
 * @property integer $id
 * @property integer $catalog_id
 * @property string $title
 * @property string $title_s
 * @property string $subtitle
 * @property string $keyword
 * @property string $thumb
 * @property string $description
 * @property integer $user_id
 * @property integer $view_count
 * @property string $create_time
 * @property string $content
 * @property string $images
 * @property string $soft
 *
 */
class Post extends FActiveRecord {

    /**
     * @return string the associated database table name
     */


    public $catalog_id;
    public $thumb_file;
    public $soft_file;


    public function tableName() {
        return '{{post}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(

            array('catalog_id,content,images,soft ','safe'),
            array('title,catalog_id', 'required'),
            array('user_id, view_count,catalog_id,create_time', 'numerical', 'integerOnly'=>true),
            array('create_time', 'length',  'max'=>11),
            array('thumb', 'length', 'max'=>100),
            array('title, subtitle,title_s', 'length', 'max'=>50),
            array('keyword, description', 'length', 'max'=>30),

            array('title,subtitle, title_s,thumb,keyword,description ', 'filter', 'filter' => array($this, 'Purify')),
            array('content,images,soft', 'filter', 'filter' => array($this, 'contentPurify')),

            // @todo Please remove those attributes that should not be searched.
            array('id,title, content,thumb,catalog_id', 'safe', 'on' => 'search'),
            array('thumb_file', 'file', 'allowEmpty'=>true,
                'types'=>'jpg, jpeg, gif, png',
                'maxSize' => 1024 * 1024 * 3, // 3MB         以字节计算 b  kb mb
                'tooLarge'=>'上传文件超过 3MB，无法上传',
            ),
            array('soft_file', 'file', 'allowEmpty'=>true,
                'types'=>'zip,rar,exe',
                'maxSize' => 1024 * 1024 * 20, // 1MB
                'tooLarge'=>'上传文件超过 20MB，无法上传',
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'catalog'=>array(self::BELONGS_TO, 'Catalog', 'catalog_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'catalog_id' => '所属栏目',
            'title' => '标题',
            'title_s' => 'SEO标题',
            'keyword' => 'SEO关键字',
            'thumb' => '缩略图',
            'description' => 'SEO描述',
            'content' => '内容',
            'images' => '图片',
            'subtitle'=>'副标题',
            'soft' => '文件',
            'create_time'=>'创建时间',
            'thumb_file'=>'缩略图',
            'soft_file'=>'文件',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('title', $this->title, true);
        $criteria->compare('subtitle', $this->subtitle, true);
        $criteria->compare('id', $this->id);
        $criteria->compare('catalog_id', $this->catalog_id, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('thumb', $this->thumb, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination'=>array(
                'pageSize'=>20,
            )
        ));
    }




    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Article the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }



    public function catalogLookup(){
        return $this->catalog->name;
    }
    public function beforeSave(){
        if($this->user_id==NULL){
            $this->user_id="";
        }

        return true;
    }


    public function getList_view($name){
        $catalog=Catalog::nameGet($name);
        return $catalog->list_view;
    }


}
