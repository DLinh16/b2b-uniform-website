<?php
/**
 * Them san pham moi + gan anh 4:5 da cat san cho toan bo san pham.
 * Chay SAU tools/cat-anh-san-pham.ps1.
 *
 *   C:\xampp\php\php.exe tools\seed-san-pham.php [C:\xampp\htdocs\dongphuc]
 *
 * Chay lai nhieu lan van an toan: san pham tim theo SKU, anh tim theo ten file.
 */

$site = isset( $argv[1] ) ? rtrim( $argv[1], '\\/' ) : 'C:/xampp/htdocs/dongphuc';
$_SERVER['HTTP_HOST']   = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require $site . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/* ---------- 1. Cai dat kich thuoc anh WooCommerce: 4:5, rong 600 ---------- */
update_option( 'woocommerce_thumbnail_image_width', 600 );
update_option( 'woocommerce_thumbnail_cropping', 'custom' );
update_option( 'woocommerce_thumbnail_cropping_custom_width', 4 );
update_option( 'woocommerce_thumbnail_cropping_custom_height', 5 );

/* ---------- 2. Danh muc moi ---------- */
if ( ! term_exists( 'dong-phuc-hoc-sinh', 'product_cat' ) ) {
    wp_insert_term( 'Đồng phục học sinh', 'product_cat', array( 'slug' => 'dong-phuc-hoc-sinh' ) );
}

/* ---------- 3. San pham moi ---------- */
function vu_mo_ta( $intro, $bullets, $chat_lieu, $mau, $size, $moq, $ngay, $g1, $g2, $g3 ) {
    $f = function ( $n ) { return number_format( $n, 0, ',', '.' ) . 'đ'; };
    $li = '';
    foreach ( $bullets as $b ) { $li .= '<li>' . $b . '</li>'; }
    return "<p>$intro</p><ul>$li</ul><h4>Thông số</h4><ul>"
        . "<li><strong>Chất liệu:</strong> $chat_lieu</li><li><strong>Màu sắc:</strong> $mau</li>"
        . "<li><strong>Size:</strong> $size</li><li><strong>Số lượng đặt tối thiểu:</strong> $moq bộ</li>"
        . "<li><strong>Thời gian sản xuất:</strong> $ngay ngày làm việc</li></ul>"
        . '<h4>Bảng giá theo số lượng (VNĐ/bộ)</h4><table><thead><tr><th>Số lượng</th><th>Đơn giá</th></tr></thead><tbody>'
        . '<tr><td>50 - 99 bộ</td><td>' . $f( $g1 ) . '</td></tr><tr><td>100 - 499 bộ</td><td>' . $f( $g2 ) . '</td></tr>'
        . '<tr><td>Từ 500 bộ</td><td>' . $f( $g3 ) . '</td></tr></tbody></table>'
        . '<p><em>Giá tham khảo, chưa gồm chi phí thêu/in logo. Liên hệ để nhận báo giá chính thức.</em></p>';
}

$moi = array(
    array( 'CS-003', 'Sơ mi công sở nam nữ phối cà vạt', 'dong-phuc-cong-so', 1,
        'Bộ sơ mi trắng đồng bộ cho cả nam và nữ, phối cà vạt và chân váy theo nhận diện doanh nghiệp.',
        array( 'Thêu logo ngực trái, đồng bộ cho cả phòng ban', 'Nữ phối chân váy bút chì, nam phối quần âu', 'Phù hợp showroom, ngân hàng, văn phòng đại diện' ),
        array( 'Cotton lụa', 'chống nhăn' ), array( 'Trắng', 'Xanh nhạt' ), array( 'S', 'M', 'L', 'XL' ), 50, 15, 245000, 225000, 205000 ),
    array( 'PL-003', 'Áo thun oversize in logo', 'ao-polo-ao-thun', 1,
        'Áo thun dáng rộng trẻ trung, hợp đồng phục nhóm, CLB và sự kiện nội bộ.',
        array( 'Form oversize unisex, vai rơi', 'In lụa hoặc in DTF logo ngực, lưng', 'Vải dày, không lộ, giữ form sau giặt' ),
        array( 'Cotton 100% định lượng 250gsm' ), array( 'Kem', 'Trắng', 'Đen' ), array( 'S', 'M', 'L', 'XL', 'XXL' ), 50, 7, 115000, 105000, 95000 ),
    array( 'YT-003', 'Bộ scrub điều dưỡng', 'dong-phuc-y-te', 0,
        'Bộ scrub cổ tim cho điều dưỡng và kỹ thuật viên, co giãn nhẹ, dễ vận động cả ca trực.',
        array( 'Áo 3 túi, quần lưng thun có dây rút', 'Vải kháng khuẩn, khô nhanh', 'Phân biệt khoa phòng bằng màu sắc' ),
        array( 'Kaki thun kháng khuẩn' ), array( 'Xanh ngọc', 'Xanh navy', 'Xanh lá' ), array( 'S', 'M', 'L', 'XL' ), 30, 12, 210000, 195000, 180000 ),
    array( 'NH-003', 'Áo lễ tân - spa cổ tàu', 'dong-phuc-nha-hang-khach-san', 0,
        'Áo dáng dài cổ tàu cách tân cho lễ tân, spa và nhà hàng phong cách Á Đông.',
        array( 'Cổ tàu phối viền màu đậm', 'Nữ dáng áo dài cách tân, nam dáng áo đứng', 'Thêu logo ngực trái bằng chỉ ánh kim' ),
        array( 'Vải lụa Hàn', 'không nhăn' ), array( 'Xanh rêu', 'Đỏ đô', 'Be' ), array( 'S', 'M', 'L', 'XL' ), 30, 18, 360000, 335000, 310000 ),
    array( 'BH-003', 'Bộ bảo hộ kỹ sư phối màu', 'bao-ho-lao-dong', 1,
        'Bộ bảo hộ phối màu vai cho kỹ sư công trình và nhà máy, nhận diện từng đội bằng màu phối.',
        array( 'Túi hộp hai bên, khóa kéo nẹp che', 'Phối màu vai và cổ tay theo yêu cầu', 'Có thể gắn thêm dải phản quang' ),
        array( 'Kaki Nam Định 65/35' ), array( 'Be phối xanh', 'Ghi phối cam', 'Xanh than' ), array( 'M', 'L', 'XL', 'XXL' ), 50, 15, 265000, 245000, 225000 ),
    array( 'HS-001', 'Áo polo học sinh tiểu học', 'dong-phuc-hoc-sinh', 1,
        'Áo polo học sinh phối viền cổ, đi kèm quần short hoặc chân váy kẻ caro.',
        array( 'Bo cổ và tay phối viền đỏ', 'Thêu logo trường ngực trái', 'Vải mềm, thấm hút, an toàn cho trẻ' ),
        array( 'Cotton cá sấu co giãn' ), array( 'Xanh navy', 'Trắng' ), array( '4', '6', '8', '10', '12' ), 100, 14, 135000, 120000, 110000 ),
);

function vu_gan_thuoc_tinh( $product, $values ) {
    $attrs = array();
    $i     = 0;
    foreach ( array( 'pa_chat-lieu' => $values[0], 'pa_mau-sac' => $values[1], 'pa_size' => $values[2] ) as $tax => $terms ) {
        $ids = array();
        foreach ( $terms as $t ) {
            $found = term_exists( $t, $tax );
            if ( ! $found ) { $found = wp_insert_term( $t, $tax ); }
            if ( ! is_wp_error( $found ) ) { $ids[] = (int) $found['term_id']; }
        }
        $a = new WC_Product_Attribute();
        $a->set_id( wc_attribute_taxonomy_id_by_name( $tax ) );
        $a->set_name( $tax );
        $a->set_options( $ids );
        $a->set_position( $i++ );
        $a->set_visible( true );
        $attrs[] = $a;
    }
    $product->set_attributes( $attrs );
}

foreach ( $moi as $m ) {
    list( $sku, $ten, $cat, $noi_bat, $intro, $bullets, $cl, $mau, $size, $moq, $ngay, $g1, $g2, $g3 ) = $m;
    $id = wc_get_product_id_by_sku( $sku );
    $p  = $id ? wc_get_product( $id ) : new WC_Product_Simple();
    $p->set_name( $ten );
    $p->set_sku( $sku );
    $p->set_status( 'publish' );
    $p->set_featured( (bool) $noi_bat );
    $p->set_regular_price( $g1 );
    $p->set_short_description( $intro );
    $p->set_description( vu_mo_ta( $intro, $bullets, implode( ', ', $cl ), implode( ', ', $mau ), implode( ', ', $size ), $moq, $ngay, $g1, $g2, $g3 ) );
    $term = get_term_by( 'slug', $cat, 'product_cat' );
    $p->set_category_ids( array( $term->term_id ) );
    vu_gan_thuoc_tinh( $p, array( $cl, $mau, $size ) );
    foreach ( array( 'moq' => $moq, 'thoi_gian_san_xuat' => $ngay, 'gia_50_99' => $g1, 'gia_100_499' => $g2, 'gia_500_tro_len' => $g3 ) as $k => $v ) {
        $p->update_meta_data( $k, $v );
    }
    $p->save();
    echo ( $id ? 'Cap nhat ' : 'Them moi ' ), $sku, ' #', $p->get_id(), "\n";
}

/* ---------- 4. Gan anh 4:5 cho moi san pham ---------- */
$dir = wp_upload_dir();
foreach ( wc_get_products( array( 'limit' => -1, 'status' => 'publish' ) ) as $p ) {
    $sku  = $p->get_sku();
    $rel  = 'vu-sanpham/' . $sku . '.jpg';
    $full = $dir['basedir'] . '/' . $rel;
    if ( ! $sku || ! file_exists( $full ) ) { echo "Bo qua $sku (khong co anh)\n"; continue; }

    $att = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_wp_attached_file', 'meta_value' => $rel, 'fields' => 'ids', 'numberposts' => 1 ) );
    $att_id = $att ? $att[0] : wp_insert_attachment( array(
        'post_mime_type' => 'image/jpeg',
        'post_title'     => $p->get_name(),
        'post_status'    => 'inherit',
    ), $full, $p->get_id() );
    update_post_meta( $att_id, '_wp_attachment_image_alt', $p->get_name() );

    // PHP tren XAMPP khong co GD nen tu khai bao metadata cho cac kich thuoc da cat san
    list( $w, $h ) = getimagesize( $full );
    $thumb = array( 'file' => $sku . '-600x750.jpg', 'width' => 600, 'height' => 750, 'mime-type' => 'image/jpeg' );
    wp_update_attachment_metadata( $att_id, array(
        'width'  => $w,
        'height' => $h,
        'file'   => $rel,
        'sizes'  => array(
            'woocommerce_thumbnail' => $thumb,
            'medium'                => $thumb,
            'shop_catalog'          => $thumb,
        ),
        'image_meta' => array(),
    ) );
    $p->set_image_id( $att_id );
    $p->save();
    echo "Anh $sku -> #$att_id ({$w}x{$h})\n";
}

wc_delete_product_transients();
echo "Xong.\n";
