<?php

//getting args
if ($argc == 2) {
    // on vérifie ici que le fichier est lisible avant de continuer
    if (@file_get_contents($argv[1]) != false) {
        $content = @file_get_contents($argv[1]);
    } else {
        echo "Le fichier spécifié en argument est illisible\n";
        return;
    }

    // on initialise ici le nombre de lignes et de colonnes de la grille si le fichier est lisible
    $colonnes = strlen(explode("\n", $content)[1]);
    $lignes = explode("\n", $content)[0];

    // on retire ici le nombre indiquant le nombre de ligne pour garder uniquement la grille
    $content = str_replace($lignes . "\n", "", $content);

    // on créer ici un tableau à deux dimensions de la grille pour ensuite commencer la recherche du plus grand carré
    $grid = @set_grid($content, $colonnes);

    // on appelle ici la principale fonction qui s'occupe de trouver le carré
    $grid = @solve_bsq($grid, $lignes, $colonnes);
} else {
    echo "Erreur - trop ou pas assez d'arguments passés en paramètre du programme!\n";
}

function set_grid($content, $l)
{
    // on transforme ici le texte en tableau à l'aide de la longueur $l qui correpond à la longueur d'une ligne
    $grid = str_split(str_replace("\n", "", $content), $l);

    foreach ($grid as $key => &$value) {
        // on re-split ensuite chaque ligne en array ce qui nous donne un tableau à deux dimensions à la fin
        $value = str_split($value);
    }

    // on retourne la grille
    return $grid;
}

function solve_bsq($g, $lignes, $colonnes)
{
    // on clone de la grille et on remplace les caractères par des 1 et des 0
    $copy = cloneGrid($g,$lignes,$colonnes);
    // on recherche dans cette fonction le plus grand carré - on récupére les infos du coin inférieur droit pour l'affichage
    $data = getMaxSquare($copy,$lignes,$colonnes);

    if ($data["taille"]==0 || $data["ligne"]==null || $data["colonne"]==null) {
        echo "La grille ne contient pas de carré\n";
        return;
    }

    // on affiche ensuite le résultat dans la grille initiale $g
    displayResult($g,$data,$lignes,$colonnes);
}

function displayResult($g,$data,$lignes,$colonnes){

    // ici on remplace les coordonnées du carré en utilisant sa taille et les coordonnées du coin inférieur droit
    for ($i=0; $i < $data["taille"]; $i++) { 
        for ($j=0; $j < $data["taille"]; $j++) { 
            $g[$data["ligne"]-$i][$data["colonne"]-$j]="x";
        }
    }

    // cette partie sert à afficher la grille à l'aide de deux boucles et d'une condition ...
    for ($y = 0; $y < $lignes; $y++) {
        for ($x = 0; $x < $colonnes; $x++) {
            if ($x < $colonnes-1) {
                echo $g[$y][$x];
            }else{
                echo $g[$y][$x]."\n";
            }
        }
    }

}

function getMaxSquare($copy,$lignes,$colonnes){

    // on initialise la valeur max qui correspond au coin inférieur droit du plus grand carré et à sa taille
    $maxResult = 0;
    // coordonnée x de la case du coin inférieur droit du plus grand carré
    $x=null;
    // coordonnée y de la case du coin inférieur droit du plus grand carré
    $y=null;

    for ($j = 0; $j < $lignes; $j++) {
        for ($i = 0; $i < $colonnes; $i++) {

            if ($j == 0 || $i == 0) {
                // ici rien ne se passe, la condition sert uniquement à isoler le cas    
            }elseif ($copy[$j][$i] > 0) {
                // ici on calcule la valeur digitale de la case
                $copy[$j][$i] = 1 + min($copy[$j-1][$i],$copy[$j-1][$i-1],$copy[$j][$i-1]);
            }

            // ici on met en cache les données de la dernière case du carré 
            if ($maxResult<$copy[$j][$i]) {
                $maxResult = $copy[$j][$i];
                $x=$i;
                $y=$j;
            }
        }
    }


    // on renvoie les données
    return ["taille"=>$maxResult,"colonne"=>$x,"ligne"=>$y];
}

function cloneGrid($g,$lignes,$colonnes){

    // on créer et renvoie la copie digitale de la grille 
    $copy = array();
    for ($y = 0; $y < $lignes; $y++) {
        for ($x = 0; $x < $colonnes; $x++) {
            if ($g[$y][$x] == ".") {
                $copy[$y][$x] = 1;
            }else{
                $copy[$y][$x] = 0;
            }
        }
    }

    return $copy;
}