<?php
	session_start();

	require('src/log.php');

	if(!empty($_POST['email']) && !empty($_POST['password'])) {

		require('src/connect.php');

		// variables
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);

		// adresse email syntaxe
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: index.php?error=1&message=Votre adresse email est invalide.');
		exit();

		}

		// chiffrage du mot de passe

		$password = "aq1".sha1($password."123")."25";
		
		//email deja utilisé
		$req = $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
		$req ->execute(array($email));

		while($email_verification = $req->fetch()) {
			if($email_verification['numberEmail'] != 1) {
				header('location: index.php?error=1&message=Impossible de vous authentifier correctement.');
				exit();
			}
		}

		// Connexion
		$req = $db->prepare("SELECT * FROM user WHERE email = ?");
		$req->execute(array($email));

		while ($user = $req->fetch()) {

			if($password == $user['password']) {

				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $user['email'];

				if(isset($_POST['auto'])) {
					setcookie('auth', $user['secret'], time() + 364*24*3600, '/', null, false, true);
				}

				header('location: index.php?success=1');
				exit();

			}
			 else {
				header('location: index.php?error=1&Impossible de vous authentifier correctement.');
			}
		}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Tarif-Transports</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">

				<?php if(isset($_SESSION['connect'])) { ?>
			
					<h1>Bienvenue !</h1>

					<?php
						if(isset($_GET['success'])) {
							echo'<div class= "alert success"> Vous êtes maintenant connecté.</div>';
						} ?>

					<p>Tester la feuille de calcul</p>
					<small><a href="logout.php">Déconnnexion</a></small>

				<?php } else { ?>
				
					<h1>S'identifier</h1>
					<p>Veuillez entrer vos informations</p>

					<?php if(isset($_GET['error'])) {

						if(isset($_GET['message'])) {
							echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}

					}?>	

					<form method="post" action="index.php">
						<input type="email" name="email" placeholder="Votre adresse email" required />
						<input type="password" name="password" placeholder="Mot de passe" required />
						<button type="submit">Se connecter</button>
						<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
						</br>
						<label id="job"><input type="radio" name="auto" checked />Commercial</label>
						<label id="job"><input type="radio" name="auto" checked />Controleur de gestion</label>
					</form>
				

					<p class="grey">Première visite sur Tarif-Transports<br/><a href="inscription.php">Inscrivez-vous ici</a></p>
				<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>