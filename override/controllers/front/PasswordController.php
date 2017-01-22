<?php

class PasswordController extends PasswordControllerCore
{
    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('email')) {
            if (!($email = trim(Tools::getValue('email'))) || !Validate::isEmail($email)) {
                $this->errors[] = Tools::displayError('Invalid email address.');
            } else {
                $customer = new Customer();
                $customer->getByemail($email);
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = Tools::displayError('There is no account registered for this email address.');
                } elseif (!$customer->active) {
                    $this->errors[] = Tools::displayError('You cannot regenerate the password for this account.');
                /*
                } elseif ((strtotime($customer->last_passwd_gen.'+'.($min_time = (int)Configuration::get('PS_PASSWD_TIME_FRONT')).' minutes') - time()) > 0) {
                    $this->errors[] = sprintf(Tools::displayError('You can regenerate your password only every %d minute(s)'), (int)$min_time);
                */
                } else {
                    $this->emailToken($customer);
                }
            }
        } elseif (Tools::isSubmit('passwd')) {
            error_log('PASSWORD CHANGE!');
            $token = Tools::getValue('token');
            $id_customer = (int) Tools::getValue('id_customer');
            $email = Db::getInstance()->getValue('SELECT `email` FROM '._DB_PREFIX_.'customer c WHERE c.`secure_key` = \''.pSQL($token).'\' AND c.id_customer = '.(int)$id_customer);
            if ($email) {
                $customer = new Customer();
                $customer->getByemail($email);
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = Tools::displayError('Customer account not found');
                } elseif (!$customer->active) {
                    $this->errors[] = Tools::displayError('You cannot regenerate the password for this account.');
                } else {
                    $pass = Tools::getValue('passwd');
                    $pass2 = Tools::getValue('passwd_confirm');
                    if ($pass != $pass2) {
                        $this->errors[] = Tools::displayError("Passwords don't match");

                        $this->context->smarty->assign(array(
                            'confirmation' => 3,
                            'customer_email' => $customer->email,
                            'id_customer' => $id_customer,
                            'token' => $token,
                        ));
                    } else {
                        $customer->passwd = Tools::encryptPass($pass);
                        $customer->last_passwd_gen = date('Y-m-d H:i:s', time());
                        if ($customer->update()) {
                            Hook::exec('actionPasswordRenew', array('customer' => $customer, 'password' => $pass));
                            $mail_params = array(
                                '{email}' => $customer->email,
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{passwd}' => $pass
                            );
                            if (Mail::Send($this->context->language->id, 'password', Mail::l('Your new password'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname)) {
                                $this->context->smarty->assign(array(
                                    'confirmation' => 1,
                                    'customer_email' => $customer->email,
                                ));
                            }
                        }
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('We cannot regenerate your password with the data you\'ve submitted.');
            }
        } elseif (($token = Tools::getValue('token')) && ($id_customer = (int)Tools::getValue('id_customer'))) {
            $email = Db::getInstance()->getValue('SELECT `email` FROM '._DB_PREFIX_.'customer c WHERE c.`secure_key` = \''.pSQL($token).'\' AND c.id_customer = '.(int)$id_customer);
            if ($email) {
                $customer = new Customer();
                $customer->getByemail($email);
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = Tools::displayError('Customer account not found');
                } elseif (!$customer->active) {
                    $this->errors[] = Tools::displayError('You cannot regenerate the password for this account.');
                } else {
                /*
                    $customer->passwd = Tools::encrypt($password = Tools::passwdGen(MIN_PASSWD_LENGTH, 'RANDOM'));
                    $customer->last_passwd_gen = date('Y-m-d H:i:s', time());
                    if ($customer->update()) {
                        Hook::exec('actionPasswordRenew', array('customer' => $customer, 'password' => $password));
                        $mail_params = array(
                            '{email}' => $customer->email,
                            '{lastname}' => $customer->lastname,
                            '{firstname}' => $customer->firstname,
                            '{passwd}' => $password
                        );
                        if (Mail::Send($this->context->language->id, 'password', Mail::l('Your new password'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname)) {
                */
                    $this->context->smarty->assign(array(
                        'confirmation' => 3,
                        'customer_email' => $customer->email,
                        'id_customer' => $id_customer,
                        'token' => $token,
                    ));
                }
            } else {
                $this->errors[] = Tools::displayError('We cannot regenerate your password with the data you\'ve submitted.');
            }
        } elseif (Tools::getValue('token') || Tools::getValue('id_customer')) {
            $this->errors[] = Tools::displayError('We cannot regenerate your password with the data you\'ve submitted.');
        }
    }

    protected function emailToken($customer)
    {
        $url = $this->context->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id);
        error_log($url);
        $mail_params = array(
            '{email}' => $customer->email,
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{url}' => $url,
        );
        if (Mail::Send($this->context->language->id, 'password_query', Mail::l('Password query confirmation'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname)) {
            $this->context->smarty->assign(array('confirmation' => 2, 'customer_email' => $customer->email));
        } else {
            $this->errors[] = Tools::displayError('An error occurred while sending the email.');
        }
    }

    protected function processToken()
    {
    }
}
