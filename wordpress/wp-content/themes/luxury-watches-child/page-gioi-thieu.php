<?php
/**
 * The editorial About page.
 *
 * @package Luxury_Watches_Atelier
 */

get_header();

$asset_base = get_stylesheet_directory_uri() . '/assets/images/';
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$auction_page = get_page_by_path('dau-gia');
$auction_url = $auction_page ? get_permalink($auction_page) : home_url('/dau-gia/');
$guide_page = get_page_by_path('huong-dan-mua-hang');
$guide_url = $guide_page ? get_permalink($guide_page) : home_url('/huong-dan-mua-hang/');
$contact_page = get_page_by_path('lien-he');
$contact_url = $contact_page ? get_permalink($contact_page) : home_url('/lien-he/');
?>

<main id="primary" class="site-main lw-about">
    <section class="lw-about-hero" aria-labelledby="lw-about-title">
        <div class="lw-wrap lw-about-hero__grid">
            <div class="lw-about-hero__content">
                <h1 id="lw-about-title" class="lw-display">Một không gian để hiểu, chọn và sống cùng thời gian.</h1>
                <p>Luxury Watches kết nối những thiết kế đáng khám phá với kiến thức dễ tiếp cận, để mỗi lựa chọn bắt đầu từ sự thấu hiểu thay vì vội vàng.</p>
                <div class="lw-about-hero__actions">
                    <a class="lw-button" href="<?php echo esc_url($shop_url); ?>">Khám phá bộ sưu tập</a>
                    <a class="lw-about-text-link" href="#cau-chuyen">Đọc câu chuyện của chúng tôi <span aria-hidden="true">↓</span></a>
                </div>
            </div>
            <figure class="lw-about-hero__visual">
                <img src="<?php echo esc_url($asset_base . 'hero-atelier.webp'); ?>" alt="Đồng hồ cơ trên bàn làm việc của nghệ nhân" width="1536" height="1024" fetchpriority="high">
                <figcaption>Vẻ đẹp của một chiếc đồng hồ nằm trong cả thiết kế, chuyển động và câu chuyện người đeo.</figcaption>
            </figure>
        </div>
    </section>

    <section id="cau-chuyen" class="lw-about-manifesto">
        <div class="lw-wrap lw-about-manifesto__grid">
            <h2 class="lw-display">Chúng tôi tin một lựa chọn tốt cần đủ thông tin để trở thành lựa chọn của riêng bạn.</h2>
            <div class="lw-about-manifesto__copy">
                <p>Luxury Watches được xây dựng cho cả người mới tìm hiểu lẫn người đã dành nhiều năm cho nghệ thuật chế tác đồng hồ. Mỗi nội dung hướng đến việc làm rõ điều quan trọng: tỷ lệ trên cổ tay, bộ máy, vật liệu, công năng và giá trị sử dụng thực tế.</p>
                <p>Thay vì đặt mọi chiếc đồng hồ lên cùng một thước đo, chúng tôi giúp bạn nhìn thấy cá tính riêng của từng thiết kế và tìm ra mẫu phù hợp với nhịp sống của mình.</p>
            </div>
        </div>
    </section>

    <section class="lw-about-selection" aria-labelledby="lw-selection-title">
        <div class="lw-wrap lw-about-selection__grid">
            <div class="lw-about-selection__media">
                <img src="<?php echo esc_url($asset_base . 'service-consultation.webp'); ?>" alt="Các mẫu đồng hồ được chuẩn bị cho buổi tư vấn" width="1200" height="900" loading="lazy">
                <p>Chọn bằng hiểu biết.<br>Sở hữu bằng sự tin tưởng.</p>
            </div>
            <div class="lw-about-selection__content">
                <h2 id="lw-selection-title" class="lw-display">Cách chúng tôi xây dựng trải nghiệm.</h2>
                <div class="lw-about-principles">
                    <article>
                        <h3>Thông tin có ngữ cảnh</h3>
                        <p>Thông số kỹ thuật được đặt cạnh nhu cầu sử dụng, giúp bạn hiểu một chi tiết sẽ tạo khác biệt như thế nào khi đeo hằng ngày.</p>
                    </article>
                    <article>
                        <h3>Bộ sưu tập có định hướng</h3>
                        <p>Các thiết kế được sắp xếp theo phong cách, tỷ lệ và trải nghiệm đeo để hành trình khám phá luôn có điểm bắt đầu rõ ràng.</p>
                    </article>
                    <article>
                        <h3>Quy trình minh bạch</h3>
                        <p>Từ tồn kho, hướng dẫn mua hàng đến bước giá trong phiên đấu giá, thông tin cần thiết được trình bày trước khi bạn quyết định.</p>
                    </article>
                </div>
                <a class="lw-about-text-link" href="<?php echo esc_url($guide_url); ?>">Xem cẩm nang lựa chọn <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>

    <section class="lw-about-journey" aria-labelledby="lw-journey-title">
        <div class="lw-wrap">
            <header class="lw-about-journey__head">
                <h2 id="lw-journey-title" class="lw-display">Từ tò mò đến một lựa chọn có ý nghĩa.</h2>
                <p>Một hành trình liền mạch cho người đang bắt đầu, người muốn đối chiếu lựa chọn và người đã sẵn sàng sở hữu.</p>
            </header>
            <ol class="lw-about-journey__steps">
                <li>
                    <span aria-hidden="true">01</span>
                    <div><h3>Khám phá</h3><p>Bắt đầu từ phong cách, kích thước hoặc một câu chuyện chế tác khiến bạn chú ý.</p></div>
                </li>
                <li>
                    <span aria-hidden="true">02</span>
                    <div><h3>Hiểu rõ</h3><p>Đối chiếu bộ máy, vật liệu, tính năng và hoàn cảnh sử dụng trước khi thu hẹp lựa chọn.</p></div>
                </li>
                <li>
                    <span aria-hidden="true">03</span>
                    <div><h3>Quyết định</h3><p>Chọn mua trực tiếp hoặc tham gia phiên đấu giá với quy tắc và bước giá được công khai.</p></div>
                </li>
                <li>
                    <span aria-hidden="true">04</span>
                    <div><h3>Đồng hành</h3><p>Tiếp tục chăm sóc trải nghiệm sở hữu qua hướng dẫn và hỗ trợ sau mua rõ ràng.</p></div>
                </li>
            </ol>
        </div>
    </section>

    <section class="lw-about-auction" aria-labelledby="lw-auction-about-title">
        <div class="lw-wrap lw-about-auction__grid">
            <div class="lw-about-auction__content">
                <h2 id="lw-auction-about-title" class="lw-display">Đấu giá là một cách khác để gặp đúng chiếc đồng hồ.</h2>
                <p>Mỗi phiên hiển thị tình trạng sản phẩm, giá hiện tại, bước giá tối thiểu và thời gian còn lại. Lịch sử trả giá được ghi nhận để người tham gia theo dõi diễn biến rõ ràng.</p>
                <a class="lw-button" href="<?php echo esc_url($auction_url); ?>">Xem các phiên đấu giá</a>
            </div>
            <figure class="lw-about-auction__visual">
                <img src="<?php echo esc_url($asset_base . 'auction-preview.webp'); ?>" alt="Đồng hồ cơ được giới thiệu trong phiên đấu giá" width="1536" height="1024" loading="lazy">
            </figure>
        </div>
    </section>

    <section class="lw-about-craft" aria-labelledby="lw-craft-title">
        <div class="lw-wrap lw-about-craft__grid">
            <div class="lw-about-craft__content">
                <h2 id="lw-craft-title" class="lw-display">Sự an tâm không dừng lại ở khoảnh khắc lựa chọn.</h2>
                <p>Thông tin sản phẩm, hướng dẫn giao nhận và chính sách hỗ trợ được kết nối trong cùng một trải nghiệm. Khi cần trao đổi thêm, bạn luôn có một lối đi rõ ràng để nhận tư vấn.</p>
                <a class="lw-about-text-link" href="<?php echo esc_url($contact_url); ?>">Liên hệ tư vấn <span aria-hidden="true">↗</span></a>
            </div>
            <img src="<?php echo esc_url($asset_base . 'service-authentication.webp'); ?>" alt="Chuyên viên quan sát chi tiết đồng hồ bằng kính lúp" width="1200" height="900" loading="lazy">
            <img src="<?php echo esc_url($asset_base . 'watchmaking-service.webp'); ?>" alt="Bộ máy đồng hồ được kiểm tra tại bàn kỹ thuật" width="1536" height="1024" loading="lazy">
        </div>
    </section>

    <section class="lw-about-closing" aria-labelledby="lw-about-closing-title">
        <div class="lw-wrap lw-about-closing__inner">
            <h2 id="lw-about-closing-title" class="lw-display">Chiếc đồng hồ phù hợp không chỉ cho biết thời gian. Nó kể điều gì đó về cách bạn trân trọng thời gian.</h2>
            <div class="lw-about-closing__actions">
                <a class="lw-button" href="<?php echo esc_url($shop_url); ?>">Bắt đầu khám phá</a>
                <a class="lw-about-text-link" href="<?php echo esc_url($contact_url); ?>">Nhận tư vấn cá nhân</a>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
