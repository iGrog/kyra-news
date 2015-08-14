<?php if (Yii::$app->session->hasFlash('news')) : ?>

    <div class="alert">
        <?= Yii::$app->session->getFlash('news'); ?>
    </div>

<?php endif; ?>

<?php
    use dosamigos\datepicker\DatePicker;
    use dosamigos\datetimepicker\DateTimePicker;
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use kyra\common\PreviewForm;
    use yii\helpers\Url;

    $form = PreviewForm::begin(
        ['previewUrl' => ['/news/preview'],
         'previewButton' => 'preview',
    ]); ?>

<?php if (!$model->isNewRecord) echo $form->field($model, 'NID')->hiddenInput()->label(false); ?>
<?= $form->field($model, 'IsVisible')->checkbox(); ?>
<?= $form->field($model, 'HeaderIID')->widget(\kyra\common\Image2HiddenField::className(),
    ['uploadPath' => Url::to(['/kyra.image/default/upload']),
        'addParams' => ['path' => $this->context->module->uploadPathKey],
        'image' => $model->image]); ?>

<?php if(!$model->isNewRecord && !empty($model['HeaderIID'])) : ?>

    <a href="<?=Url::to(['/kyra.image/default/crop', 'iid' => $model['HeaderIID'], 'upk' => 'news']); ?>">редактировать кропы</a>

<?php endif; ?>

<?= $form->field($model, 'UrlKey'); ?>
<?= $form->field($model, 'Title') ?>
<?= $form->field($model, 'Subtitle') ?>
<?= $form->field($model, 'DateOf')->widget(
    DateTimePicker::className(), [
        'pickButtonIcon' => 'glyphicon glyphicon-time',
        'inline' => false,
        'clientOptions' => [
            'weekStart' => 1,
            'format' => 'yyyy-mm-dd hh:ii:ss', // if inline = false
            'todayBtn' => true
        ]
    ]) ?>
<?= $form->field($model, 'SmallDesc')->textarea(['rows' => 4]) ?>
<?= $form->field($model, 'GalleryID')->dropDownList($galleries, ['prompt' => '-Select-']); ?>
<?= $form->field($model, 'ContentJSON')->widget(\kyra\steditor\StEditor::className()); ?>
<?= Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-primary']) ?>


<?php PreviewForm::end(); ?>
