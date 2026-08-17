<?php
/* =========================================================
   SESION / COOKIES COMPATIBLES CON PHP 8.4 + SAFARI
   ========================================================= */
if (session_status() === PHP_SESSION_NONE) {
	session_set_cookie_params([
		'lifetime' => 0,
		'path'     => '/',
		'domain'   => '',
		'secure'   => true,
		'httponly' => true,
		'samesite' => 'None'
	]);
	session_start();
}

/* =========================================================
   RESPUESTA JSON LIMPIA
   ========================================================= */
if (!headers_sent()) {
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Pragma: no-cache');
}

function json_response($ok, $message = '', $extra = []) {
	echo json_encode(array_merge([
		'ok'      => (bool)$ok,
		'message' => (string)$message
	], $extra), JSON_UNESCAPED_UNICODE);
	exit;
}

////////////////////////////////////////// LOGIN
if (isset($_GET["opt"]) && $_GET["opt"] == "login") :

	if (!isset($_SESSION["user_id"]) && !isset($_SESSION["client_id"])) :

		$username_raw = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
		$password_raw = isset($_POST['password']) ? trim((string)$_POST['password']) : '';

		if ($username_raw === '' || $password_raw === '') {
			json_response(false, 'Usuario o contraseña vacíos');
		}

		/* ==========================================
		   TELEFONO: limpiar y variantes
		   ========================================== */
		function limpiarTelefono($v) {
			return preg_replace('/\D/', '', (string)$v);
		}

		function variantesTelefono($telefono) {
			$n = limpiarTelefono($telefono);
			if ($n === '') return [];

			$out = [];
			$out[] = $n;

			if (strlen($n) >= 10) {
				$last10 = substr($n, -10);
				$out[] = $last10;
				$out[] = "1" . $last10;
			}

			if (strlen($n) >= 11) {
				$last11 = substr($n, -11);
				$out[] = $last11;
				if (isset($last11[0]) && $last11[0] === '1') {
					$out[] = (string) substr($last11, 1);
				}
			}

			if (strlen($n) == 10) {
				$out[] = "1" . $n;
			}

			if (strlen($n) == 11 && isset($n[0]) && $n[0] === '1') {
				$out[] = (string) substr($n, 1);
			}

			return array_values(array_unique(array_filter($out)));
		}

		$username_clean = strtolower($username_raw);
		$is_email = (filter_var($username_clean, FILTER_VALIDATE_EMAIL) !== false);

		/* ==========================================
		   CONEXION
		   ========================================== */
		$base = new Database();
		$con = $base->connect();

		if (!$con) {
			json_response(false, 'Error de conexión');
		}

		mysqli_set_charset($con, "utf8");

		/* ==========================================
		   VALIDAR ESTADO DEL SISTEMA
		   ========================================== */
		$status = 'panic';
		$check = $con->query("SELECT status FROM system_status LIMIT 1");

		if ($check && $row = $check->fetch_assoc()) {
			$status = isset($row['status']) ? trim((string)$row['status']) : 'panic';
		}

		if ($status !== 'normal') {
			$_SESSION = [];

			if (ini_get("session.use_cookies")) {
				setcookie(session_name(), '', [
					'expires'  => time() - 42000,
					'path'     => '/',
					'domain'   => '',
					'secure'   => true,
					'httponly' => true,
					'samesite' => 'None'
				]);
			}

			session_destroy();
			json_response(false, 'Sistema no disponible');
		}

		/* ==========================================
		   HASH PASSWORD USER
		   ========================================== */
		$password_hash = sha1(md5($password_raw));

		/* ==========================================
		   LOGIN STAFF / ADMIN
		   user.email = username
		   user.password = password encriptado
		   ========================================== */
		if ($is_email) {

			$stmt = $con->prepare("
				SELECT id, stock_id 
				FROM user 
				WHERE LOWER(TRIM(email)) = ? 
				AND password = ? 
				AND status = 1 
				LIMIT 1
			");

			if ($stmt) {
				$stmt->bind_param("ss", $username_clean, $password_hash);
				$stmt->execute();
				$res = $stmt->get_result();

				if ($res && $u = $res->fetch_assoc()) {

					session_regenerate_id(true);

					$_SESSION['user_id']  = (int)$u['id'];
					$_SESSION['stock_id'] = (int)$u['stock_id'];
					$_SESSION['login_type'] = 'user';

					unset($_SESSION['client_id']);

					setcookie("seen_login", "1", [
						'expires'  => time() + (86400 * 365),
						'path'     => '/',
						'domain'   => '',
						'secure'   => true,
						'httponly' => true,
						'samesite' => 'None'
					]);

					$stmt->close();

					json_response(true, 'Acceso permitido', [
						'redirect' => './?view=home'
					]);
				}

				$stmt->close();
			}
		}

		/* ==========================================
		   LOGIN CLIENTE / PERSON
		   person.phone = username
		   person.phone = password SIN ENCRIPTAR
		   client_id = person.id
		   stock_id = person.stock_id
		   ========================================== */

		$telefonos_user = variantesTelefono($username_raw);
		$telefonos_pass = variantesTelefono($password_raw);

		if (!empty($telefonos_user) && !empty($telefonos_pass)) {

			foreach ($telefonos_user as $tel_user) {

				foreach ($telefonos_pass as $tel_pass) {

					$stmt = $con->prepare("
						SELECT id, stock_id
						FROM person
						WHERE 
						REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', ''), '+', '') = ?
						AND 
						REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', ''), '+', '') = ?
						LIMIT 1
					");

					if ($stmt) {
						$stmt->bind_param("ss", $tel_user, $tel_pass);
						$stmt->execute();
						$res = $stmt->get_result();

						if ($res && $p = $res->fetch_assoc()) {

							session_regenerate_id(true);

							$_SESSION['client_id'] = (int)$p['id'];
							$_SESSION['stock_id']  = (int)$p['stock_id'];
							$_SESSION['login_type'] = 'client';

							unset($_SESSION['user_id']);

							setcookie("seen_login", "1", [
								'expires'  => time() + (86400 * 365),
								'path'     => '/',
								'domain'   => '',
								'secure'   => true,
								'httponly' => true,
								'samesite' => 'None'
							]);

							$stmt->close();

							json_response(true, 'Acceso permitido', [
								'redirect' => './?view=home'
							]);
						}

						$stmt->close();
					}
				}
			}
		}

		json_response(false, 'Por favor verifique su nombre de usuario y contraseña');

	else :

		json_response(true, 'Sesión ya iniciada', [
			'redirect' => './?view=home'
		]);

	endif;


////////////////////////////////////////// LOGIN 

elseif(isset($_GET["opt"]) && $_GET["opt"]=="box"):

$sells = null;
$sells = BookingData::getSellsUnBoxed(StockData::getPrincipal()->id);


	$box = new BoxData();
	$box->user_id = Core::$user->id; 
	$box->stock_id = StockData::getPrincipal()->id;
	$b = $box->add();

if(count($sells)){	
	foreach($sells as $sell){
		$sell->box_id = $b[1];
		$sell->update_box();
	}
}

$spends = SpendData::getAllUnBoxed($_SESSION["user_id"]);
if(count($spends)){
	foreach($spends as $sell){
		$sell->box_id = $b[1];
		$sell->update_box();
	}
}
$income = MaintenanceData::getAllUnBoxed();
if(count($income)){
	foreach($income as $sell){
		$sell->box_id = $b[1];
		$sell->update_box();
	}
}


	$user = new ACData();
          $user->user_id = $_SESSION["user_id"];
          $user->accion = "Hizo el corte caja " .$b[1];"";
          $user->add();

          header('location:./?view=b&id='.$b[1]);

////////////////////////////////////////////////////////////////////// INPUT //////////////////////////////////////
endif;
?>