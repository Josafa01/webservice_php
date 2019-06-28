<?php
	
namespace Controllers;

use \Core\Controller;
use \Models\User;
use \Models\Products;

class ProductsController extends Controller 
{

	public function new_prod() 
	{
		$data_array = array('error'=>'', 'logged'=>false);

		$method = $this->method();
		$data = $this->request_data();

		$user = new User();

		if (!empty($data['jwt']) && $user->validate_jwt($data['jwt']))
		{
			$data_array['logged'] = true;
			$id_user = $user->get_id();
			
			$p = new Products();

			if ($method == 'POST')
			{
				if (!empty($data['name']) && !empty($data['type']) && !empty($data['distributor']) &&
				!empty($data['price']) && !empty($data['number_prod']) && !empty($id_user))
				{
					$data_array['error'] = $p->insert_prod($data['name'], $data['type'], $data['distributor'], 
					$data['price'], $data['number_prod'], $id_user);
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
		} 
		else 
		{
			$data_array['error'] = 'Falha ao inserir!';
		}

		$this->return_json($data_array);
	}

	public function edit_prod($id) 
	{
		$data_array = array('error'=>'', 'logged'=>false);

		$method = $this->method();
		$data = $this->request_data();

		$user = new User();
		$p = new Products();

		if (!empty($data['jwt']) && $user->validate_jwt($data['jwt']))
		{
			
			$data_array['logged'] = true;
			$id_user = $user->get_id();

			switch ($method) {
				case 'GET':
					$data_array['data'] = $p->get_prod($id_user, $id);

					if(count($data_array['data']) === 0)
					{
						$data_array['error'] = 'Preencha os dados!';
					}

					break;

				case 'PUT':
					unset($data['jwt']);
					$data_array['error'] = $p->update_prod($id_user, $id, $data);
					break;

				case 'DELETE':
					$data_array['error'] = $p->delete_prod($id_user, $id);
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

	public function get_all_products($id_user) 
	{
		$data_array = array('error'=>'', 'logged'=>false);

		$method = $this->method();
		$data = $this->request_data();
		
		$user = new User();
		$p = new Products();
		$offset = 0;
		$per_page = 10;

		if (!empty($data['jwt']) && $user->validate_jwt($data['jwt']))
		{
			$data_array['logged'] = true;

			switch ($method) {
				case 'GET':
					$data_array['data'] = $p->get_all_prod($id_user, $offset, $per_page);

					if(count($data_array['data']) === 0)
					{
						$data_array['error'] = 'Preencha os dados!';
					}

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
}
