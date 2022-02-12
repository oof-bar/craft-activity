<?php

namespace oofbar\activity\models;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Model;
use craft\records\Element as ElementRecord;

class Event extends Model
{
    /**
     * @var int ID
     */
    public $id;

    /**
     * @var int Related Element’s ID.
     */
    public $elementId;

    /**
     * @var string Event name/category, used for grouping.
     */
    public $category;

    /**
     * @var int Abstract “value” associated with the event. Used for tallying or weighting, within a category.
     */
    public $value = 1;

    /**
     * @var string Arbitrary text attached to the event. Not designed for direct querying, but useful for debugging (i.e. recording exceptions).
     */
    public $detail;

    /**
     * @var \DateTime Date the event was pushed.
     */
    public $dateCreated;

    /**
     * @var \DateTime Date the event was last updated. Should be the same as {@see self::dateCreated}.
     */
    public $dateUpdated;

    /**
     * @var string Auto-generated UID, stable throughout the record's life.
     */
    public $uid;

    /**
     * @var Element|null Memoized Element, if one was associated.
     */
    private $_element;

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        $rules = [];

        $rules[] = [['category', 'value'], 'required'];

        $rules[] = [
            ['category'],
            'string',
            'max' => 255,
        ];

        $rules[] = [['value'], 'integer'];

        // Due to the foreign key + index, the database will reject this if we don't check it ahead of time:
        $rules[] = [
            ['elementId'],
            'exist',
            'targetClass' => ElementRecord::class,
            'targetAttribute' => ['elementId' => 'id'],
        ];

        return $rules;
    }

    /**
     * Associates the Event with the provided Element or Element ID.
     * 
     * Passing `null` disassociates the Event from any previously-assigned Element.
     * 
     * @param ElementInterface|int|null $element
     */
    public function setElement($element)
    {
        if ($element instanceof Element) {
            $this->elementId = $element->id;
        }

        $this->elementId = $element;
    }

    /**
     * Returns the associated Element, if one is defined.
     * 
     * @return Element|null
     */
    public function getElement()
    {
        if (is_null($this->elementId)) {
            return null;
        }

        $this->_element = Craft::$pp->getElements()->getElementById($this->elementId);

        return $this->_element;
    }
}
