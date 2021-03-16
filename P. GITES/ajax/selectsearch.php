<?php
require_once "C:\wamp64\www\Exercices\P. GITES/connect.php";
require_once "C:\wamp64\www\Exercices\P. GITES\classes/class.booking.php";
require_once "C:\wamp64\www\Exercices\P. GITES\classes/class.lodge.php";


//🍺🍺🍺 JSON
$test = json_decode(file_get_contents("php://input"), true);
$j = 1;
$q = '';

foreach ((array)$test as $data => $key) {
    if (!empty($data)) {

        $k = '';
        $nb = count($key);
        $i = 1;

        foreach ($key as $value) {
            if (is_array($value) > 1) {
                $k .= "'" . implode(',', $value) . "'";
            } else if ($i < $nb) {
                $k .= "'" . $value . "',";
            } else {
                $k .= "'" . $value . "'";
            }
            $i++;
        }

        if (strlen($k)) {
            switch ($data) {
                case "boxCategory":
                    if ($j > 1) {
                        $q .= "AND `category` IN ($k) ";
                    } else {
                        $q .= "`category` IN ($k) ";
                    }
                    break;
                case "boxSpecificity":
                    $q .= "`specificity` IN ($k) ";
                    break;
                case "boxBedroom":
                    if ($j > 1) {
                        $q .= "AND `bedroom` IN ($k) ";
                    } else {
                        $q .= "`bedroom` IN ($k) ";
                    }
                    break;
                case "boxBathroom":
                    if ($j > 1) {
                        $q .= "AND `bathroom` IN ($k) ";
                    } else {
                        $q .= "`bathroom` IN ($k) ";
                    }
                    break;
                default:
                    $q .= $k;
                    break;
            }
        }
    }
    $j++;
}

$lodgeWhere = [];
try {
    $req = $db->prepare("SELECT * FROM `lodge` WHERE $q");
    $req->execute();
    $content = '';
    while ($donnees = $req->fetch(PDO::FETCH_ASSOC)) {
        $lodgeWhere[] = new Lodge($donnees);
        foreach ($lodgeWhere as $data) {
            $array = unserialize($data->getImage());
        }
        var_dump($array);
        echo $array[3];
        $content .= '<div class="lodge">';
        $content .= '<a id="lodge_wrap" href="presentation.php?id= ' . $donnees['idlodge'] . ' ">';
        $content .= '<img src="' . $array[0] . '"';
        $content .= '<h1>Nom du gîte:' . $donnees['lodgename'] . '</h1>';
        $content .= '<p>Categorie:' . $donnees['category'] . '<p>';
        $content .= '<p>Nombre de salle de bain:' . $donnees['bathroom'] . '<p>';
        $content .= '<p>Nombre de couchage:' . $donnees['bedroom'] . '<p>';
        $content .= '<p>Specificitées:' . $donnees['specificity'] . '<p>';
        $content .= '</div>';
    }
    echo $content;
} catch (Exception $e) {
    echo 'Echec de requete' . $e->getMessage();
}
