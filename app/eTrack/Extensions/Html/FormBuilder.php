<?php namespace eTrack\Extensions\Html;

use Illuminate\Html\FormBuilder as IlluminateFormBuilder;

class FormBuilder extends IlluminateFormBuilder
{
    /**
     * @param string $name
     * @param string $label
     * @param null   $value
     * @param array  $options
     * @return string
     */
    public function textField($name, $label, $value = null, $options = array())
    {
        return $this->field('text', $name, $label, $value, $options);
    }

    /**
     * @param string $name
     * @param string $label
     * @param array  $options
     * @return string
     */
    public function passwordField($name, $label, $options = array())
    {
        return $this->field('password', $name, $label, '', $options);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $options
     * @return string
     */
    public function submitField($name, $value, $options = array())
    {
        $options = array_merge(array('class' => 'button'), $options);

        return $this->actionField($name, $value, $options);
    }

    /**
     * @param $html
     * @return string
     */
    private function wrap($html)
    {
        return "<div class=\"group\">{$html}</div>";
    }

    /**
     * @param $html
     * @return string
     */
    private function actionWrap($html)
    {
        return "<p class=\"action group\">{$html}</p>";
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $label
     * @param string $value
     * @param array  $options
     * @return string
     */
    private function field($type, $name, $label, $value, $options = array())
    {
        $html = $this->label($name, ucwords($label));
        $html .= $this->input($type, $name, $value, $options);

        return $this->wrap($html);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $options
     * @return string
     */
    private function actionField($name, $value, $options = array())
    {
        $options = array_merge(array('name' => $name), $options);

        $html = $this->submit($value, $options);

        return $this->actionWrap($html);
    }
}