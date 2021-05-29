    <?php
        include_once('bdd.php');
        if(isset($_POST['action']) && $_POST['action'] == "Deconnexion"){
            session_destroy();
            header('location: menu.php');
            exit;
        }
    ?>
    <head>
    <meta charset="utf-8" />
    <title>Noël</title>
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Mountains+of+Christmas" />
    <link rel="stylesheet" href="fontawesome-free-5.15.1-web/css/all.css">

</head>
    <header>
        <h1>Noël 2020</h1>
    </header>
    <!--Barre de nav-->

    <?php
        if(!isset ($_SESSION['pseudo'])){
        //Si on est pas connecté
    ?>
    
    <div id='navbar'>
        <nav>
            <ul id='menu'>
                <li><a href='menu.php'>Accueil</a></li>
                <li><a onclick='connecte_toi()'>Poster</a></li>
                <li id="btn_connec"><a href='connexion.php'>Connexion</a></li>
            </ul>
        </nav>
    </div>

    <?php
        }
        elseif(isset ($_SESSION['grade']) && $_SESSION['grade']==2){
        //Si on est administrateur
    ?>

    <div id='navbar'>
        <nav>
            <ul id='menu'>
                <li><a href='menu.php'>Accueil</a></li>
                <li><a href='newpost.php'>Poster</a></li>
                <li id='btn_admin'><a href='moderation.php'>Modération</a></li>

                <li id='btn_admin'><a href='administration.php'>Administration</a></li>
                <div class="dropdown">
                <li id="btn_option"><form><button id='dropbtn'><?php echo $_SESSION['pseudo'] ?>  <i class="fa fa-caret-down" aria-hidden="true"></i></button></form> </li>
                <div class="dropdown-content">
                    <ul id='menu'>
                        <li id='btn_compte'><a href='compte.php'>Mon Compte</a></li>
                        <li id="btn_deconnec"><form method="POST"><button type="submit" name="action" value="Deconnexion">Deconnexion</button></form> </li>
                    </ul>
                </div>
                </div>
               
                
            </ul>
        </nav>
    </div>

    <?php
        }
        else{
        //Si on est un simple utilisateur et que l'on est connecté
    ?> 
    
    <div id='navbar'>
        <nav>
            <ul id='menu' method='POST'>
                <li><a href='menu.php'>Accueil</a></li>
                <li><a href='newpost.php'>Poster</a></li>
                <div class="dropdown">
                <li id="btn_option"><form><button id='dropbtn'><?php echo $_SESSION['pseudo'] ?>  <i class="fa fa-caret-down" aria-hidden="true"></i></button></form> </li>
                <div class="dropdown-content">
                    <ul id='menu'>
                        <li id='btn_compte'><a href='compte.php'>Mon Compte</a></li>
                        <li id="btn_deconnec"><form method="POST"><button type="submit" name="action" value="Deconnexion">Deconnexion</button></form> </li>
                    </ul>
                </div>
            </ul>
        </nav>
    </div>

    

    <?php
        }
    ?>
    
    

    <script>

        // FONCTION POUR GARDER LA BARRE DE NAVIGATION 
        window.onscroll = function() {myFunction()};

        var navbar = document.getElementById("navbar");
        var sticky = navbar.offsetTop;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky")
            } else {
                navbar.classList.remove("sticky");
            }
        }


        // FONCTION QUI VERIFIE LES MDP LORS DE L'INSCRIPTION
        function check_pass(p1,p2,action){
            var message;
            var password1 = document.getElementById(p1).value;
            var password2 = document.getElementById(p2).value;
            if(password1 != password2){
                message = "Les mots de passe ne correspondent pas !";
                document.getElementById(action).disabled = true;
            }
            else{
                message= '';
                document.getElementById(action).disabled = false;
            }
            document.getElementById('message').innerHTML = message;
            
        } 


        // FONCTION QUI AVERTIE LES UTILISATEURS DE SE CONNECTER
        function connecte_toi(){
            alert('Veuillez-vous connecter pour pouvoir accéder à cette page !')
        }


        // FONCTION QUI VERIFIE L'ANCIEN MOT DE PASSE
        function mdp_error(){
            document.getElementById('alerte_mdp').innerHTML = "Mauvais mot de passe !";
        }


         // AFFICHE LA DIV POUR ECRIRE UN NOUVEAU COMMENTAIRE
         function afficher(){
            var btn_new = document.getElementById('bouton_comment');
            var new_comment = document.getElementById('new_comment');
            if(getComputedStyle(new_comment).display != "none"){
                new_comment.style.display = "none";
            } else {
                new_comment.style.display = "block";
            }
         }
         
         function exist(){
             alert('Pseudo déjà pris !');
         }
        

	
        function choix_image(){
            var fichier = document.getElementById('fichier');
            document.getElementById('fichier').innerHTML = document.getElementById('btn_up').value;
            if(document.getElementById('btn_up').value == 'undefined'){
                fichier.style.display = "none";
            } else {
                fichier.style.display = "block";
            }
        }


        function sup_image(){
            var check = document.getElementById('check');
            
            if(document.getElementById('check').innerHTML == ''){
                document.getElementById('check').innerHTML = "Vous allez supprimer l'image";
                check.style.display = "block";
            } else {
                document.getElementById('check').innerHTML = '';
                check.style.display = "none";
            }
        }        
         

	// PREVIENS L'UTILISATEUR QUE LE PSEUDO OU LE MOT DE PASSE RENTREE N'EST PAS BON
        function wrong(){
            alert('Mauvais pseudo ou mot de passe !');
        }
       

    </script>



