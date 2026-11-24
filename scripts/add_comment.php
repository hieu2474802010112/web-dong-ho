<?php
require_once __DIR__ . '/../wordpress/wp-load.php';
$comment_id = wp_insert_comment([
    'comment_post_ID' => 264,
    'comment_author' => 'Trần Bảo Ngọc (Nhà sưu tầm)',
    'comment_author_email' => 'ngoc.tran@heritage-watch.vn',
    'comment_content' => 'Bài viết phân tích cơ chế Tourbillon rất chuyên sâu và dễ hiểu! Độ chính xác và tay nghề hoàn thiện thủ công của Luxury Watches Atelier thật sự đẳng cấp.',
    'comment_type' => 'comment',
    'comment_approved' => 1,
    'comment_date' => current_time('mysql')
]);
echo "Inserted approved comment ID: $comment_id\n";

