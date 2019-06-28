<?php 

namespace Models;

use \Core\Model;
use \Models\Jwt;
use \Models\Products;

class User extends Model 
{

	private $id_user;

	public function new_user($name, $email, $pass)
	{
		if (!$this->email_verify($email))
		{
			$hash = password_hash($pass, PASSWORD_DEFAULT);

			$sql = "INSERT INTO user (name, email, pass) VALUES (:name, :email, :pass)";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':name', $name);
			$sql->bindValue(':email', $email);
			$sql->bindValue(':pass', $hash);
			$sql->execute();

			$this->id_user = $this->db->lastInsertId();

			return true;
		}
		else
		{
			return false;
		}
	}

	public function validate_login($email, $pass)
	{
		
		$sql = "SELECT id, pass FROM user WHERE email = :email";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':email', $email);
		$sql->execute();

		if ($sql->rowCount() > 0)
		{		
			$info = $sql->fetch();

			if (password_verify($pass, $info['pass']))
			{
				$this->id_user = $info['id'];
				return true;
			}
			else 
			{
				return false;
			}
		}
		else 
		{
			return false;
		}
	}

	public function get_id()
	{
		return $this->id_user;
	}

	public function get_info($id)
	{
		$array = array();

		$sql = "SELECT id, name, email FROM user WHERE id = :id";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id);
		$sql->execute();

		if ($sql->rowCount() > 0)
		{
			$array = $sql->fetch(\PDO::FETCH_ASSOC);
		}

		return $array;
	}


	public function create_jwt() 
	{
		$jwt = new Jwt();
		return $jwt->create(array('id_user' => $this->id_user));
	}

	public function validate_jwt($token)
	{
		$jwt = new Jwt();
		$info = $jwt->validate($token);

		if (isset($info->id_user))
		{
			$this->id_user = $info->id_user;
			return true;
		}
		else 
		{
			return false;
		}
	}

	private function email_verify($email)
	{
		$sql = "SELECT id FROM user WHERE email = :email";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':email', $email);
		$sql->execute();

		if ($sql->rowCount() > 0)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	public function delete($id)
	{

		if ($id === $this->get_id())
		{
			$sql = "DELETE FROM user WHERE id = :id";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id', $id);
			$sql->execute();

			return '';
		}
		else
		{
			return 'Não é permitido editar outro usuário';
		}

	}

	public function edit_info($id, $data) 
	{

		if ($id === $this->get_id())
		{
			$new_data = array();

			if (!empty($data['name']))
			{
				$new_data['name'] = $data['name'];
			}

			if (!empty($data['email'])) 
			{
				if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) 
				{
					if (!$this->email_verify($data['email']))
					{
						$new_data['email'] = $data['email'];	
					}
					else
					{
						return 'E-mail já existente!';
					}
				}
				else
				{
					return 'Email inválido!';
				}
			}
			

			if (!empty($data['pass']))
			{
				$new_data['pass'] = password_hash(
					$data['pass'], PASSWORD_DEFAULT
				);
			}

			if (count($new_data) > 0)
			{
				$input_data = array();
				foreach ($new_data as $k => $v) 
				{
					$input_data[] = $k.' = :'.$k;
				}

				$sql = "UPDATE user SET ".implode(',', $input_data)." WHERE id = :id";
				$sql = $this->db->prepare($sql);
				$sql->bindValue(':id', $id);

				foreach ($new_data as $k => $v) 
				{
					$sql->bindValue(':'.$k, $v);
				}

				$sql->execute();
				return '';
			}
			else 
			{
				return 'Preencha os dados corretamente';
			}

		}
		else
		{
			return 'Não é permitido editar outro usuário';
		}

	}

	public function get_prod($offset, $per_page)
	{
		$p = new Products();
		$id_user = $this->get_id();
		return $p->get_all_prod($id_user, $offset, $per_page);
	}

}