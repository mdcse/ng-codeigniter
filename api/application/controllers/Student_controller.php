<?php
defined('BASEPATH') or exit('DIRECT SCRIPT ACCESS IS NOT ALLOWED');

class Student_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Student_model');
    }

    public function add()
    {

        if ($this->input->method() == 'post') {
            $token = $this->input->request_headers()['token'];
            if ($this->Student_model->_is_login($token)) {
                $data = json_decode(file_get_contents('php://input'));
                $obj = $this->Student_model->if_exist($data);
                if ($obj) {
                    $context['data'] = $obj;
                    $context['error'] = 'user already exist';
                    echo json_encode($context);
                } else {
                    $context['data'] = $data->email;
                    $context['error'] = '';
                    $this->Student_model->_insert($data);
                    echo json_encode($context);
                }
            } else {
                $context['error'] = 'Login please';
                echo json_encode($context);
            }
        }
    }

    public function get()
    {
        $token = $this->input->request_headers()['token'];
        if ($this->Student_model->_is_login($token)) {
            // to show the data in list
            if ($this->input->method() == 'get') {
                $obj = $this->Student_model->_get();
                echo json_encode($obj);
            }
            // to show the data in update form
            if ($this->input->method() == 'post') {
                $data = json_decode(file_get_contents('php://input'));
                $obj = $this->Student_model->get_by_id($data->id);
                print_r(json_encode($obj));
            }
        } else {
            $context['error'] = 'Login please';
            echo json_encode($context);
        }
    }

    public function search()
    {
        $token = $this->input->request_headers()['token'];
        if ($this->Student_model->_is_login($token)) {
            if ($this->input->method() == 'post') {
                $data = json_decode(file_get_contents('php://input'));
                $obj = $this->Student_model->_search($data->key);
                print_r(json_encode($obj));
            }
        } else {
            $context['error'] = 'Login please';
            echo json_encode($context);
        }
    }

    public function delete()
    {
        $token = $this->input->request_headers()['token'];
        if ($this->Student_model->_is_login($token)) {
            if ($this->input->method() == 'post') {
                $data = json_decode(file_get_contents('php://input'));
                $this->Student_model->_delete($data->id);
            }
        } else {
            $context['error'] = 'Login please';
            echo json_encode($context);
        }
    }

    public function update()
    {
        $token = $this->input->request_headers()['token'];
        if ($this->Student_model->_is_login($token)) {
            if ($this->input->method() == 'post') {
                $data = json_decode(file_get_contents('php://input'));
                $udata = array(
                    'name' => $data->name,
                    'username' => $data->username,
                    'email' => $data->email,
                    'password' => $data->password
                );

                $emailexist = $this->Student_model->if_email_exist($data->email);
                $usernameexist = $this->Student_model->if_username_exist($data->username);
                // if user is not changing his email and username
                if ($udata['email'] == $data->baseemail && $udata['username'] == $data->baseusername) {
                    $this->Student_model->_update($udata, $data->baseid);
                    $context['message'] = "Success";
                    echo json_encode($context);
                // if user is changing both
                } else if ($udata['email'] != $data->baseemail && $udata['username'] != $data->baseusername) {
                    if (!$emailexist && !$usernameexist) {
                        $this->Student_model->_update($udata, $data->baseid);
                        $context['message'] = "Success";
                        echo json_encode($context);
                    } else {
                        $context['error'] = "Both";
                        echo json_encode($context);
                    }
                //if user is changing only email
                } else if ($udata['email'] != $data->baseemail &&  $udata['username'] == $data->baseusername) {
                    if ($emailexist) {
                        $context['error'] = "Email";
                        echo json_encode($context);
                    } else {
                        $this->Student_model->_update($udata, $data->baseid);
                        $context['message'] = "Success";
                        echo json_encode($context);
                    }
                // if user is changing username only
                } else if ($udata['email'] == $data->baseemail && $udata['username'] != $data->baseusername) {
                    if ($usernameexist) {
                        $context['error'] = "Username";
                        echo json_encode($context);
                    } else {
                        $this->Student_model->_update($udata, $data->baseid);
                        $context['message'] = "Success";
                        echo json_encode($context);
                    }
                }
            }
        } else {
            $context['error'] = 'Login please';
            echo json_encode($context);
        }
    }

    public function login()
    {
        if ($this->input->method() == 'post') {
            $data = json_decode(file_get_contents('php://input'));
            $obj = $this->Student_model->_authenticate($data);
            if ($obj) {
                $string = bin2hex(random_bytes(20));
                $token = array('token' => $string);
                $this->Student_model->_set_token($data->username, $token);
                print_r(json_encode($token));
            }
        }
    }

    public function logout()
    {
        if ($this->input->method() == 'get') {
            $token = $this->input->request_headers()['token'];
            print_r($token);
            $this->Student_model->_unset_token($token);
        }
    }

    public function validate()
    {
        print_r($this->input->request_headers());
    }
}
