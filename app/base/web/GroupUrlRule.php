<?php

namespace app\base\web;

/**
 * Fixed Yii GroupUrlRule
 */
class GroupUrlRule extends \yii\web\GroupUrlRule
{
    public $name;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->fixRules();
        if ($this->name === null) {
            $this->name = 'group rule ' . $this->prefix;
        }
        return parent::init();
    }

    /*
     * Fixes group rules.
     */
    public function fixRules()
    {
        foreach ($this->rules as $key => $rule) {
            if (!is_array($rule) || !isset($rule['prefix'])) {
                continue;
            }
            $rule['prefix'] = trim(trim($this->prefix, '/') . '/' . trim($rule['prefix'], '/'), '/');
            if (isset($rule['routePrefix'])) {
                $rule['routePrefix'] = trim(trim($this->routePrefix, '/') . '/' . trim($rule['routePrefix'], '/'), '/');
            } else {
                $rule['routePrefix'] = $rule['prefix'];
            }
            $this->rules[$key] = $rule;
        }
    }
}
