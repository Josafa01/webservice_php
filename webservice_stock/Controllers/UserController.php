<?php
	
namespace Controllers;

use \Core\Controller;
use \Models\User;
use \Models\Products;

class UserController extends Controller 
{


	public function login()
	{
		$data_array = array('error'=>'');

		$method = $this->method();
		$data = $this->request_data();

		if ($method == 'POST') 
		{
			if (!empty($data['email']) && !empty($data['pass']))
			{
				
				$user = new User();

				if ($user->validate_login($data['email'], $data['pass']))
				{	
					$data_array['jwt'] = $user->create_jwt();
				}
				else 
				{
					$data_array['error'] = 'Acesso negado!';
				}

			}
			else
			{
				$data_array['error'] = 'E-mail e/ou senha não preenchidos!!';
			}
		}
		else
		{
			$data_array['error'] = 'Método de requisição incompatível';
		}

		$this->return_json($data_array);
	}

	public function new_user() 
	{
		$data_array = array('error' => '');

		$method = $this->method();
		$data = $this->request_data();
	
		if ($method == 'POST')
		{
			if (!empty($data['name']) && !empty($data['email']) && !empty($data['pass']))
			{
				if(filter_var($data['email'], FILTER_VALIDATE_EMAIL))
				{
					$user = new User();

					if($user->new_user($data['name'], $data['email'], $data['pass']))
					{
						$data_array['jwt'] = $user->create_jwt();
					}
					else 
					{
						$data_array['error'] = 'E-mail já existente';
					}
				}
				else
				{
					$data_array['error'] = 'E-mail inválido';
				}
			}
			else 
			{
				$data_array['error'] = 'Dados não preenchidos!';
			}
		} 
		else 
		{
			$data_array['error'] = 'Método de requisição incompatível!';
		}

		$this->return_json($data_array);
	}


	public function edit_user($id)
	{
		$data_array = array('error'=>'', 'logged'=>false);

		$method = $this->method();
		$data = $this->request_data();

		$user = new User();

		if (!empty($data['jwt']) && $user->validate_jwt($data['jwt']))
		{
			$data_array['logged'] = true;

			$data_array['is_me'] = false;
			if ($id == $user->get_id())
			{
				$data_array['is_me'] = true;
			}

			switch ($method) {
				case 'GET':
					$data_array['data'] = $user->get_info($id);

					if (count($data_array['data']) === 0)
					{
						$data_array['error'] = 'Usuário não existe';
					}	

					break;
				
				case 'PUT':
					$data_array['error'] = $user->edit_info($id, $data);
					break;

				case 'DELETE':
					$data_array['error'] = $user->delete($id);
					break;

				default:
					$data_array['error'] = 'Método '.$method.' não disponível!!';
					break;
			}

		}
		else 
		{
			$data_array['error'] = 'Acesso Negado!!';
		}

		$this->return_json($data_array);
	}

	public function get_prod($id_user)
	{
		$data_array = array('error'=>'', 'logged'=>false);

		$method = $this->method();
		$data = $this->request_data();

		$user = new User();

		if (!empty($data['jwt']) && $user->validate_jwt($data['jwt']))
		{
			$data_array['logged'] = true;
			$offset = 0;
			$per_page = 10;

			$data_array['is_me'] = false;
			if ($id_user == $user->get_id())
			{
				$data_array['is_me'] = true;
			}

			if ($method == 'GET')
			{
				if (!empty($data['offset']))
				{
					$offset = intval($data['offset']);
				}

				if (!empty($data['per_page']))
				{
					$per_page = intval($data['per_page']);
				}

				$data_array['data'] = $user->get_prod($offset, $per_page);
			}
			else
			{
				$data_array['error'] = 'Método '.$method.' não disponível!!';
			}

		}
		else 
		{
			$data_array['error'] = 'Acesso Negado!!';
		}

		$this->return_json($data_array);
	}


}
