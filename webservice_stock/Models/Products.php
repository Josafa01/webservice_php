<?php

namespace Models;

use \Core\Model;

class Products extends Model
{

	public function insert_prod($name, $type, $distributor, $price, $number_prod, $id_user)
	{
		if(!$this->verify_add($name, $id_user) === true)
		{
			$sql = "INSERT INTO products (name, type, distributor, price, number_prod, id_user) 
			VALUES (:name, :type, :distributor, :price, :number_prod, :id_user)";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':name', $name);
			$sql->bindValue(':type', $type);
			$sql->bindValue(':distributor', $distributor);
			$sql->bindValue(':price', $price);
			$sql->bindValue(':number_prod', $number_prod);
			$sql->bindValue(':id_user', $id_user);
			$sql->execute();	
		}
		else 
		{
			return 'Erro dados duplicados!';
		}
		
	}

	public function get_all_prod($id_user, $offset, $per_page)
	{
		$array = array();

		$sql = "SELECT * FROM products WHERE id_user = :id 
		ORDER BY id DESC LIMIT ".$offset.", ".$per_page;
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id_user);
		$sql->execute();

		if ($sql->rowCount() > 0)
		{
			$array = $sql->fetchAll(\PDO::FETCH_ASSOC);
		}

		return $array;
	}

	public function get_prod($id_user, $id)
	{
		$array = array();

		$sql = "SELECT * FROM products WHERE id = :id AND id_user = :id_user";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id);
		$sql->bindValue(':id_user', $id_user);
		$sql->execute();

		if ($sql->rowCount() > 0)
		{
			$array = $sql->fetch(\PDO::FETCH_ASSOC);
		}

		return $array;
	}

	public function update_prod($id_user, $id, $data) 
	{
		if ($this->verify_prod($id_user, $id) === true)
		{

			foreach ($data as $k => $v) 
			{
				$new_data = array($k => $v);
			}
			
			if (!empty($k) && !empty($v))
			{
				$input_data = array();
				foreach ($new_data as $k => $v) 
				{
					$input_data[] = $k.' = :'.$k;
				}

				$sql = "UPDATE products SET ".implode(',', $input_data)." WHERE id = :id AND id_user = :id_user";
				$sql = $this->db->prepare($sql);
				$sql->bindValue(':id', $id);
				$sql->bindValue(':id_user', $id_user);

				foreach ($new_data as $k => $v) 
				{
					$sql->bindValue(':'.$k, $v);
				}

				$sql->execute();
				return 'Dados atualizados com sucesso';
			}
			else 
			{
				return 'Informações inválidas';
			}

		}
		else
		{
			return 'Informações inconsistentes';
		}

	}

	public function delete_prod($id_user, $id)
	{
		if ($this->verify_prod($id_user, $id) === true)
		{
			$sql = "DELETE FROM products WHERE id = :id AND id_user = :id_user";
			$sql = $this->db->prepare($sql);
			$sql->bindValue(':id', $id);
			$sql->bindValue(':id_user', $id_user);
			$sql->execute();
		}
		else
		{
			return 'Informações inválidas';
		}
	}

	private function verify_prod($id_user, $id)
	{
		$sql = "SELECT id, id_user FROM products WHERE id = :id AND id_user = :id_user";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':id', $id);
		$sql->bindValue(':id_user', $id_user);
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

	private function verify_add($name, $id_user) 
	{
		$sql = "SELECT * FROM products WHERE id_user = :id_user AND name = :name";
		$sql = $this->db->prepare($sql);
		$sql->bindValue(':name', $name);
		$sql->bindValue(':id_user', $id_user);
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

}