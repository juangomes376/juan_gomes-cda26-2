<?php

    $stockInitial = [
        // Billets (en cents)
        50000 => 0, // 500€
        20000 => 1, // 200€
        10000 => 1, // 100€
        5000 => 10, // 50€
        2000 => 5,  // 20€
        1000 => 10, // 10€
        500 => 5,  // 5€
        // Pièces (en cents)
        200 => 45, // 2€
        100 => 30, // 1€
        50 => 30,  // 0.50€
        20 => 45,  // 0.20€
        10 => 45,  // 0.10€
        5 => 45,   // 0.05€
        2 => 45,   // 0.02€
        1 => 45,   // 0.01€
    ];

    // $stockInitial = [];

    function caisse($montantTotal, $montantPaye, $stockInitial) {

        global $stockInitial;

        // 1. Convertir les montants en cents
        $totalCents = (int) round($montantTotal * 100);
        $payeCents = (int) round($montantPaye * 100);
        $resteDuCents = $payeCents - $totalCents;

        if ($resteDuCents < 0) {
            throw new Exception("Le montant payé est insuffisant. Il manque " . abs($resteDuCents / 100) . " €.");
        }

        if ($resteDuCents === 0) {
             // Dans une vraie application, il faudrait ajouter le $montantPayeCents au stock
            return [
                'reste_du' => 0.00,
                'monnaie_rendue' => [],
                'message' => 'Rien à rendre.'
            ];
        }

        $monnaieRendue = calculerMonnaieRendue($resteDuCents, $stockInitial);


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


    function calculerMonnaieRendue($montantCents, $stockInitial){
        $monnaieARendre = [];
        $montantRestant = $montantCents;

        // global $stockInitial;

        foreach ($stockInitial as $valeurCents => $quantiteEnStock) {
            while ($montantRestant >= $valeurCents && $quantiteEnStock > 0) {
                $monnaieARendre[$valeurCents] = ($monnaieARendre[$valeurCents] ?? 0) + 1;
                $montantRestant -= $valeurCents;
                $quantiteEnStock--;
            }
        }

        if ($montantRestant > 0) {
            throw new Exception("Impossible de rendre la monnaie. La caisse n'a pas les coupures nécessaires pour rendre le reste de " . $montantRestant / 100 . " €.");
        }

        return $monnaieARendre;
    }

    function formaterMonnaieRendue(array $monnaieRendue): array {
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
        // global $stockInitial;
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
    $resultat = caisse($montantAchat, $montantDonne, $stockInitial);

    echo "--- Résultat de la Transaction ---\n";
    echo "Reste dû à rendre: " . number_format($resultat['reste_du'], 2, ',', '.') . " €\n";

    echo "\nDétail de la Monnaie à Rendre:\n";
    foreach ($resultat['monnaie_rendue'] as $item) {
        $type = $item['type'] . ($item['quantite'] > 1 ? 's' : '');
        echo "- " . $item['quantite'] . " " . $type . " de " . $item['valeur'] . "\n";
    }
    
    // Afficher le nouveau stock (facultatif)
    echo afficherStock($stockInitial);
    
} catch (Exception $e) {
    echo "❌ Erreur de Transaction: " . $e->getMessage() . "\n";
}

?>