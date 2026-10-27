<?php

/** Return curated product-card angle images for the New 2026 collection. */
function lw_get_curated_watch_angles(WC_Product $product): array {
    $assets = [
        'LW-NEW-CER-20' => ['ceramique-blanc-38-angle-1.jpg', 'ceramique-blanc-38-angle-2.jpg'],
        'LW-NEW-MONOTI-19' => ['mono-titanium-38-angle-1.jpg', 'mono-titanium-38-angle-2.jpg'],
        'LW-NEW-SIE-18' => ['sienna-copper-petite-angle-1.jpg', 'sienna-copper-petite-angle-2.jpg'],
        'LW-NEW-ETO-17' => ['etoile-diamond-halo-angle-1.jpg', 'etoile-diamond-halo-angle-2.jpg'],
        'LW-NEW-TUR-16' => ['riviera-turquoise-cushion-angle-1.jpg', 'riviera-turquoise-cushion-angle-2.jpg'],
        'LW-NEW-OPA-15' => ['opaline-mini-moonphase-angle-1.jpg', 'opaline-mini-moonphase-angle-2.jpg'],
        'LW-NEW-IRI-14' => ['iris-lavender-automatic-angle-1.jpg', 'iris-lavender-automatic-angle-2.jpg'],
        'LW-NEW-ROS-13' => ['rosee-two-tone-angle-1.jpg', 'rosee-two-tone-angle-2.jpg'],
    ];

    $files = $assets[$product->get_sku()] ?? [];
    $base_url = trailingslashit(get_stylesheet_directory_uri()) . 'assets/watch-angles/';

    return array_map(static function (string $file) use ($base_url): string {
        return $base_url . rawurlencode($file);
    }, $files);
}
