<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* =====================================================
 * 1. NAP CSS CHO CHILD THEME
 * ===================================================== */
add_action( 'wp_enqueue_scripts', 'nhom5_enqueue_styles' );
function nhom5_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ), wp_get_theme()->get( 'Version' ) );
}

/* =====================================================
 * 2. SHORTCODE [thong_bao]
 * Cach dung: [thong_bao loai="success" noidung="Noi dung"]
 * Ky thuat an toan: sanitize, validate, escape
 * ===================================================== */
add_shortcode( 'thong_bao', 'nhom5_thong_bao_shortcode' );
function nhom5_thong_bao_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'loai'    => 'info',
        'noidung' => 'Xin chao!',
    ), $atts, 'thong_bao' );

    // [1] SANITIZATION: loc du lieu dau vao
    $noidung = sanitize_text_field( $atts['noidung'] );
    $loai    = sanitize_key( $atts['loai'] );

    // [2] VALIDATION: chi chap nhan gia tri trong danh sach cho phep
    $allowed = array( 'info', 'success', 'warning' );
    if ( ! in_array( $loai, $allowed, true ) ) {
        $loai = 'info';
    }

    // [3] ESCAPING: ma hoa du lieu dau ra, chong XSS
    return '<div class="nhom5-notice nhom5-' . esc_attr( $loai ) . '">'
         . esc_html( $noidung )
         . '</div>';
}

/* =====================================================
 * 3. NUT BACK-TO-TOP (JS tu viet)
 * ===================================================== */
add_action( 'wp_footer', 'nhom5_back_to_top' );
function nhom5_back_to_top() {
    ?>
    <button id="nhom5-btt" type="button"
        aria-label="<?php echo esc_attr__( 'Lên đầu trang', 'astra-child' ); ?>">&#8593;</button>
    <script>
    (function () {
        var btn = document.getElementById('nhom5-btt');
        var body = document.body;
        var ticking = false;
        function onScroll() {
            var y = window.scrollY;
            if (btn) btn.classList.toggle('show', y > 300);
            body.classList.toggle('vu-scrolled', y > 8);
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
        }, { passive: true });
        onScroll();
        if (btn) {
            btn.addEventListener('click', function () {
                var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
            });
        }
    })();
    </script>
    <?php
}

/* =====================================================
 * 3b. HIEN DAN KHI CUON (nhe, chay mot lan)
 * Chi gan class khi trinh duyet ho tro IntersectionObserver
 * va nguoi dung khong bat "giam chuyen dong".
 * ===================================================== */
add_action( 'wp_footer', 'vu_scroll_reveal', 30 );
function vu_scroll_reveal() {
    ?>
    <script>
    (function () {
        if (!('IntersectionObserver' in window)) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        // Nhom phan tu: cac phan tu cung nhom hien lech nhau mot chut (toi da 3 buoc)
        var groups = [
            '.vh-title', '.vh-about > *', '.vh-category', '.vh-step',
            '.vh-cta .vh-container', '.vh-contact > div',
            '.va-intro > *', '.va-grid > .va-card', '.va-cta',
            '.venus-price-page > h2', '.venus-price-table', '.venus-price-note',
            '.venus-price-cta', 'ul.products > li.product', '.tv3-form'
        ];
        var seen = [];
        document.documentElement.classList.add('vu-js');

        groups.forEach(function (sel) {
            var parentIndex = new Map();
            document.querySelectorAll(sel).forEach(function (el) {
                if (seen.indexOf(el) !== -1) return;
                seen.push(el);
                var p = el.parentNode;
                var i = parentIndex.get(p) || 0;
                parentIndex.set(p, i + 1);
                el.style.setProperty('--vu-delay', (Math.min(i % 4, 3) * 80) + 'ms');
                el.classList.add('vu-reveal');
            });
        });

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in');
                    io.unobserve(e.target);
                }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

        seen.forEach(function (el) { io.observe(el); });
    })();
    </script>
    <?php
}

/* =====================================================
 * 4. HARDENING: AN PHIEN BAN WORDPRESS
 * ===================================================== */
remove_action( 'wp_head', 'wp_generator' );
/* =====================================================
 * 5. WOOCOMMERCE CATALOG MODE (chi trung bay, khong ban)
 * ===================================================== */

// An nut "Them vao gio hang"
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// San pham khong the mua
add_filter( 'woocommerce_is_purchasable', '__return_false' );

// Nut "Yeu cau bao gia" tro toi trang Lien he
add_action( 'woocommerce_single_product_summary', 'nhom5_nut_bao_gia', 30 );
function nhom5_nut_bao_gia() {
    $url = esc_url( home_url( '/lien-he/' ) );
    echo '<a class="button nhom5-btn-baogia" href="' . $url . '">'
       . esc_html__( 'Yêu cầu báo giá', 'astra-child' )
       . '</a>';
}

// Gio hang, thanh toan: chuyen ve trang chu
add_action( 'template_redirect', 'nhom5_chan_cart_checkout' );
function nhom5_chan_cart_checkout() {
    if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
        wp_safe_redirect( home_url( '/' ) );
        exit;
    }
}
add_filter( 'woocommerce_get_price_html', 'nhom5_an_gia' );
function nhom5_an_gia( $price ) {
    return esc_html__( 'Liên hệ để nhận báo giá', 'astra-child' );
}

/* =====================================================
 * 6. TV2 - LOAD RIENG FILE GIAO DIEN
 * ===================================================== */
add_action( 'wp_enqueue_scripts', 'tv2_enqueue_giaodien_css', 20 );
function tv2_enqueue_giaodien_css() {
    // Font co ho tro tieng Viet day du: Lora (tieu de), Be Vietnam Pro (noi dung)
    wp_enqueue_style(
        'vu-fonts',
        'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Lora:wght@500;600&display=swap&subset=vietnamese',
        array(),
        null
    );
    wp_enqueue_style(
        'tv2-giaodien',
        get_stylesheet_directory_uri() . '/giaodien.css',
        array( 'child-style', 'vu-fonts' ),
        '1.2.5'
    );
}

add_filter( 'wp_resource_hints', 'vu_font_preconnect', 10, 2 );
function vu_font_preconnect( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
    }
    return $urls;
}

/* =====================================================
   VENUS UNIFORM - FOOTER RIÊNG
===================================================== */

function venus_uniform_custom_footer() {
    ?>
    <footer class="venus-footer">
        <div class="venus-footer-container">

            <div class="venus-footer-column">
                <h2>VENUS UNIFORM</h2>
                <p>
                    Chuyên thiết kế, sản xuất và cung cấp đồng phục
                    chuyên nghiệp cho doanh nghiệp, trường học và tổ chức.
                </p>
            </div>

            <div class="venus-footer-column">
                <h3>DANH MỤC SẢN PHẨM</h3>
                <ul>
                    <li><a href="/dongphuc/cua-hang/">Tất cả sản phẩm</a></li>
                    <li><a href="/dongphuc/cua-hang/">Đồng phục công sở</a></li>
                    <li><a href="/dongphuc/cua-hang/">Đồng phục y tế</a></li>
                    <li><a href="/dongphuc/cua-hang/">Đồng phục nhà hàng</a></li>
                </ul>
            </div>

            <div class="venus-footer-column">
                <h3>HỖ TRỢ KHÁCH HÀNG</h3>
                <ul>
                    <li><a href="/dongphuc/bang-gia/">Bảng giá</a></li>
                    <li><a href="/dongphuc/dat-lich-tu-van/">Đặt lịch tư vấn</a></li>
                    <li><a href="/dongphuc/lien-he/">Liên hệ</a></li>
                </ul>
            </div>

            <div class="venus-footer-column">
                <h3>THÔNG TIN LIÊN HỆ</h3>
                <p>Địa chỉ: Số 10 Phố Vọng, Hai Bà Trưng, Hà Nội</p>
                <p>Hotline: 0988 686 868</p>
                <p>Thời gian: Thứ 2 – Thứ 7, 8:00 – 17:30</p>
            </div>

        </div>

        <div class="venus-footer-bottom">
            © <?php echo esc_html(date('Y')); ?> VENUS UNIFORM.
            All rights reserved.
        </div>
    </footer>
    <?php
}

add_action('astra_footer', 'venus_uniform_custom_footer', 5);


/* CSS cho footer VENUS UNIFORM: da chuyen sang giaodien.css (muc 12) */

//[products limit="12" columns="3" orderby="date" order="DESC"]
/*
<style>
.venus-product-page {
    --navy: #102B46;
    --gold: #C9A45C;
    --light: #F3F5F7;
    color: #333;
    font-family: Arial, sans-serif;
    line-height: 1.7;
}
.venus-product-page * { box-sizing: border-box; }
.venus-product-page .vp-hero {
    background: linear-gradient(120deg, #102B46, #1D4668);
    color: white;
    padding: 85px 25px;
    text-align: center;
    border-radius: 6px;
    margin-bottom: 40px;
}
.venus-product-page .vp-hero .vp-label {
    color: #E3C889;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-size: 13px;
    margin-bottom: 10px;
}
.venus-product-page .vp-hero h1 {
    color: white;
    font-size: 42px;
    margin: 0 0 15px;
}
.venus-product-page .vp-hero p {
    max-width: 750px;
    margin: auto;
    font-size: 18px;
}
@media(max-width:768px) {
    .venus-product-page .vp-hero { padding: 60px 20px; }
    .venus-product-page .vp-hero h1 { font-size: 32px; }
}
</style>
<div class="venus-product-page">
<div class="vp-hero">
<div class="vp-label">VENUS UNIFORM</div>
<h1>Sản phẩm &amp; Giải pháp đồng phục</h1>
<p>Khám phá bộ sưu tập đồng phục chuyên nghiệp cho doanh nghiệp, trường học và tổ chức, thiết kế theo nhận diện thương hiệu của bạn.</p>
</div>
</div>
*/

/* =====================================================
 * 7. TRANG CUA HANG / DANH MUC
 * Dau trang gon + thanh loc danh muc; CSS nam trong giaodien.css (muc 10).
 * ===================================================== */

// Tieu de H1 nam trong dau trang rieng, an tieu de mac dinh de khong bi lap
add_filter( 'woocommerce_show_page_title', function ( $show ) {
    return ( is_shop() || is_product_category() ) ? false : $show;
} );

// Hien thi du san pham tren mot trang
add_filter( 'loop_shop_per_page', function () { return 24; }, 30 );

add_action( 'woocommerce_archive_description', 'venus_product_page_header', 5 );
function venus_product_page_header() {
    if ( ! is_shop() && ! is_product_category() ) {
        return;
    }
    $current = is_product_category() ? get_queried_object() : null;
    $title   = $current ? $current->name : 'Sản phẩm & giải pháp đồng phục';
    $desc    = $current && $current->description
        ? $current->description
        : 'Đồng phục may theo nhận diện thương hiệu cho doanh nghiệp, trường học và tổ chức. Chọn mẫu và gửi yêu cầu để nhận báo giá.';
    $cats    = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'orderby'    => 'term_id',
        'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
    ) );
    $shop_url = get_permalink( wc_get_page_id( 'shop' ) );
    $total    = (int) wp_count_posts( 'product' )->publish;
    ?>
    <div class="venus-product-page">
        <div class="vp-hero">
            <div class="vp-label">Venus Uniform · Bộ sưu tập</div>
            <h1><?php echo esc_html( $title ); ?></h1>
            <p><?php echo esc_html( wp_strip_all_tags( $desc ) ); ?></p>
        </div>
        <?php if ( ! is_wp_error( $cats ) && $cats ) : ?>
        <nav class="vp-cats" aria-label="<?php echo esc_attr__( 'Danh mục sản phẩm', 'astra-child' ); ?>">
            <a href="<?php echo esc_url( $shop_url ); ?>"<?php echo $current ? '' : ' class="is-active" aria-current="page"'; ?>>
                Tất cả <span><?php echo esc_html( $total ); ?></span>
            </a>
            <?php foreach ( $cats as $cat ) :
                $active = $current && (int) $current->term_id === (int) $cat->term_id; ?>
                <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"<?php echo $active ? ' class="is-active" aria-current="page"' : ''; ?>>
                    <?php echo esc_html( $cat->name ); ?> <span><?php echo esc_html( $cat->count ); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>
    </div>
    <?php
}

/* =====================================================
 * 8. THE SAN PHAM: nhan "Noi bat", dong MOQ, chu nut
 * ===================================================== */
add_action( 'woocommerce_before_shop_loop_item_title', 'vu_nhan_noi_bat', 9 );
function vu_nhan_noi_bat() {
    global $product;
    if ( $product && $product->is_featured() ) {
        echo '<span class="vu-badge">' . esc_html__( 'Nổi bật', 'astra-child' ) . '</span>';
    }
}

// Astra dua tieu de ra ngoai khung anh; hook mac dinh cua Woo lai nam trong link anh
add_action( 'astra_woo_shop_price_after', 'vu_dong_moq', 15 );
function vu_dong_moq() {
    global $product;
    if ( ! $product ) {
        return;
    }
    $moq  = absint( $product->get_meta( 'moq' ) );
    $ngay = absint( $product->get_meta( 'thoi_gian_san_xuat' ) );
    if ( ! $moq && ! $ngay ) {
        return;
    }
    $parts = array();
    if ( $moq )  { $parts[] = sprintf( 'Tối thiểu %d bộ', $moq ); }
    if ( $ngay ) { $parts[] = sprintf( 'May %d ngày', $ngay ); }
    echo '<p class="vu-moq">' . esc_html( implode( ' · ', $parts ) ) . '</p>';
}

add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) {
    return $product && ! $product->is_purchasable() ? __( 'Xem chi tiết', 'astra-child' ) : $text;
}, 10, 2 );

/* =====================================================
 * 9. TRANG CHI TIET: khoi thong tin dat may
 * ===================================================== */
add_action( 'woocommerce_single_product_summary', 'vu_khoi_dat_may', 25 );
function vu_khoi_dat_may() {
    global $product;
    $moq  = absint( $product->get_meta( 'moq' ) );
    $ngay = absint( $product->get_meta( 'thoi_gian_san_xuat' ) );
    $gia  = absint( $product->get_meta( 'gia_500_tro_len' ) );
    if ( ! $moq && ! $ngay && ! $gia ) {
        return;
    }
    ?>
    <dl class="vu-order-facts">
        <?php if ( $moq ) : ?><div><dt>Đặt tối thiểu</dt><dd><?php echo esc_html( $moq ); ?> bộ</dd></div><?php endif; ?>
        <?php if ( $ngay ) : ?><div><dt>Thời gian may</dt><dd><?php echo esc_html( $ngay ); ?> ngày</dd></div><?php endif; ?>
        <?php if ( $gia ) : ?><div><dt>Giá tham khảo từ</dt><dd><?php echo esc_html( number_format( $gia, 0, ',', '.' ) ); ?>đ</dd></div><?php endif; ?>
    </dl>
    <?php
}
