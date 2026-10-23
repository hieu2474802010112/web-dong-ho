<?php
get_header();

$products = function_exists('wc_get_products') ? wc_get_products(['status' => 'publish', 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC']) : [];
$asset_base = get_stylesheet_directory_uri() . '/assets/images/';
$hero_image = $asset_base . 'hero-atelier.webp';
$auction_image = $asset_base . 'auction-preview.webp';
$care_image = $asset_base . 'watchmaking-service.webp';
$journal_fallback = $asset_base . 'journal-collector.webp';
$service_consultation = $asset_base . 'service-consultation.webp';
$service_authentication = $asset_base . 'service-authentication.webp';
$service_handover = $asset_base . 'service-handover.webp';
$posts = get_posts(['post_status' => 'publish', 'numberposts' => 2, 'orderby' => 'date']);
$auction_page = get_page_by_path('dau-gia');
$auction_url = $auction_page ? get_permalink($auction_page) : home_url('/dau-gia/');
$men_category = get_term_by('slug', 'dong-ho-nam', 'product_cat');
$women_category = get_term_by('slug', 'dong-ho-nu', 'product_cat');
$new_category = get_term_by('slug', 'bo-suu-tap-moi-2026', 'product_cat');
$men_url = $men_category ? get_term_link($men_category) : wc_get_page_permalink('shop');
$women_url = $women_category ? get_term_link($women_category) : wc_get_page_permalink('shop');
$new_url = $new_category ? get_term_link($new_category) : wc_get_page_permalink('shop');
$buying_guide_url = ($page = get_page_by_path('huong-dan-mua-hang')) ? get_permalink($page) : home_url('/huong-dan-mua-hang/');
$auction_guide_url = ($page = get_page_by_path('huong-dan-dau-gia')) ? get_permalink($page) : home_url('/huong-dan-dau-gia/');
$faq_url = ($page = get_page_by_path('cau-hoi-thuong-gap')) ? get_permalink($page) : home_url('/cau-hoi-thuong-gap/');
?>

<main id="primary" class="site-main">
  <section class="lw-hero" style="--lw-hero-image:url('<?php echo esc_url($hero_image); ?>')">
    <div class="lw-wrap lw-hero__content">
      <h1 class="lw-display">Thời gian, được tuyển chọn như một di sản.</h1>
      <p>Khám phá đồng hồ chính hãng, câu chuyện chế tác và những phiên đấu giá minh bạch dành cho người yêu vẻ đẹp trường tồn.</p>
      <div class="lw-actions">
        <a class="lw-button" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Khám phá bộ sưu tập</a>
        <a class="lw-button lw-button--ghost" href="<?php echo esc_url($auction_url); ?>">Xem phiên đấu giá</a>
      </div>
    </div>
  </section>

  <div class="lw-trust">
    <div class="lw-wrap lw-trust__grid">
      <div class="lw-trust__item"><strong>Cam kết chính hãng</strong><span>Thông tin xuất xứ minh bạch</span></div>
      <div class="lw-trust__item"><strong>Bảo hành tận tâm</strong><span>Hỗ trợ sau mua rõ ràng</span></div>
      <div class="lw-trust__item"><strong>Đấu giá công khai</strong><span>Lưu lịch sử và bước giá hợp lệ</span></div>
    </div>
  </div>

  <section class="lw-section lw-section--cream">
    <div class="lw-wrap">
      <header class="lw-section__head">
        <div><p class="lw-eyebrow">20 thiết kế vừa ra mắt</p><h2 class="lw-display">Bộ sưu tập mới 2026</h2></div>
        <div class="lw-section__aside"><p>Từ đồng hồ công cụ đến những thiết kế trang sức, mỗi mẫu có hình ảnh và cá tính riêng.</p><a href="<?php echo esc_url($new_url); ?>">Xem toàn bộ bộ sưu tập →</a></div>
      </header>
      <div class="lw-products"><?php echo do_shortcode('[products category="bo-suu-tap-moi-2026" limit="8" columns="4" orderby="date" order="DESC"]'); ?></div>
    </div>
  </section>

  <section class="lw-section lw-section--gender">
    <div class="lw-wrap">
      <header class="lw-section__head">
        <h2 class="lw-display">Chọn đồng hồ theo phong cách</h2>
        <p>Hai bộ sưu tập được sắp xếp theo tỷ lệ cổ tay, ngôn ngữ thiết kế và nhu cầu sử dụng.</p>
      </header>
      <div class="lw-gender-grid">
        <article class="lw-gender-panel lw-gender-panel--men">
          <header class="lw-gender-panel__head">
            <div><span>Bộ sưu tập</span><h2 class="lw-display">Đồng hồ nam</h2></div>
            <a href="<?php echo esc_url($men_url); ?>">Xem tất cả</a>
          </header>
          <div class="lw-products"><?php echo do_shortcode('[products category="dong-ho-nam" limit="4" columns="2" orderby="date" order="DESC"]'); ?></div>
        </article>
        <article class="lw-gender-panel lw-gender-panel--women">
          <header class="lw-gender-panel__head">
            <div><span>Bộ sưu tập</span><h2 class="lw-display">Đồng hồ nữ</h2></div>
            <a href="<?php echo esc_url($women_url); ?>">Xem tất cả</a>
          </header>
          <div class="lw-products"><?php echo do_shortcode('[products category="dong-ho-nu" limit="4" columns="2" orderby="date" order="DESC"]'); ?></div>
        </article>
      </div>
    </div>
  </section>

  <section class="lw-section lw-section--guide">
    <div class="lw-wrap">
      <header class="lw-section__head">
        <div><p class="lw-eyebrow">Cẩm nang lựa chọn</p><h2 class="lw-display">Hiểu chiếc đồng hồ trước khi sở hữu.</h2></div>
        <div class="lw-section__aside"><p>Ba yếu tố giúp thu hẹp lựa chọn nhanh hơn: tỷ lệ cổ tay, hoàn cảnh sử dụng và loại bộ máy.</p><a href="<?php echo esc_url($buying_guide_url); ?>">Đọc hướng dẫn mua hàng →</a></div>
      </header>
      <div class="lw-guide-grid">
        <article class="lw-guide-card"><span class="lw-guide-card__index">01</span><p class="lw-eyebrow">Tỷ lệ</p><h3>Kích thước vừa cổ tay</h3><p><strong>29–34 mm</strong> thanh mảnh, <strong>38–40 mm</strong> cân bằng, còn <strong>41–42 mm</strong> tạo cảm giác thể thao và nổi bật.</p></article>
        <article class="lw-guide-card"><span class="lw-guide-card__index">02</span><p class="lw-eyebrow">Công năng</p><h3>Phù hợp hoàn cảnh</h3><p>Dress watch cho trang phục lịch sự; GMT cho người di chuyển; chronograph và diver dành cho phong cách năng động.</p></article>
        <article class="lw-guide-card"><span class="lw-guide-card__index">03</span><p class="lw-eyebrow">Chuyển động</p><h3>Chọn bộ máy và chức năng</h3><p>Máy cơ tự động giàu cảm xúc; moonphase, worldtimer và open-heart tạo thêm chiều sâu cho trải nghiệm đeo.</p></article>
      </div>
    </div>
  </section>

  <section class="lw-section lw-section--dark">
    <div class="lw-wrap">
      <div class="lw-auction-feature" style="--lw-auction-image:url('<?php echo esc_url($auction_image); ?>')">
        <div class="lw-auction-feature__visual" aria-hidden="true"></div>
        <div class="lw-auction-feature__content">
          <h2 class="lw-display">Sở hữu chiếc đồng hồ bằng giá bạn lựa chọn.</h2>
          <p>Phiên đấu giá ghi nhận từng lượt trả giá, áp dụng bước giá tối thiểu và tự động khóa khi hết thời gian.</p>
          <div class="lw-auction-meta">
            <div><span>Cơ chế</span><strong>Trả giá tăng dần</strong></div>
            <div><span>Bảo vệ</span><strong>Đăng nhập &amp; xác thực</strong></div>
          </div>
          <div><a class="lw-button" href="<?php echo esc_url($auction_url); ?>">Tham gia đấu giá</a></div>
        </div>
      </div>
      <div class="lw-auction-steps" aria-label="Quy trình tham gia đấu giá">
        <article><span>01</span><h3>Đăng nhập</h3><p>Xác thực tài khoản để lưu lịch sử trả giá.</p></article>
        <article><span>02</span><h3>Chọn phiên</h3><p>Đọc thông số, giá hiện tại và thời gian còn lại.</p></article>
        <article><span>03</span><h3>Đặt giá</h3><p>Nhập mức giá đáp ứng bước tăng tối thiểu.</p></article>
        <article><span>04</span><h3>Theo dõi</h3><p>Kiểm tra lịch sử đến khi phiên tự động đóng.</p></article>
      </div>
      <div class="lw-auction-guide-link"><a href="<?php echo esc_url($auction_guide_url); ?>">Xem hướng dẫn đấu giá chi tiết →</a></div>
    </div>
  </section>

  <?php if ($posts) : ?>
  <section class="lw-section">
    <div class="lw-wrap">
      <header class="lw-section__head">
        <h2 class="lw-display">Kiến thức dành cho người yêu đồng hồ</h2>
        <p>Xu hướng, thương hiệu và hướng dẫn chăm sóc đồng hồ được biên soạn dễ hiểu.</p>
      </header>
      <div class="lw-journal">
        <?php foreach ($posts as $index => $post) : setup_postdata($post); $image = get_the_post_thumbnail_url($post, 'large') ?: $journal_fallback; ?>
          <article class="lw-story" style="background-image:url('<?php echo esc_url($image); ?>')">
            <div class="lw-story__content">
              <h3><?php echo esc_html(get_the_title($post)); ?></h3>
              <a href="<?php echo esc_url(get_permalink($post)); ?>">Đọc bài viết</a>
            </div>
          </article>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="lw-section lw-section--cream">
    <div class="lw-wrap">
      <div class="lw-care-feature">
        <div class="lw-care-feature__content">
          <h2 class="lw-display">Sự an tâm tiếp tục sau khi bạn nhận đồng hồ.</h2>
          <p>Mỗi sản phẩm được kiểm tra ngoại hình và chức năng trước khi bàn giao. Thông tin bảo hành được lưu rõ ràng để việc hỗ trợ sau mua luôn thuận tiện.</p>
          <div><a class="lw-button" href="<?php echo esc_url(get_permalink(14)); ?>">Xem chính sách bảo hành</a></div>
        </div>
        <img class="lw-care-feature__image" src="<?php echo esc_url($care_image); ?>" alt="Kiểm tra bộ máy đồng hồ tại bàn kỹ thuật" loading="lazy" width="1536" height="1024">
      </div>
      <header class="lw-section__head"><h2 class="lw-display">Dịch vụ sau mỗi lựa chọn</h2></header>
      <div class="lw-service-grid">
        <article class="lw-service">
          <img src="<?php echo esc_url($service_consultation); ?>" alt="Khay đồng hồ và dụng cụ tư vấn kích thước cổ tay" loading="lazy" width="1200" height="900">
          <div class="lw-service__content"><h3>Tư vấn đúng nhu cầu</h3><p>Chọn kích thước, bộ máy và phong cách phù hợp cổ tay cùng hoàn cảnh sử dụng.</p></div>
        </article>
        <article class="lw-service">
          <img src="<?php echo esc_url($service_authentication); ?>" alt="Chuyên viên kiểm định đồng hồ bằng kính lúp" loading="lazy" width="1200" height="900">
          <div class="lw-service__content"><h3>Kiểm tra trước giao nhận</h3><p>Đối chiếu ngoại hình, chức năng và thông tin sản phẩm trước khi bàn giao.</p></div>
        </article>
        <article class="lw-service">
          <img src="<?php echo esc_url($service_handover); ?>" alt="Đồng hồ trong hộp da xanh chuẩn bị bàn giao" loading="lazy" width="1200" height="900">
          <div class="lw-service__content"><h3>Hỗ trợ sau mua</h3><p>Theo dõi bảo hành và tiếp nhận yêu cầu ngay trong tài khoản khách hàng.</p></div>
        </article>
      </div>
    </div>
  </section>

  <section class="lw-section lw-section--faq">
    <div class="lw-wrap lw-faq-shell">
      <header>
        <p class="lw-eyebrow">Thông tin cần biết</p>
        <h2 class="lw-display">Giải đáp trước khi bạn quyết định.</h2>
        <p>Từ chọn kích thước đến đấu giá và bảo hành, các câu trả lời quan trọng đều được trình bày rõ ràng.</p>
        <a class="lw-button" href="<?php echo esc_url($faq_url); ?>">Xem tất cả câu hỏi</a>
      </header>
      <div class="lw-faq-list">
        <details open><summary>Làm sao chọn kích thước đồng hồ phù hợp?</summary><p>Hãy dựa vào chu vi cổ tay, chiều dài vấu và phong cách mong muốn. Kích thước 38–40 mm thường mang lại tỷ lệ cân bằng.</p></details>
        <details><summary>Thông tin tồn kho nằm ở đâu?</summary><p>Trạng thái tồn kho được hiển thị trên trang chi tiết. Mẫu giới hạn và sản phẩm đấu giá có thể chỉ còn một chiếc.</p></details>
        <details><summary>Tôi cần làm gì để tham gia đấu giá?</summary><p>Đăng nhập, chọn phiên còn thời gian, đọc bước giá tối thiểu và nhập mức giá hợp lệ.</p></details>
        <details><summary>Sản phẩm được kiểm tra trước khi giao không?</summary><p>Mỗi sản phẩm được đối chiếu ngoại hình, chức năng cơ bản và phụ kiện trước khi đóng gói.</p></details>
      </div>
    </div>
  </section>
</main>

<?php get_footer();
