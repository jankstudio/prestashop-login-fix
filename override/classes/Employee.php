<?php

class Employee extends EmployeeCore
{
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'employee',
        'primary' => 'id_employee',
        'fields' => array(
            'lastname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'firstname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'email' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'passwd' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isPasswdAdmin', 'required' => true, 'size' => 60),
            'last_passwd_gen' =>            array('type' => self::TYPE_STRING),
            'active' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'optin' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'id_profile' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'bo_color' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),
            'default_tab' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'bo_theme' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 32),
            'bo_css' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
            'bo_width' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'bo_menu' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'stats_date_from' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_date_to' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_compare_from' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_compare_to' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'stats_compare_option' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'preselect_date_range' =>        array('type' => self::TYPE_STRING, 'size' => 32),
            'id_last_order' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_last_customer_message' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_last_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
        ),
    );

    /**
     * Return employee instance from its e-mail (optionnaly check password)
     *
     * @param string $email e-mail
     * @param string $passwd Password is also checked if specified
     * @param bool $active_only Filter employee by active status
     * @return Employee instance
     */
    public function getByEmail($email, $passwd = null, $active_only = true)
    {
        if (!Validate::isEmail($email) || ($passwd != null && !Validate::isPasswd($passwd))) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->getRow('
        SELECT *
        FROM `'._DB_PREFIX_.'employee`
        WHERE `email` = \''.pSQL($email).'\'
        '.($active_only ? ' AND `active` = 1' : ''));

        // .($passwd !== null ? ' AND `passwd` = \''.Tools::encryptPass($passwd).'\'' : ''));

        if (!$result) {
            return false;
        }

        // validate password
        if ($passwd !== null) {
            if (!Tools::verifyPass($passwd, $result['passwd']))
                return false;;
        }

        $this->id = $result['id_employee'];
        $this->id_profile = $result['id_profile'];
        foreach ($result as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    public function setWsPasswd($passwd)
    {
        if ($this->id != 0) {
            if ($this->passwd != $passwd) {
                $this->passwd = Tools::encryptPass($passwd);
            }
        } else {
            $this->passwd = Tools::encryptPass($passwd);
        }
        return true;
    }
}
