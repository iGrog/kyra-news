<?php
    use kyra\image\models\Image;
    use yii\helpers\Url;

?>

<?= yii\grid\GridView::widget(['dataProvider' => $dp,
    'columns' => [
        'HeaderIID' => [
            'format' => 'raw',
            'value' => function ($data)
            {
                if ($data->headerImage)
                {
                    $img = Image::GetImageUrl($data->headerImage->attributes, $this->context->module->uploadPathKey, 'preview');
                    return '<img src="' . $img . '" style="max-width: 200px; max-height: 150px;"  />';
                } else return '-';

            },
        ],

        'DateOf', 'Title', 'SmallDesc',
        [
            'class' => \yii\grid\ActionColumn::className(),
            'urlCreator' => function ($type, $data)
            {
                if ($type == 'view') return Url::to(['/kyra.news/default/edit', 'nid' => $data['NID']]);
                else if ($type == 'update') return Url::to(['/kyra.news/default/edit', 'nid' => $data['NID']]);
                else if ($type == 'delete') return Url::to(['/kyra.news/default/delete', 'nid' => $data['NID']]);
            },
        ]
    ]
]);