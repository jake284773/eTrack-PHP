<?php namespace eTrack\Extensions\Html;

use Illuminate\Html\FormBuilder as IlluminateFormBuilder;
use Illuminate\Support\ViewErrorBag;

class FormBuilder extends IlluminateFormBuilder
{
    public function formOpen(ViewErrorBag $errors, array $options = array())
    {
        $html = parent::open($options);

        if ($errors->has())
        {
            $html = $this->makeErrorBox($errors, $html);
        }

        return $html;
    }

    /**
     * @param string $name
     * @param string $label
     * @param null $value
     * @param \Illuminate\Support\ViewErrorBag $errors
     * @param array $options
     * @return string
     */
    public function textField($name, $label, $value = null, ViewErrorBag $errors, $options = array())
    {
        return $this->field('text', $name, $label, $value, $errors, $options);
    }

    /**
     * @param string $name
     * @param string $label
     * @param \Illuminate\Support\ViewErrorBag $errors
     * @param array $options
     * @return string
     */
    public function passwordField($name, $label, ViewErrorBag $errors, $options = array())
    {
        return $this->field('password', $name, $label, '', $errors, $options);
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
     * @param $error
     * @return string
     */
    private function wrap($html, $error)
    {
        if ($error)
        {
            return "<div class=\"group validation\">{$html}</div>";
        }

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
     * @param \Illuminate\Support\ViewErrorBag $errors
     * @param array $options
     * @return string
     */
    private function field($type, $name, $label, $value, ViewErrorBag $errors, $options = array())
    {
        $html = $this->label($name, ucwords($label));

        if ($errors->has($name))
        {
            $error = $errors->get($name)[0];
            $html .= "<span class=\"validation-message\">{$error}</span>";
        }
        else
        {
            $error = null;
        }

        $html .= $this->input($type, $name, $value, $options);

        return $this->wrap($html, $error);
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

    /**
     * @param ViewErrorBag $errors
     * @param $html
     * @return string
     */
    private function makeErrorBox(ViewErrorBag $errors, $html)
    {
        $html .= "<div class=\"error\">";
        $html .= "<h3>There was a problem submitting the form</h3>";
        $html .= "<p>Because of the following problems:</p>";

        $html .= "<ol>";

        foreach ($errors->all() as $error)
        {
            $html .= "<li>{$error}</li>";
        }

        $html .= "</ol>";
        $html .= "</div>";
        return $html;
    }
}