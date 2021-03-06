<?php
/**
 * MessageEditForm.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Postman\backend\forms;

use cookyii\modules\Postman\resources\PostmanMessage\Model as PostmanMessageModel;
use yii\helpers\Json;

/**
 * Class MessageEditForm
 * @package cookyii\modules\Postman\backend\forms
 */
class MessageEditForm extends \cookyii\base\FormModel
{

    use \cookyii\traits\PopulateErrorsTrait;

    /** @var PostmanMessageModel */
    public $Message;

    public $subject;
    public $content_text;
    public $content_html;
    public $address;
    public $error;
    public $scheduled_at;

    public function init()
    {
        if (!($this->Message instanceof PostmanMessageModel)) {
            throw new \yii\base\InvalidConfigException(\Yii::t('cookyii.postman', 'Not specified message to edit.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /** type validators */
            [['subject', 'content_text', 'content_html', 'error', 'scheduled_at'], 'string'],

            /** semantic validators */
            [['subject'], 'required'],
            [['subject'], 'filter', 'filter' => 'str_clean'],
            [['content_text', 'content_html'], 'filter', 'filter' => 'str_pretty'],
            [['address'], 'safe'],

            /** default values */
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subject' => \Yii::t('cookyii.postman', 'Subject'),
            'content_text' => \Yii::t('cookyii.postman', 'Plain content'),
            'content_html' => \Yii::t('cookyii.postman', 'HTML content'),
            'address' => \Yii::t('cookyii.postman', 'Address'),
            'error' => \Yii::t('cookyii.postman', 'Sending error'),
            'scheduled_at' => \Yii::t('cookyii.postman', 'Scheduled at'),
        ];
    }

    /**
     * @return array
     */
    public function formAction()
    {
        return ['/postman/rest/message/edit'];
    }

    /**
     * @return bool
     */
    public function isNewMessage()
    {
        return $this->Message->isNewRecord;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $Message = $this->Message;

        $address = [];
        if (!empty($this->address) && is_array($this->address)) {
            foreach ($this->address as $addr) {
                if ($addr === null || empty($addr['email'])) {
                    continue;
                }

                $address[] = $addr;
            }
        }

        $params = [];
        if (!empty($this->params) && is_array($this->params)) {
            foreach ($this->params as $param) {
                if ($param === null || empty($param['key'])) {
                    continue;
                }

                $params[] = $param;
            }
        }

        /** @var PostmanMessageModel $MessageModel */
        $MessageModel = \Yii::createObject(PostmanMessageModel::className());

        if ($Message->isNewRecord) {
            $Message = $MessageModel::compose($this->subject, $this->content_text, $this->content_html);
        } else {
            $Message->subject = $this->subject;
            $Message->content_text = $this->content_text;
            $Message->content_html = $this->content_html;
        }

        $Message->error = $this->error;
        $Message->scheduled_at = empty($this->scheduled_at) ? null : strtotime($this->scheduled_at);
        $Message->address = Json::encode($address);

        $result = $Message->validate() && $Message->save();

        if ($Message->hasErrors()) {
            $this->populateErrors($Message, 'subject');
        }

        $this->Message = $Message;

        return $result;
    }
}