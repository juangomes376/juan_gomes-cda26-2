<?php

    $stockBillets   = [
        // Billets (en cents)
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
        // 1. Convertir les montants en cents
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
            'reste_du' => $resteDuCents / 100,
            'monnaie_rendue' => formaterMonnaieRendue($monnaieRendue),
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
                echo "Ajout de " . number_format($valeurCents / 100, 2, ',', '.') . " €, reste à rendre: " . number_format($montantRestant / 100, 2, ',', '.') . " €\n";
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

    function afficherStock($stockInitial) {
        echo "\n### 💰 État Actuel du Stock de la Caisse\n";
        foreach ($stockInitial as $valeurCents => $quantite) {
            echo number_format($valeurCents / 100, 2, ',', '.') . " € : " . $quantite . " unités\n";
        }
    }

echo "## 🚀 Simulation de Caisse Enregistreuse\n\n";

$montantAchat = 33.40;
$montantDonne = 50.00;

echo "Achat: " . number_format($montantAchat, 2, ',', '.') . " €\n";
echo "Payé: " . number_format($montantDonne, 2, ',', '.') . " €\n\n";

try {

    $stock = $stockBillets;

    $resultat = caisse($montantAchat, $montantDonne, $stock, "SmallFirst"); // option "Standard" , "SmallFirst", ou valor em cents para preferido (ex: 200 para 2€)

    echo "--- Résultat de la Transaction ---\n";
    echo "Reste dû à rendre: " . number_format($resultat['reste_du'], 2, ',', '.') . " €\n";

    echo "\nDétail de la Monnaie à Rendre:\n";
    foreach ($resultat['monnaie_rendue'] as $item) {
        $type = $item['type'] . ($item['quantite'] > 1 ? 's' : '');
        echo "- " . $item['quantite'] . " " . $type . " de " . $item['valeur'] . "\n";
    }

    echo afficherStock($stock);
    
} catch (Exception $e) {
    echo "❌ Erreur de Transaction: " . $e->getMessage() . "\n";
}

?>