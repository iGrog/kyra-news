<?php

namespace kyra\news\models;

use kyra\image\models\Image;
use Yii;

/**
 * This is the model class for table "news".
 *
 * @property string $NID
 * @property string $Title
 * @property string $DateOf
 * @property string $HeaderIID
 * @property string $ContentJSON
 * @property string $ContentHTML
 * @property integer $IsVisible
 * @property string $SmallDesc
 *
 */
class News extends \yii\db\ActiveRecord
{
    const EVENT_NEWS_CREATED = 'Kyra.News.eventNewsCreated';
    const EVENT_NEWS_UPDATED = 'Kyra.News.eventNewsUpdated';
    const EVENT_NEWS_DELETED = 'Kyra.News.eventNewsDeleted';

    public $image = ''; // Для показывания нормальной картинки заголовка

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Title', 'UrlKey', 'DateOf', 'SmallDesc'], 'required'],
            [['DateOf', 'GalleryID', 'Subtitle'], 'safe'],
            [['HeaderIID', 'IsVisible'], 'integer'],
            [['ContentJSON', 'ContentHTML'], 'string'],
            [['Title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'NID' => 'Nid',
            'Title' => 'Заголовок',
            'DateOf' => 'Дата новости',
            'HeaderIID' => 'Картинка новости',
            'SmallDesc' => 'Короткое описание',
            'ContentJSON' => 'Содержимое',
            'ContentHTML' => 'Cодержимое',
            'IsVisible' => 'Новость видна?',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHeaderImage()
    {
        return $this->hasOne(Image::className(), ['IID' => 'HeaderIID']);
    }

    public function AddNews()
    {
        return $this->save(false);
    }

    public function UpdateNews()
    {
        return $this->update(false);
    }

    public function beforeSave()
    {
        $time = strtotime($this->DateOf);
        $this->DateOf = date('Y-m-d H:i:s', $time);
        return true;
    }

    public function DeleteNews()
    {
        $this->delete();
        return true;
    }

}
