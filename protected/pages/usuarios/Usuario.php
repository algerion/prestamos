<?php
/*
 * Creado el 11/mar/2009
 */
// Incluye el archivo TDbUserManager.php que define TDbUser
Prado::using('System.Security.TDbUserManager');
// Incluye el archivo HMAC.php que define el uso del generador de HASH HMAC.
require_once('../compartidos/clases/HMAC.php');

/**
 * Clase Usuario.
 * Usuario representa los datos de usuario que deben mantenerse en la sesión.
 * La implementación original mantiene la información del usuario y del rol.
 */
class Usuario extends TDbUser
{
	var $hmac;
	var $tabla = "usuarios", $login = "usuario", $pass = "acceso", $id_usuario = "id_usuario";
	var  $id_permiso = "id_permiso", $permiso = "permiso", $roles = "permisos", $lista = "lista_permisos";

    /**
     * Crea un objeto Usuario basado en el nombre de usuario especificado
     * Este método es requerido por TDbUser. Revisa la base de datos
     * para ver si el nombre de usuario especificado existe. Si es así,
     * se crea e inicializa un objeto Usuario.
     * @param string el nombre de usuario especificado
     * @return Usuario el objeto usuario, null si el nombre de usuario no es válido
     */
    public function createUser($username)
    {
        $consulta = "SELECT " . $this->id_usuario . " FROM " . $this->tabla . " WHERE " . $this->login . " = :login";
        $command = $this->getDbConnection()->createCommand($consulta);
        $command->bindValue(":login", $username);
        $resultado = $command->query();

        if($row = $resultado->read())
        {
            $user = new Usuario($this->Manager);
            $user->Name = $row[$this->id_usuario];  // asigna el nombre de usuario
			/*$consulta = "SELECT " . $this->permiso . " FROM " . $this->roles . " r JOIN " . 
					$this->lista . " l ON r." . $this->id_permiso . " = l." . $this->id_permiso . 
					" WHERE " . $this->id_usuario . " = :id_usuario";
			$command = $this->getDbConnection()->createCommand($consulta);
			$command->bindValue(":id_usuario", $user->Name);
			$roles = array('');
			foreach($command->query()->readAll() as $rol)
				$roles[] = $rol[$this->permiso]; 
			$user->Roles = $roles; // asigna los roles
			*/
			//file_put_contents("temp/usr.txt", $rol, FILE_APPEND);
			
            $user->IsGuest = false;   // el usuario no es un invitado
            return $user;
        }
        else
            return null;
    }

    /**
     * Revisa si el par (usuario, password) especificado es válido.
     * Método requerido por TDbUser.
     * @param string username
     * @param string password
     * @return boolean retorna verdadero si username and password son válidos, falso en cualquier otro caso.
     */
    public function validateUser($username, $password)
    {
		$consulta = "SELECT " . $this->pass . " FROM " . $this->tabla . " WHERE " . $this->login . " = :login";
		$command = $this->getDbConnection()->createCommand($consulta);
		$command->bindValue(":login", $username);
		$resultado = $command->query();

		if($row = $resultado->read())
		{
			$phphmac = new Crypt_HMAC($_SESSION["aleat"]);
			if($phphmac->hash($row[$this->pass]) == $password)
				return true;
			else
				return false;
		}
		else
			return false;
    }

    /**
     * @return boolean retorna verdadero si el usuario es administrador.
     */
/*    public function getIsAdmin()
    {
        return $this->isInRole('1');
    }*/

	public function createUserFromCookie($cookie)
	{
		if(($data = $cookie->Value) !== '')
		{
			$application = Prado::getApplication();
			if(($data = $application->SecurityManager->validateData($data)) !== false)
			{
				$data = unserialize($data);
				if(is_array($data) && count($data) === 3)
				{
					list($username, $address, $token) = $data;
					$sql = "SELECT " . $this->login . ", " . $this->pass . " FROM " . $this->tabla . " WHERE " . $this->id_usuario . " = :id_usuario";
					$command = $this->DbConnection->createCommand($sql);
					$command->bindValue(":id_usuario", $username);
					$resultado = $command->query();
					if($row = $resultado->read())
						if($token === $row[$this->pass] && $token !== false && $address =
								$application->Request->UserHostAddress)
							return $this->createUser($row[$this->login]);
				}
			}
		}
		return null;
	}

	public function saveUserToCookie($cookie)
	{
		$application = Prado::getApplication();
		$username = strtolower($this->Name);
		$address = $application->Request->UserHostAddress;
		$sql = "SELECT " . $this->pass . " FROM " . $this->tabla . " WHERE " . $this->id_usuario . " = :id_usuario";
		$command = $this->DbConnection->createCommand($sql);
		$command->bindValue(":id_usuario", $username);
		$token = $command->queryScalar();
		$data = array($username, $address, $token);
		$data = serialize($data);
		$data = $application->SecurityManager->hashData($data);
		$cookie->setValue($data);
	}
}
?>