<?php
require_once 'BD.php';

// Receber dados via POST
$montantAchat = ($_POST['prix_achat'])*1 ?? '';
$montantDonne = ($_POST['prix_vente'])*1 ?? '';
$mode = $_POST['mode'] ?? 'standard';


$stockBillets = [
    50000 => 0, // 500€
    20000 => 1, // 200€
    10000 => 1, // 100€
    5000 => 10, // 50€
    2000 => 5,  // 20€
    1000 => 10, // 10€
    500 => 5,  // 5€
    200 => 45, // 2€
    100 => 30, // 1€
    50 => 30,  // 0.50€
    20 => 45,  // 0.20€
    10 => 45,  // 0.10€
    5 => 45,   // 0.05€
    2 => 45,   // 0.02€
    1 => 45,   // 0.01€
];

function caisse($montantTotal, $montantPaye, $stockInitial, $option) {
    $totalCents = (int) round($montantTotal * 100);
    $payeCents = (int) round($montantPaye * 100);
    $resteDuCents = $payeCents - $totalCents;

    if ($resteDuCents < 0) {
        throw new Exception("Le montant payé est insuffisant. Il manque " . abs($resteDuCents / 100) . " €.");
    }

    if ($resteDuCents === 0) {
        return [
            'reste_du' => 0.00,
            'monnaie_rendue' => [],
            'message' => 'Rien à rendre.'
        ];
    }

    if($option == "Standard"){
        $monnaieRendue = calculerMonnaieRendue($resteDuCents, $stockInitial);
    }elseif($option == "SmallFirst"){
        if($resteDuCents >= 5000){ // 50€ in cents
            $monnaieRendue = calculerMonnaieRendue($resteDuCents, $stockInitial); // Use Standard
        }else{
            $smallStock = array_filter($stockInitial, function($key){ return $key <= 500; }, ARRAY_FILTER_USE_KEY);
            krsort($smallStock);
            $monnaieRendue = calculerMonnaieRendue($resteDuCents, $smallStock);
        }
    }elseif(is_numeric($option)){
        $preferred = $option;
        $monnaieRendue = calculerMonnaieRendue($resteDuCents, $stockInitial, $preferred);
    }

    foreach ($monnaieRendue as $valeurCents => $quantite) {
        if ($stockInitial[$valeurCents] < $quantite) {
            throw new Exception("Erreur interne : Stock insuffisant après le calcul.");
        }
        $stockInitial[$valeurCents] -= $quantite;
    }

    return [
        'change' => $resteDuCents / 100,
        'items' => formaterMonnaieRendue($monnaieRendue),
    ];
}

function calculerMonnaieRendue($montantCents, $stockInitial, $preferred = null){
    $monnaieARendre = [];
    $montantRestant = $montantCents;

    if($preferred !== null && isset($stockInitial[$preferred])){
        $sortedStock = [$preferred => $stockInitial[$preferred]];
        krsort($stockInitial);
        foreach($stockInitial as $val => $qty){
            if($val != $preferred){
                $sortedStock[$val] = $qty;
            }
        }
        $stockInitial = $sortedStock;
    }

    foreach ($stockInitial as $valeurCents => $quantiteEnStock) {
        while ($montantRestant >= $valeurCents && $quantiteEnStock > 0) {
            $monnaieARendre[$valeurCents] = ($monnaieARendre[$valeurCents] ?? 0) + 1;
            $montantRestant -= $valeurCents;
            // echo "Ajout de " . number_format($valeurCents / 100, 2, ',', '.') . " €, reste à rendre: " . number_format($montantRestant / 100, 2, ',', '.') . " €\n";
            $quantiteEnStock--;
        }
    }

    if ($montantRestant > 0) {
        throw new Exception("Impossible de rendre la monnaie. La caisse n'a pas les coupures nécessaires pour rendre le reste de " . $montantRestant / 100 . " €.");
    }

    return $monnaieARendre;
}

function formaterMonnaieRendue($monnaieRendue){
    $resultat = [];
    foreach ($monnaieRendue as $valeurCents => $quantite) {
        $valeurEuro = $valeurCents / 100;
        $type = ($valeurEuro >= 5) ? 'Billet' : 'Pièce';
        $resultat[] = [
            'type' => $type,
            'valeur' => number_format($valeurEuro, 2, ',', '.') . ' €',
            'quantite' => $quantite,
        ];
    }
    return $resultat;
}

try {

    if (!is_numeric($montantAchat) || !is_numeric($montantDonne)) {
        echo json_encode(['success' => false, 'message' => 'Valeurs invalides.']);
        exit;
    }

    $resultat = caisse($montantAchat, $montantDonne, $stockBillets, $mode);
    echo json_encode(['success' => true, 'change' => $resultat['change'], 'items' => $resultat['items']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
