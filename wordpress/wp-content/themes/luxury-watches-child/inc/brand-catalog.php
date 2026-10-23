<?php
if (!defined('ABSPATH')) { exit; }

add_filter('body_class', function ($classes) {
    if (is_page('thuong-hieu-dong-ho')) { $classes[] = 'storefront-full-width-content'; }
    return $classes;
});

add_filter('wp_nav_menu_items', function ($items, $args) {
    if (!in_array(($args->theme_location ?? ''), ['primary', 'handheld'], true)) { return $items; }
    $page = get_page_by_path('thuong-hieu-dong-ho');
    if (!$page) { return $items; }
    $page_url = get_permalink($page);
    $brands = ['Seiko', 'Citizen', 'Carnival', 'Tsar Bomba'];
    $submenu = '';
    foreach ($brands as $brand) {
        $submenu .= '<li class="menu-item"><a href="' . esc_url(add_query_arg('hang', sanitize_title($brand), $page_url)) . '">' . esc_html($brand) . '</a></li>';
    }
    $watch_item = '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children lw-watch-menu"><a href="' . esc_url($page_url) . '">Đồng hồ</a><ul class="sub-menu">' . $submenu . '</ul></li>';
    $first_end = strpos($items, '</li>');
    if ($first_end === false) { return $items . $watch_item; }
    $first_end += strlen('</li>');
    return substr($items, 0, $first_end) . $watch_item . substr($items, $first_end);
}, 20, 2);

add_action('wp_enqueue_scripts', function () {
    if (!is_page('thuong-hieu-dong-ho')) { return; }
    $file = get_stylesheet_directory() . '/assets/brand-catalog.css';
    wp_enqueue_style('lw-brands', get_stylesheet_directory_uri() . '/assets/brand-catalog.css', ['luxury-watches'], filemtime($file));
});

add_shortcode('lw_brand_catalog', function () {
    $file = get_stylesheet_directory() . '/assets/brand-catalog/catalog.json';
    $rows = is_readable($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($rows)) { $rows = []; }
    $brands = ['Seiko' => 'Nhật Bản · Presage', 'Citizen' => 'Nhật Bản · Automatic', 'Carnival' => 'Carnival 1986', 'Tsar Bomba' => 'Thiết kế hiện đại'];
    $requested_brand = isset($_GET['hang']) && is_string($_GET['hang']) ? sanitize_key(wp_unslash($_GET['hang'])) : '';
    $selected_brand = '';
    foreach ($brands as $name => $subtitle) {
        if (sanitize_title($name) === $requested_brand) {
            $selected_brand = $name;
            break;
        }
    }
    $selected_gender = isset($_GET['gioi-tinh']) && is_string($_GET['gioi-tinh']) ? sanitize_key(wp_unslash($_GET['gioi-tinh'])) : '';
    if (!in_array($selected_gender, ['', 'nam', 'nu'], true)) { $selected_gender = ''; }
    $selected_sort = isset($_GET['sap-xep']) && is_string($_GET['sap-xep']) ? sanitize_key(wp_unslash($_GET['sap-xep'])) : '';
    if (!in_array($selected_sort, ['', 'gia-tang', 'gia-giam'], true)) { $selected_sort = ''; }
    $page_url = get_permalink(get_queried_object_id());
    ob_start(); ?>
    <div class="lw-brands">
      <header class="lw-brands__intro">
        <p class="lw-brands__overline">BỘ SƯU TẬP THAM KHẢO</p>
        <h1>Khám phá đồng hồ<br><em>theo thương hiệu.</em></h1>
        <p>Bốn phong cách, nhiều lựa chọn. Chọn một thương hiệu để xem các mẫu đồng hồ tương ứng.</p>
        <nav class="lw-brands__nav" aria-label="Chọn thương hiệu">
          <?php foreach ($brands as $name => $subtitle) : ?>
            <a href="<?php echo esc_url(add_query_arg('hang', sanitize_title($name), $page_url)); ?>"<?php echo $selected_brand === $name ? ' aria-current="true"' : ''; ?>><?php echo esc_html($name); ?></a>
          <?php endforeach; ?>
        </nav>
        <p class="lw-brands__notice">Giá tham khảo theo nguồn, có thể thay đổi và chưa bao gồm phí vận chuyển hoặc thuế phát sinh. Các mẫu chưa có giá được xếp cuối danh sách. Khi sắp xếp các giá khác loại tiền, tỷ giá tham khảo được ghi nhận ngày 21/10/2026 từ <a href="https://open.er-api.com/v6/latest/USD" target="_blank" rel="noopener noreferrer">ExchangeRate-API</a>.</p>
      </header>
      <?php if (!$selected_brand) : ?>
        <section class="lw-brands__empty" aria-live="polite"><h2>Chọn một thương hiệu</h2><p>Nhấn vào tên hãng phía trên để khám phá các mẫu đồng hồ.</p></section>
      <?php endif; ?>
      <?php foreach ($brands as $name => $subtitle) :
          if ($selected_brand !== $name) { continue; }
          $items = array_values(array_filter($rows, static function ($row) use ($name, $selected_gender) {
              return ($row['brand'] ?? '') === $name && (!$selected_gender || ($row['gender'] ?? '') === $selected_gender);
          }));
          if ($selected_sort) {
              usort($items, static function ($first, $second) use ($selected_sort) {
                  if (empty($first['price_sort_vnd'])) { return empty($second['price_sort_vnd']) ? 0 : 1; }
                  if (empty($second['price_sort_vnd'])) { return -1; }
                  $comparison = ((float) $first['price_sort_vnd']) <=> ((float) $second['price_sort_vnd']);
                  return $selected_sort === 'gia-giam' ? -$comparison : $comparison;
              });
          }
          $base_brand_url = add_query_arg('hang', sanitize_title($name), $page_url); ?>
        <section class="lw-brands__section" id="<?php echo esc_attr(sanitize_title($name)); ?>" aria-labelledby="heading-<?php echo esc_attr(sanitize_title($name)); ?>"><div class="lw-brands__heading"><div><p><?php echo esc_html($subtitle); ?></p><h2 id="heading-<?php echo esc_attr(sanitize_title($name)); ?>"><?php echo esc_html($name); ?></h2></div><span><?php echo esc_html(count($items)); ?> mẫu tuyển chọn</span></div>
        <form class="lw-brands__filters" method="get" aria-label="Lọc mẫu <?php echo esc_attr($name); ?>">
          <input type="hidden" name="hang" value="<?php echo esc_attr(sanitize_title($name)); ?>">
          <div class="lw-brands__filter-field"><label for="lw-gender">Giới tính</label><select id="lw-gender" name="gioi-tinh"><option value="">Tất cả</option><option value="nam"<?php selected($selected_gender, 'nam'); ?>>Đồng hồ nam</option><option value="nu"<?php selected($selected_gender, 'nu'); ?>>Đồng hồ nữ</option></select></div>
          <div class="lw-brands__filter-field"><label for="lw-sort">Sắp xếp giá</label><select id="lw-sort" name="sap-xep"><option value="">Nổi bật</option><option value="gia-tang"<?php selected($selected_sort, 'gia-tang'); ?>>Giá tăng dần</option><option value="gia-giam"<?php selected($selected_sort, 'gia-giam'); ?>>Giá giảm dần</option></select></div>
          <button type="submit">⌕ Lọc</button><a href="<?php echo esc_url($base_brand_url); ?>">Đặt lại</a>
        </form>
        <?php if (!$items) : ?><p class="lw-brands__no-results">Chưa có mẫu phù hợp với bộ lọc này. Hãy chọn lại giới tính hoặc cách sắp xếp.</p><?php else : ?><div class="lw-brands__grid"><?php foreach ($items as $item) : ?><article class="lw-brands__card"><a href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener noreferrer"><div class="lw-brands__image"><img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/brand-catalog/' . basename($item['file'])); ?>" alt="<?php echo esc_attr($item['name']); ?>" width="<?php echo esc_attr($item['width']); ?>" height="<?php echo esc_attr($item['height']); ?>" loading="lazy" decoding="async"></div><div class="lw-brands__details"><span><?php echo esc_html($name); ?><?php if (!empty($item['gender'])) : ?> · <?php echo $item['gender'] === 'nu' ? 'Nữ' : 'Nam'; ?><?php endif; ?></span><h3><?php echo esc_html($item['name']); ?></h3><?php if (!empty($item['price']) && !empty($item['currency'])) : ?><strong class="lw-brands__price">Giá nguồn tham khảo: <?php echo !empty($item['price_from']) ? 'từ ' : ''; echo esc_html(number_format_i18n((float) $item['price'], $item['currency'] === 'USD' ? 2 : 0) . ' ' . $item['currency']); ?></strong><?php if (!empty($item['price_checked'])) : ?><span class="lw-brands__checked">Kiểm tra <?php echo esc_html(date_i18n('d/m/Y', strtotime($item['price_checked']))); ?></span><?php endif; ?><?php else : ?><strong class="lw-brands__price">Giá: xem tại nguồn</strong><?php endif; ?><span class="lw-brands__source">Xem tại nguồn <span aria-hidden="true">↗</span></span></div></a></article><?php endforeach; ?></div><?php endif; ?></section>
      <?php endforeach; ?>
    </div><?php return ob_get_clean();
});

add_action('wp', function () {
    if (is_page('thuong-hieu-dong-ho')) {
        remove_action('storefront_sidebar', 'storefront_get_sidebar', 10);
        remove_action('storefront_page', 'storefront_page_header', 10);
    }
});
