<?php
/**
 * Forms class is part of Nanobanano framework
 * wrapper for standart PHP $_SESSION
 *
 * PHP version 5
 *
 * @copyright 2012 Dmitry Elfimov
 * @license   http://www.elfimov.ru/nanobanano/license.txt MIT License
 * @link      http://elfimov.ru/nanobanano
 * 
 */
 
/**
 * Forms class
 *
 * @package Forms
 * @author  Dmitry Elfimov <elfimov@gmail.com>
 *
 */
 
class Forms
{

    private $_prefix = '';
	
	// CSRF protection is ON by default
	public $csrf = true;
    
    public $formEnctype = '';
    public $formMethod = 'post'; // 'post' or 'get'
    
    private $_salt = 'salt12345';
	
	public $_form = array();

    /**
     * Constructor.
     *
     */
    public function __construct()
	{

    }
	

    
    /**
     * Set form.
     *
     * @param array $form form
     *
     * @return no value is returned.
     */
	public function setForm($form)
	{
		$this->_form = $form;
	}
	
    /**
     * Set form prefix.
     *
     * @param string $prefix prefix
     *
     * @return no value is returned.
     */
	public function setPrefix($prefix) 
	{
		$this->_prefix = $prefix;
	}
	
    /**
     * Render form.
     *
     * @return form html code.
     */
	public function render() 
	{
        if ($this->csrf) {
            $this->_addCsrf();
        }
		foreach ($this->_form as $name => $field) {
			switch ($field['type']) {
			case 'select':
				$out .= $this->getSelect($name, $field);
				break;
			case 'radio':
				$out .= $this->getRadio($name, $field);
				break;
			case 'textarea':
				$out .= $this->getTextarea($name, $field);
				break;
			case 'file':
				$out .= $this->getFile($name, $field);
				break;
			case 'password':
				$out .= $this->getPassword($name, $field);
				break;
			case 'checkbox':
				$out .= $this->getCheckbox($name, $field);
				break;
			case 'hidden':
				$out .= $this->getHidden($name, $field);
				break;
			case 'text':
			default:
				$out .= $this->getText($name, $field);
				break;
			}
		}
	}
    
    /**
     * Adds Csrf value to form
     *
     * @return no value is returned.
     */
    private function _addCsrf()
    {
        $token = sha1(uniqid(rand()).$this->_salt);
        setcookie('_csrf', $token.time(), 0, '/');
        $this->_form['_csrf'] = array(
            'type'  => 'hidden',
            'value' => $token,
        );
    }
	
    /**
     * Validate form.
     *
     * @return array of errors
     */
	public function validate()
    {
		$errors = array();
        
        if ($this->formMethod=='post') {
            $sent = $_POST;
        } else {
            $sent = $_GET;
        }
        
        if (empty($sent)) {
            return $errors;
        }
        
        if ($this->csrf && !$this->_isValidCsrf()) {
            $errors['csrf'] = 'csrf';
            return $errors;
        }
        
        foreach ($this->_form as $name => &$field) {
            if (empty($sent[$name])) {
                if (!empty($field['required'])) {
                    $errors[$name] = 'required field is empty';
                }
            } else {
                if (is_array($field['values'])) {
                    if (is_array($sent[$name])) {
                        if ($field['type']=='checkbox') {
                            foreach ($field['values'] as $id => $value) {
                                if (in_array($value, $sent[$name]) {
                                    $values[] = $value;
                                }
                            }
                        }
                    } else {
                        if (in_array($sent[$name], $field['values']) {
                            $values = $sent[$name];
                        }
                    }
                    if (empty($values)) {
                        $errors[$name] = 'incorrect value';
                    } else {
                        $field['value'] = $values;
                    }
                } else if (!empty($field['filter'])) {
                    $filter = $this->_validateFilter($sent[$name], $field);
                    if ($filter === false) {
                        $errors[$name] = 'incorrect value';
                    } else {
                        $field['value'] = $filter;
                    }
                }
            }
        }
		return $errors;
	}
    
    /**
     * Validates field with filter_var(). 
     * See http://php.net/manual/function.filter-var.php
     *
     * @return filtered value or false
     */
    private function _validateFilter($value, $field) {
        $options = empty($field['filter']['options']) ? null : $field['filter']['options'];
        $flags = empty($field['filter']['flags']) ? null : $field['filter']['flags'];
        return filter_var($value, $field['filter']['filter'], $options, $flags);
    }
    
    /**
     * Check if csrf is valid or not.
     *
     * @return true or false
     */
    public function _isValidCsrf()
    {
        if (!empty($_COOKIE['_csrf']) && strlen($_COOKIE['_csrf'])>40) {
            $csrf = substr($_COOKIE['_csrf'], 0, 40);
            $time = (int) substr($_COOKIE['_csrf'], 40);
            setcookie('_csrf', 0, time()-3600, '/');
            return $csrf==$this->getSentValue('_csrf') && ($time+86400)>time();
        } else {
            return false;
        }
    }

    /**
     * Get sent value
     *
     * @param string $name form element name
     *
     * @return true or false
     */
    public function getSentValue($name)
    {
        if ($this->formMethod=='post') {
            return isset($_GET[$this->prefix.$name]) ? $_GET[$this->prefix.$name] : null;
        } else {
            return isset($_POST[$this->prefix.$name]) ? $_POST[$this->prefix.$name] : null;
        }
    }

    
    /**
     * Get select input.
     *
     * @param string $name   form element name
	 * @param array  $field  form element description
     *
     * @return html select
     */
    public function getSelect($name, $field)
	{
		$active = isset($field['active']) ? $field['active'] : null;

        $out = '';
        $out .= '<select '.$this->_getTagAttributes($name, $field).'">';
        foreach ($field['values'] as $id => $value) {
            $out .= '<option value="'.$id.'"'.($id==$active?' selected="selected"':'').'>'.$value.'</option>';
        }
        $out .= '</select>';
        return $out;
    }
    
    /**
     * Get file input.
     *
     * @param string $name form element
     *
     * @return html file input field
     */
    public function getFile($name, $field)
	{
        $this->formEnctype = 'multipart/form-data';
		$out = '<input '.$this->_getTagAttributes($name, $field).' value="'.$this->_getValue($field).'">';
        return $out;
    }
    
    /**
     * Get text input.
     *
     * @param string $name form element
     *
     * @return html text input field
     */
    public function getText($name, $field)
	{
		$out = '<input '.$this->_getTagAttributes($name, $field).' value="'.$this->_getValue($field).'">';
        return $out;
    }
    
    /**
     * Get textarea input.
     *
     * @param string $name form element
     *
     * @return html textarea input field
     */
    public function getTextarea($name, $field)
	{
		$out = '<textarea '.$this->_getTagAttributes($name, $field).'>'.$this->_getValue($field).'</textarea>';
        return $out;
    }
    
    
    /**
     * Get hidden input.
     *
     * @param string $name form element
     *
     * @return html hidden input field
     */
    public function getHidden($name, $field)
	{
		$out = '<input '.$this->_getTagAttributes($name, $field).' value="'.$this->_getValue($field).'">';
        return $out;
    }
    
    /**
     * Get checkbox input.
     *
     * @param string $name form element
     *
     * @return html checkbox input field
     */
    public function getCheckbox($name, $field)
	{
        $checkedValue = $this->_getValue($field);
        $out = '';
        foreach ($field['values'] as $id => $value) {
            $out .= '<input '.$this->_getTagAttributes($name, $field, $id)
                .' value="'.$value.'"'
                .($checkedValue==$value ? ' checked="checked"' : '')
                .'>';
        }
        return $out;
    }
    
    /**
     * Get radiobutton input.
     *
     * @param string $name form element
     *
     * @return html radiobutton input field
     */
    public function getRadio($name, $field)
	{
        $checkedValue = $this->_getValue($field);
        $out = '';
        foreach ($field['values'] as $id => $value) {
            $out .= '<input '.$this->_getTagAttributes($name, $field, $id)
                .' value="'.$value.'"'
                .($checkedValue==$value ? ' checked="checked"' : '')
                .'>';
        }
        return $out;
    }
    
    /**
     * Get password input.
     *
     * @param string $name form element
     *
     * @return html password input field
     */
    public function getPassword($name, $field)
	{
		$out = '<input '.$this->_getTagAttributes($name, $field).'>';
        return $out;
    }
    
    /**
     * Get input field value.
     *
     * @param string $name form element
     *
     * @return value
     */
    private function _getValue($field) {
        if (!empty($field['value'])) {
            $value = $field['value'];
        } else if (!empty($field['default'])) {
            $value = $field['default'];
        } else {
            $value = '';
        }
        return $value;
    }
    
    private function _getTagAttributes($name, $field, $id=null) {
        if ($field['type']=='select' || $field['type']=='textarea') {
            $type = '';
        } else {
            $type = 'type="'.$field['type'].'" ';
        }
        if ($field['type']=='checkbox') {
            $name = 'id="'.$this->prefix.$name.'_'.$id.'" name="'.$this->prefix.$name.'[]"';
        } else if ($field['type']=='radio') {
            $name = 'id="'.$this->prefix.$name.'_'.$id.'" name="'.$this->prefix.$name.'"';
        } else {
            $name = 'id="'.$this->prefix.$name.'" name="'.$this->prefix.$name.'"';
        }
        $out = $type
            .$name
            .(empty($field['length']) ? '' : ' length="'.$field['length'].'"') 
        retun $out;
    }
    
}